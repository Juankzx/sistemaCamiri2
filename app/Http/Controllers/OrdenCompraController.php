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
        return view('ordenes.create', compact('proveedores', 'productos', 'inventarios'));
    }

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'proveedor_id' => 'required|exists:proveedores,id',
        'numero_orden' => 'required|string|unique:ordenes_compras,numero_orden',
        'estado' => 'required|string',
        'detalles' => 'required|array',
        'detalles.*.producto_id' => 'required|exists:productos,id',
        'detalles.*.cantidad' => 'required|integer|min:1',
        'detalles.*.precio_compra' => 'required|numeric',
        'detalles.*.inventario_id' => 'required|exists:inventarios,id',
        // Datos para guía de despacho
        'numero_guia' => 'required|string|unique:guias_despacho,numero_guia',
        'fecha_entrega' => 'required|date',
        // Datos para factura
        'numero_factura' => 'required|string|unique:facturas,numero_factura',
        'fecha_factura' => 'required|date',
        'total_factura' => 'required|numeric',
    ]);

    DB::transaction(function () use ($validatedData, $request) {
        // Crear la orden de compra
        $ordenCompra = OrdenCompra::create($validatedData);
        foreach ($request->detalles as $detalle) {
            $ordenCompra->detalles()->create([
                'producto_id' => $detalle['producto_id'],
                'cantidad' => $detalle['cantidad'],
                'precio_compra' => $detalle['precio_compra'],
                'inventario_id' => $detalle['inventario_id'],
            ]);
        }

        // Crear la guía de despacho
        $guiaDespacho = $ordenCompra->guiasDespacho()->create([
            'numero_guia' => $request->numero_guia,
            'fecha_entrega' => $request->fecha_entrega,
            'estado' => 'emitida', // Estado inicial
        ]);

        // Crear la factura
        $factura = $guiaDespacho->facturas()->create([
            'numero_factura' => $request->numero_factura,
            'fecha_factura' => $request->fecha_factura,
            'total_factura' => $request->total_factura,
            'estado_pago' => 'pendiente', // Estado inicial
        ]);
    });

    return redirect()->route('ordenes.index')->with('success', 'Orden de compra, guía de despacho y factura creadas con éxito.');
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
    $validated = $request->validate([
        'estado' => 'required|string',
    ]);

    DB::beginTransaction();
    try {
        if ($ordenCompra->estado !== 'entregado' && $validated['estado'] === 'entregado') {
            $ordenCompra->update(['estado' => $validated['estado']]);

            foreach ($ordenCompra->detalles as $detalle) {
                $inventario = Inventario::find($detalle->inventario_id);
                if ($inventario) {
                    $cantidadAnterior = $inventario->cantidad;
                    $inventario->increment('cantidad', $detalle->cantidad);

                    // Registrar el movimiento de inventario
                    Movimiento::create([
                        'inventario_id' => $inventario->id,
                        'tipo' => 'entrada',
                        'cantidad' => $detalle->cantidad,
                        'descripcion' => "Ingreso por entrega de Orden de Compra #{$ordenCompra->id}"
                    ]);

                    \Log::info("Inventario actualizado: {$inventario->id}, Cantidad añadida: {$detalle->cantidad}, Antes: {$cantidadAnterior}, Ahora: {$inventario->cantidad}");
                } else {
                    \Log::error("Inventario no encontrado para el detalle con ID: {$detalle->id}");
                    throw new \Exception("Inventario no encontrado para el detalle con ID: {$detalle->id}");
                }
            }
        }

        DB::commit();
        return redirect()->route('ordenes.index')->with('success', 'Orden de compra actualizada con éxito y movimientos de inventario registrados.');
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error("Error al actualizar la orden de compra: " . $e->getMessage());
        return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
    }
}
    public function destroy(OrdenCompra $orden)
    {
        $orden->delete();
        return redirect()->route('ordenes.index')->with('success', 'Orden de compra eliminada con éxito.');
    }

    public function storeCompleta(Request $request)
{
    DB::transaction(function () use ($request) {
        // Crear Orden de Compra
        $ordenCompra = OrdenCompra::create($request->only(['proveedor_id', 'numero_orden', 'estado']));

        // Crear Detalles de Orden de Compra
        foreach ($request->detalles as $detalle) {
            $ordenCompra->detalles()->create($detalle);
        }

        // Crear Guía de Despacho
        $guia = GuiaDespacho::create($request->only(['orden_compra_id', 'numero_guia', 'fecha_entrega', 'estado_guia']));

        // Crear Factura
        $factura = Factura::create($request->only(['guia_despacho_id', 'numero_factura', 'fecha_factura', 'total_factura', 'estado_pago']));
    });

    return redirect()->route('ordenes.index')->with('success', 'Orden de compra completa creada con éxito.');
}

public function entregar($id)
{
    $ordenCompra = OrdenCompra::with(['guiasDespacho', 'detalles.inventario'])->findOrFail($id);
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
                $inventario = Inventario::find($detalle->inventario_id);
                if ($inventario) {
                    $inventario->increment('cantidad', $detalle->cantidad);
                    \Log::info("Inventario actualizado: Producto ID {$inventario->producto_id}, Sucursal ID {$inventario->sucursal_id}, Añadidas {$detalle->cantidad} unidades.");

                    // Registrar movimiento
                    Movimiento::create([
                        'producto_id' => $detalle->producto_id,
                        'sucursal_id' => $inventario->sucursal_id,
                        'tipo' => 'entrada',
                        'cantidad' => $detalle->cantidad,
                        'fecha' => now(),
                        'user_id' => auth()->id() // Asumiendo que estás registrando quién hace la operación
                    ]);
                    \Log::info("Movimiento registrado para el producto ID {$detalle->producto_id} en la sucursal ID {$inventario->sucursal_id}.");
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
