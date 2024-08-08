<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Producto;
use App\Models\OrdenCompra;
use App\Models\DetalleOrdenCompra;
use App\Models\Proveedore;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenCompraController extends Controller
{
    public function index()
    {
        $ordenes = OrdenCompra::with(['proveedor', 'detalles.producto', 'detalles.inventario'])->get();
        return view('ordenes.index', compact('ordenes'));
    }

    public function create()
    {
        $proveedores = Proveedore::all();
        $inventarios = Inventario::with(['producto', 'sucursal', 'bodega'])->get();
        $productos = Producto::with('inventarios.sucursal')->get(); // Asumiendo que cada producto tiene inventarios relacionados con sucursales
        $ultimoNumeroOrden = OrdenCompra::max('numero_orden');
        $nuevoNumeroOrden = $ultimoNumeroOrden ? $ultimoNumeroOrden + 1 : 1;


        return view('ordenes.create', compact('proveedores', 'productos', 'inventarios', 'nuevoNumeroOrden'));
    }

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'proveedor_id' => 'required|exists:proveedores,id',
        'detalles' => 'required|array',
        'detalles.*.producto_id' => 'required|exists:productos,id',
        'detalles.*.cantidad' => 'required|integer|min:1',
        'detalles.*.precio_compra' => 'required|numeric',
        'total' => 'required|numeric'
    ]);

    DB::transaction(function () use ($validatedData, $request) {
        $ultimoNumeroOrden = OrdenCompra::max('numero_orden');
        $nuevoNumeroOrden = $ultimoNumeroOrden ? $ultimoNumeroOrden + 1 : 1;

        // Crear la orden de compra
        $ordenCompra = OrdenCompra::create([
            'numero_orden' => $nuevoNumeroOrden,
            'proveedor_id' => $validatedData['proveedor_id'],
            'estado' => 'solicitado', // Estado inicial
            'total' => $validatedData['total'],
        ]);

        foreach ($request->detalles as $detalle) {
            $ordenCompra->detalles()->create([
                'producto_id' => $detalle['producto_id'],
                'cantidad' => $detalle['cantidad'],
                'precio_compra' => $detalle['precio_compra'],
            ]);
        }
    });

    return redirect()->route('ordenes.index')->with('success', 'Orden de compra creada con éxito.');
}

public function checkProductoEnBodegaGeneral($productoId)
    {
        $bodegaGeneral = Bodega::where('nombre', 'Bodega General')->first();
        $inventario = Inventario::where('producto_id', $productoId)
            ->where('bodega_id', $bodegaGeneral->id)
            ->first();

        if ($inventario) {
            return response()->json(['exists' => true, 'cantidad' => $inventario->cantidad]);
        } else {
            return response()->json(['exists' => false, 'cantidad' => 0]);
        }
    }

    public function show($id)
    {
        $ordenCompra = OrdenCompra::with(['proveedor', 'detalles.producto', 'detalles.inventario.sucursal'])->findOrFail($id);
        return view('ordenes.show', compact('ordenCompra'));
    }

    public function edit(OrdenCompra $orden)
    {
        $orden->load(['proveedor', 'detalles.producto', 'detalles.inventario']);
        return view('ordenes.edit', compact('orden'));
    }

    public function update(Request $request, OrdenCompra $ordenCompra)
{
    $validatedData = $request->validate([
        'estado' => 'required|in:solicitado,entregado,cancelado',
        // Otros campos de validación...
    ]);

    $ordenCompra->update($validatedData);

    return redirect()->route('ordenes.index')->with('success', 'Orden de compra actualizada con éxito.');
}

    public function destroy(OrdenCompra $orden)
    {
        $orden->delete();
        return redirect()->route('ordenes.index')->with('success', 'Orden de compra eliminada con éxito.');
    }

    public function entregar($id)
    {
        $ordenCompra = OrdenCompra::with(['guiasDespacho', 'detalles.producto'])->findOrFail($id);
        
        if ($ordenCompra->estado == 'solicitado') {
            DB::beginTransaction();
            try {
                $ordenCompra->update(['estado' => 'entregado']);
                \Log::info("Orden de compra ID {$ordenCompra->id} marcada como entregada.");

                // Actualizar el estado de la guía de despacho
                foreach ($ordenCompra->guiasDespacho as $guia) {
                    $guia->update(['estado' => 'entregada']);
                    \Log::info("Guía de despacho ID {$guia->id} actualizada a 'Entregada'.");
                }

                // Actualizar el inventario y registrar movimientos
                foreach ($ordenCompra->detalles as $detalle) {
                    // Buscar el inventario en la bodega general
                    $inventario = Inventario::where('producto_id', $detalle->producto_id)
                                            ->where('bodega_id', 1) // Asumiendo que '1' es el ID de la bodega general
                                            ->first();
                    
                    if ($inventario) {
                        $inventario->increment('cantidad', $detalle->cantidad);
                        \Log::info("Inventario actualizado: Producto ID {$inventario->producto_id}, Bodega ID {$inventario->bodega_id}, Añadidas {$detalle->cantidad} unidades.");

                        // Registrar movimiento
                        Movimiento::create([
                            'producto_id' => $detalle->producto_id,
                            'bodega_id' => $inventario->bodega_id,
                            'tipo' => 'compra',
                            'cantidad' => $detalle->cantidad,
                            'fecha' => now(),
                            'user_id' => auth()->id() // Asumiendo que estás registrando quién hace la operación
                        ]);
                        \Log::info("Movimiento registrado para el producto ID {$detalle->producto_id} en la bodega ID {$inventario->bodega_id}.");
                    } else {
                        \Log::error("Inventario no encontrado para el detalle con ID: {$detalle->id}");
                        throw new \Exception("Inventario no encontrado para el detalle con ID: {$detalle->id}");
                    }
                }

                DB::commit();
                return redirect()->route('ordenes.index')->with('success', 'Orden marcada como entregada, guía de despacho actualizada correctamente y inventario modificado.');
            } catch (\Exception $e) {
                DB::rollback();
                \Log::error("Error al entregar la orden de compra: " . $e->getMessage());
                return redirect()->route('ordenes.index')->with('error', 'Error al marcar la orden como entregada: ' . $e->getMessage());
            }
        } else {
            return redirect()->route('ordenes.index')->with('error', 'La orden ya ha sido procesada o entregada.');
        }
    }




}
