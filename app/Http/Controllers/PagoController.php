<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Factura;
use App\Models\MetodosPago;
use App\Models\DetalleOrdenCompra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\FormatHelper;
use App\Services\EstadoService;
use App\Services\InventarioService;

class PagoController extends Controller
{
    protected $estadoService;
    protected $inventarioService;

    public function __construct(EstadoService $estadoService, InventarioService $inventarioService)
{
    $this->estadoService = $estadoService;
    $this->inventarioService = $inventarioService;


    $this->middleware(function ($request, $next) {
        if (auth()->check() && auth()->user()->hasRole('vendedor')) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        return $next($request);
    });
}


    public function index()
    {
        $pagos = Pago::orderBy('created_at', 'desc')->paginate(15);
        $metodoPago = MetodosPago::all();
        return view('pagos.index', compact('pagos', 'metodoPago'));
    }

    public function create()
    {
        $facturas = Factura::with('guiaDespacho.ordenCompra.detalles.producto', 'guiaDespacho.ordenCompra.proveedor')
                       ->where('estado_pago', 'pendiente')
                       ->get();
        $metodosPago = MetodosPago::all();
        return view('pagos.create', compact('facturas', 'metodosPago'));
    }

    public function store(Request $request)
    {
        Log::info('Inicio del método store');
    
        // Validación de los datos
        try {
            Log::info('Datos recibidos para validación', $request->all());
            $validatedData = $request->validate([
                'factura_id' => 'nullable|exists:facturas,id',
                'metodo_pago_id' => 'required|exists:metodos_pagos,id',
                'monto' => 'required|numeric|min:0.01',
                'fecha_pago' => 'required|date',
                'descripcion' => 'nullable|string|max:255',
                'estado_pago' => 'required|in:pendiente,pagado',
                'numero_transferencia' => 'nullable|string|max:255'
            ]);
            Log::info('Datos validados correctamente', $validatedData);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error en la validación', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    
        DB::beginTransaction();
    
        try {
            Log::info('Iniciando la creación del pago');
            $pago = Pago::create($validatedData);
            Log::info('Pago creado', ['pago' => $pago]);
    
            // Si existe una factura asociada, procesar su estado
            if (!empty($validatedData['factura_id'])) {
                Log::info('Procesando factura asociada', ['factura_id' => $validatedData['factura_id']]);
                $factura = Factura::find($validatedData['factura_id']);
    
                if (!$factura) {
                    throw new \Exception('Factura no encontrada');
                }
    
                Log::info('Factura encontrada', ['factura' => $factura]);
    
                // Usar el EstadoService para actualizar el estado de la factura
                $this->estadoService->actualizarEstadoFactura($factura, 'pagado');
                Log::info('Estado de la factura actualizado a pagado', ['factura_id' => $factura->id]);
    
                // Si la factura tiene una guía de despacho asociada, actualizar sus estados
                if ($factura->guiaDespacho) {
                    $this->estadoService->actualizarEstadoGuia($factura->guiaDespacho, 'entregada');
                    Log::info('Estado de la guía de despacho actualizado a entregada', ['guia_id' => $factura->guiaDespacho->id]);
    
                    // Si la guía tiene una orden de compra asociada, verificar el estado
                    if ($factura->guiaDespacho->ordenCompra) {
                        $ordenCompra = $factura->guiaDespacho->ordenCompra;
    
                        // Verificar si todas las facturas están pagadas
                        $todasFacturasPagadas = $ordenCompra->facturas->every(function ($factura) {
                            return $factura->estado_pago === 'pagado';
                        });
    
                        Log::info('Todas las facturas de la orden están pagadas', ['estado' => $todasFacturasPagadas]);
    
                        if ($todasFacturasPagadas) {
                            // Actualizar el estado de la orden de compra
                            $this->estadoService->actualizarEstadoOrden($ordenCompra, 'entregado');
                            Log::info('Estado de la orden de compra actualizado a entregado', ['orden_id' => $ordenCompra->id]);
    
                            // Agregar productos al inventario usando el InventarioService
                            $this->inventarioService->agregarInventarioDesdeOrden($ordenCompra);
                            Log::info('Inventario actualizado con los productos de la orden de compra', ['orden_id' => $ordenCompra->id]);
                        }
                    }
                }
            } else {
                Log::info('No se proporcionó factura, registrando pago sin factura');
            }
    
            DB::commit();
            Log::info('Transacción completada con éxito');
            return redirect()->route('pagos.index')->with('success', 'Pago registrado y estado actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar el pago', ['exception' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }
    
    
public function show(Pago $pago)
{
    // Cargar las relaciones necesarias
    $pago->load([
        'factura.guiaDespacho.detalles.producto',
        'factura.guiaDespacho.ordenCompra.detalles'
    ]);

    // Mapear detalles con cantidad solicitada
    $detalles = $pago->factura->guiaDespacho->detalles->map(function ($detalle) {
        $cantidadSolicitada = DetalleOrdenCompra::where('producto_id', $detalle->producto_id)
            ->where('orden_compra_id', $detalle->guiaDespacho->ordenCompra->id ?? null)
            ->value('cantidad');
    
        $subtotal = $detalle->cantidad_entregada * $detalle->precio_compra;
    
        return [
            'producto' => $detalle->producto->nombre,
            'cantidad_solicitada' => $cantidadSolicitada ?? 0,
            'cantidad_entregada' => $detalle->cantidad_entregada,
            'precio_compra' => $detalle->precio_compra,
            'subtotal' => $subtotal,
        ];
    });
    
// Calculamos el Total Neto y el IVA
$totalFactura = $pago->factura->monto_total;
$totalNeto = $totalFactura / 1.19;
$iva = $totalFactura - $totalNeto;

// Redondeamos para evitar decimales no deseados
$totalNeto = round($totalNeto, 0);
$iva = round($iva, 0);

return view('pagos.show', compact('pago', 'detalles', 'totalNeto', 'iva', 'totalFactura'));

}


public function edit($id)
{
    $pago = Pago::findOrFail($id);
    
    // Usar formato para la vista sin modificar el objeto original
    $formatter = new \NumberFormatter('es_CL', \NumberFormatter::CURRENCY);
    $formattedMonto = $formatter->formatCurrency($pago->monto, 'CLP');

    $metodoPago = MetodosPago::all();

    return view('pagos.edit', compact('pago', 'metodoPago', 'formattedMonto'));
}


public function update(Request $request, $id)
{
    $pago = Pago::findOrFail($id);
    Log::info('Actualización de pago iniciada', ['id' => $id, 'request' => $request->all()]);

    // Validar datos del formulario
    $validatedData = $request->validate([
        'metodo_pago_id' => 'required|exists:metodos_pagos,id',
        'fecha_pago' => 'required|date',
        'monto' => 'required|numeric|min:0',
        'numero_transferencia' => 'nullable|string|max:255',
        'descripcion' => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();

    try {
        // Actualizar datos del pago
        $pago->update([
            'metodo_pago_id' => $validatedData['metodo_pago_id'],
            'fecha_pago' => $validatedData['fecha_pago'],
            'numero_transferencia' => $validatedData['numero_transferencia'],
            'descripcion' => $validatedData['descripcion'],
            'monto' => filter_var($validatedData['monto'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'estado_pago' => $validatedData['monto'] > 0 ? 'pagado' : $pago->estado_pago,
        ]);

        Log::info('Pago actualizado correctamente', ['pago' => $pago]);

        // Verificar si el pago tiene una factura asociada
        if ($pago->factura) {
            $factura = $pago->factura;

            // Actualizar el estado de la factura basado en los pagos asociados
            $montoPagado = $factura->pagos()->sum('monto');
            Log::info('Monto total pagado a la factura', ['monto_pagado' => $montoPagado]);

            if ($montoPagado >= $factura->monto_total) {
                $factura->update(['estado_pago' => 'pagado']);
                Log::info('Estado de la factura actualizado a pagado', ['factura_id' => $factura->id]);

                // Actualizar la guía de despacho asociada si corresponde
                if ($factura->guiaDespacho) {
                    $factura->guiaDespacho->update(['estado' => 'entregada']);
                    Log::info('Estado de la guía de despacho actualizado a entregada', ['guia_id' => $factura->guiaDespacho->id]);

                    // Si la guía no tiene una orden de compra asociada, agregar directamente al inventario
                    if (!$factura->guiaDespacho->ordenCompra) {
                        $this->inventarioService->agregarInventarioDesdeGuia($factura->guiaDespacho);
                        Log::info('Inventario actualizado desde la guía de despacho (sin orden de compra)', ['guia_id' => $factura->guiaDespacho->id]);
                    } else {
                        // Actualizar el estado de la orden de compra si corresponde
                        $ordenCompra = $factura->guiaDespacho->ordenCompra;
                        if ($ordenCompra) {
                            $todasFacturasPagadas = $ordenCompra->facturas->every(function ($factura) {
                                return $factura->estado_pago === 'pagado';
                            });

                            if ($todasFacturasPagadas) {
                                $ordenCompra->update(['estado' => 'entregado']);
                                Log::info('Estado de la orden de compra actualizado a entregado', ['orden_id' => $ordenCompra->id]);

                                // Agregar inventario desde la guía de despacho
                                $this->inventarioService->agregarInventarioDesdeGuia($factura->guiaDespacho);
                                Log::info('Inventario actualizado desde la guía de despacho', ['guia_id' => $factura->guiaDespacho->id]);
                            }
                        }
                    }
                }
            }
        }

        DB::commit();

        return redirect()->route('pagos.index')->with('success', 'Pago actualizado y estados relacionados procesados correctamente.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al actualizar el pago', ['exception' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Error al actualizar el pago: ' . $e->getMessage());
    }
}



    public function destroy(Pago $pago)
    {
        if (auth()->user()->hasRole('bodeguero')) {
            abort(403, 'No tienes permiso para editar este producto.');
        }

        DB::beginTransaction();

        try {
            $factura = $pago->factura;
            $pago->delete();

            // Recalcular el estado de la factura
            $montoPagado = $factura->pagos()->sum('monto');
            $factura->update(['estado_pago' => $montoPagado >= $factura->monto_total ? 'pagado' : 'pendiente']);

            DB::commit();
            return redirect()->route('pagos.index')->with('success', 'Pago eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar el pago: ' . $e->getMessage());
        }
    }
    
    public function getFacturaDetalles($id)
{
    $factura = Factura::with([
        'guiaDespacho.detalles.producto',
        'guiaDespacho.ordenCompra.proveedor',
    ])->findOrFail($id);

    $detalles = $factura->guiaDespacho ? $factura->guiaDespacho->detalles->map(function ($detalle) {
        return [
            'producto' => $detalle->producto->nombre ?? 'Sin nombre',
            'cantidad_entregada' => $detalle->cantidad_entregada,
            'precio_compra' => $detalle->precio_compra,
            'subtotal' => $detalle->cantidad_entregada * $detalle->precio_compra,
        ];
    }) : [];

    return response()->json([
        'factura_id' => $factura->id,
        'numero_factura' => $factura->numero_factura,
        'monto_total' => $factura->monto_total,
        'proveedor' => $factura->guiaDespacho->ordenCompra->proveedor->nombre ?? 'Sin proveedor',
        'rut_proveedor' => $factura->guiaDespacho->ordenCompra->proveedor->rut ?? 'Sin RUT',
        'detalles' => $detalles,
    ]);
}

    
}
