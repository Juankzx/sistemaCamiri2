<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Producto;
use App\Models\OrdenCompra;
use App\Models\DetalleOrdenCompra;
use App\Models\Proveedore;
use App\Models\GuiaDespacho;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenCompraController extends Controller
{
    public function index()
    {
        $ordenes = OrdenCompra::with(['proveedor', 'guiasDespacho', 'detalles.producto', 'detalles.inventario'])->paginate(15);
        return view('ordenes.index', compact('ordenes'));
    }

    

    public function create()
    {
        $proveedores = Proveedore::all();
        $inventarios = Inventario::with(['producto', 'sucursal', 'bodega'])->get();
        $productos = Producto::whereHas('inventarios.bodega', function($query) {
            $query->where('nombre', 'Bodega General');
        })->get();
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
            'detalles.*.precio_compra' => 'required|numeric|min:0.01', // Validar que el precio sea mayor a 0
            'total' => 'required|numeric|min:0', // Validar que el total no sea negativo
        ]);
    
        try {
            // Iniciar una transacción para mantener la integridad de los datos
            DB::transaction(function () use ($validatedData, $request) {
                // Calcular el nuevo número de orden de compra
                $ultimoNumeroOrden = OrdenCompra::max('numero_orden');
                $nuevoNumeroOrden = $ultimoNumeroOrden ? $ultimoNumeroOrden + 1 : 1;
    
                // Crear la orden de compra con el estado inicial 'solicitado'
                $ordenCompra = OrdenCompra::create([
                    'numero_orden' => $nuevoNumeroOrden,
                    'proveedor_id' => $validatedData['proveedor_id'],
                    'estado' => 'solicitado',
                    'total' => $validatedData['total'],
                ]);
    
                // Iterar sobre los detalles para verificar que los productos estén en la Bodega General
                foreach ($request->detalles as $detalle) {
                    // Verificar que el producto esté registrado en la Bodega General
                    $inventario = Inventario::where('producto_id', $detalle['producto_id'])
                                            ->whereHas('bodega', function ($query) {
                                                $query->where('nombre', 'Bodega General');
                                            })->first();
    
                    if (!$inventario) {
                        throw new \Exception("El producto ID: {$detalle['producto_id']} no está registrado en la Bodega General. Por favor, asegúrese de que todos los productos existan en la Bodega General antes de proceder.");
                    }
    
                    // Crear el detalle de la orden si la validación pasa
                    $ordenCompra->detalles()->create([
                        'producto_id' => $detalle['producto_id'],
                        'cantidad' => $detalle['cantidad'],
                        'precio_compra' => $detalle['precio_compra'],
                    ]);
                }
            });
    
            return redirect()->route('ordenes.index')->with('success', 'Orden de compra creada con éxito.');
        } catch (\Exception $e) {
            \Log::error("Error al crear la orden de compra: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al crear la orden de compra: ' . $e->getMessage());
        }
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
    // Cargar la orden de compra con las relaciones necesarias
    $ordenCompra = OrdenCompra::with(['guiasDespacho', 'detalles.producto', 'facturas'])->findOrFail($id);

    // Verificar que la orden de compra esté en estado 'en_transito'
    if ($ordenCompra->estado == 'en_transito') {
        DB::beginTransaction();
        try {
            // Verificar que existan facturas asociadas
            if ($ordenCompra->facturas->isEmpty()) {
                return redirect()->route('ordenes.index')->with('warning', 'No se puede entregar: la orden no tiene facturas asociadas.');
            }

            // Verificar si todas las facturas están en estado "pagado"
            $todasFacturasPagadas = $ordenCompra->facturas->every(function ($factura) {
                return $factura->estado_pago === 'pagado';
            });

            // Si todas las facturas están pagadas, se marca la orden como 'entregado'
            if ($todasFacturasPagadas) {
                // Actualizar el estado de la orden de compra
                $ordenCompra->update(['estado' => 'entregado']);
                \Log::info("Orden de compra ID {$ordenCompra->id} marcada como 'entregado'.");

                // Verificar y actualizar el estado de cada guía de despacho
                if ($ordenCompra->guiasDespacho->isNotEmpty()) {
                    foreach ($ordenCompra->guiasDespacho as $guia) {
                        $guia->update(['estado' => 'entregada']);
                        \Log::info("Guía de despacho ID {$guia->id} actualizada a 'Entregada'.");
                    }
                } else {
                    \Log::warning("No se encontraron guías de despacho para la orden de compra ID {$ordenCompra->id}.");
                }

                // Actualizar el inventario y registrar movimientos
                foreach ($ordenCompra->detalles as $detalle) {
                    $inventario = Inventario::where('producto_id', $detalle->producto_id)
                        ->where('bodega_id', 1) // Asumiendo que '1' es el ID de la bodega general
                        ->first();

                    if ($inventario) {
                        // Incrementar cantidad en inventario
                        $inventario->increment('cantidad', $detalle->cantidad);
                        \Log::info("Inventario actualizado: Producto ID {$inventario->producto_id}, Bodega ID {$inventario->bodega_id}, Añadidas {$detalle->cantidad} unidades.");

                        // Registrar movimiento en inventario
                        Movimiento::create([
                            'producto_id' => $detalle->producto_id,
                            'bodega_id' => $inventario->bodega_id,
                            'tipo' => 'compra',
                            'cantidad' => $detalle->cantidad,
                            'fecha' => now(),
                            'user_id' => auth()->id() // Registrar el usuario que realiza la operación
                        ]);
                        \Log::info("Movimiento registrado para el producto ID {$detalle->producto_id} en la bodega ID {$inventario->bodega_id}.");
                    } else {
                        \Log::error("Inventario no encontrado para el detalle con ID: {$detalle->id}");
                        throw new \Exception("Inventario no encontrado para el detalle con ID: {$detalle->id}");
                    }
                }

                DB::commit();
                return redirect()->route('ordenes.index')->with('success', 'Orden marcada como entregada, guía de despacho actualizada correctamente y inventario modificado.');
            } else {
                DB::rollback();
                return redirect()->route('ordenes.index')->with('warning', 'No se puede marcar como entregada: existen facturas que no están pagadas.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("Error al entregar la orden de compra: " . $e->getMessage());
            return redirect()->route('ordenes.index')->with('error', 'Error al marcar la orden como entregada: ' . $e->getMessage());
        }
    } else {
        return redirect()->route('ordenes.index')->with('error', 'La orden ya ha sido procesada o entregada.');
    }
}

public function actualizarInventario($ordenCompra)
{
    try {
        foreach ($ordenCompra->detalles as $detalle) {
            // Buscar el inventario en la bodega general (ID 1 como supuesto)
            $inventario = Inventario::where('producto_id', $detalle->producto_id)
                                    ->where('bodega_id', 1) // Cambiar por el ID de la bodega correcta si es necesario
                                    ->first();

            if ($inventario) {
                // Incrementar la cantidad en el inventario
                $inventario->increment('cantidad', $detalle->cantidad);
                \Log::info("Inventario actualizado: Producto ID {$inventario->producto_id}, Bodega ID {$inventario->bodega_id}, Añadidas {$detalle->cantidad} unidades.");

                // Registrar movimiento en la tabla de movimientos
                Movimiento::create([
                    'producto_id' => $detalle->producto_id,
                    'bodega_id' => $inventario->bodega_id,
                    'tipo' => 'compra', // Tipo de movimiento (puede ser 'compra', 'venta', etc.)
                    'cantidad' => $detalle->cantidad,
                    'fecha' => now(),
                    'user_id' => auth()->id() // Registrar el usuario que realiza la operación
                ]);

                \Log::info("Movimiento registrado para el producto ID {$detalle->producto_id} en la bodega ID {$inventario->bodega_id}.");
            } else {
                // Si el inventario no existe, crear una nueva entrada para el producto en la bodega
                Inventario::create([
                    'producto_id' => $detalle->producto_id,
                    'bodega_id' => 1, // Asignar ID de la bodega correcta
                    'cantidad' => $detalle->cantidad,
                ]);

                \Log::info("Nuevo inventario creado: Producto ID {$detalle->producto_id}, Bodega ID 1, Cantidad {$detalle->cantidad} unidades.");

                // Registrar movimiento en la tabla de movimientos para el nuevo inventario
                Movimiento::create([
                    'producto_id' => $detalle->producto_id,
                    'bodega_id' => 1, // Asignar ID de la bodega correcta
                    'tipo' => 'compra',
                    'cantidad' => $detalle->cantidad,
                    'fecha' => now(),
                    'user_id' => auth()->id()
                ]);
                
                \Log::info("Movimiento registrado para el producto ID {$detalle->producto_id} en la nueva bodega.");
            }
        }

        return true; // Retornar verdadero si se actualizó correctamente
    } catch (\Exception $e) {
        \Log::error("Error al actualizar inventario para la orden de compra ID {$ordenCompra->id}: " . $e->getMessage());
        return false; // Retornar falso en caso de error
    }
}



    
    




}