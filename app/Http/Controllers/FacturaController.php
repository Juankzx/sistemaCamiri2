<?php

namespace App\Http\Controllers;

use App\Models\GuiaDespacho;
use App\Models\Factura;
use App\Models\Proveedore;
use App\Models\OrdenCompra;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        if (auth()->check() && auth()->user()->hasRole('vendedor')) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        return $next($request);
    });
}


    public function index()
    {
        $facturas = Factura::with(['guiaDespacho.ordenCompra.proveedor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $guiasDespacho = GuiaDespacho::whereIn('estado', ['emitida', 'en_transito'])->get();
        $proveedor = Proveedore::all();
        $ordenCompra = OrdenCompra::all();
        return view('facturas.create', compact('guiasDespacho', 'ordenCompra'));
    }

    public function store(Request $request)
    {
        // Validación de los datos recibidos
        $validatedData = $request->validate([
            'guia_despacho_id' => 'nullable|exists:guias_despacho,id',
            'numero_factura' => 'required|string|unique:facturas,numero_factura',
            'fecha_emision' => 'required|date',
            'monto_total' => 'required|numeric|min:0',
            'estado_pago' => 'required|in:pendiente,pagado',
        ]);

        try {
            // Crear la factura y asociarla a la guía de despacho
            $factura = Factura::create($validatedData);

            // Actualizar el estado de la guía de despacho si el estado de pago es "pagado"
            if ($factura->estado_pago === 'pagado') {
                $guiaDespacho = GuiaDespacho::findOrFail($validatedData['guia_despacho_id']);
                $guiaDespacho->update(['estado' => 'entregada']);
            }

            return redirect()->route('facturas.index')->with('success', 'Factura creada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

    public function show(Factura $factura)
    {
        // Cargar relaciones necesarias
        $factura->load([
            'guiaDespacho.detalles.producto', // Relación con productos de la guía
            'guiaDespacho.ordenCompra.proveedor', // Relación con proveedor
        ]);
    
        // Si existe una guía de despacho, calcula los detalles
        $detalles = $factura->guiaDespacho
            ? $factura->guiaDespacho->detalles->map(function ($detalle) {
                return [
                    'producto' => $detalle->producto->nombre ?? 'Sin nombre',
                    'cantidad_entregada' => $detalle->cantidad_entregada ?? 0,
                    'precio_unitario' => $detalle->precio_compra ?? 0,
                    'subtotal' => ($detalle->cantidad_entregada ?? 0) * ($detalle->precio_compra ?? 0),
                ];
            })
            : collect([]); // Si no hay guía de despacho, detalles es una colección vacía
    
        // Cálculo de totales
        $totalNeto = $detalles->sum('subtotal'); // Total neto basado en productos
        $totalIVA = $totalNeto * 0.19; // IVA del 19%
        $totalFactura = $totalNeto + $totalIVA;
    
        // Si no hay productos relacionados, toma el monto_total de la factura
        if ($detalles->isEmpty()) {
            $totalNeto = $factura->monto_total / 1.19; // Derivar total neto del monto total
            $totalIVA = $factura->monto_total - $totalNeto;
            $totalFactura = $factura->monto_total; // Usar el total directo de la factura
        }
    
        return view('facturas.show', [
            'factura' => $factura,
            'detalles' => $detalles,
            'totalNeto' => $totalNeto,
            'totalIVA' => $totalIVA,
            'totalFactura' => $totalFactura,
        ]);
    }
    
    

    


    public function edit(Factura $factura)
{
    // Validar si el estado es diferente de 'pendiente'
    if ($factura->estado_pago !== 'pendiente') {
        return redirect()->route('facturas.index')->with('error', 'Solo se pueden editar facturas en estado pendiente.');
    }

    // Verificar si la factura tiene una guía de despacho
    $puedeEditarTotal = is_null($factura->guia_despacho_id);
    // Validar el rol del usuario
    if (auth()->user()->hasRole('bodeguero')) {
        abort(403, 'No tienes permiso para editar esta factura.');
    }

    return view('facturas.edit', compact('factura', 'puedeEditarTotal'));
}

public function update(Request $request, Factura $factura)
{
    $validatedData = $request->validate([
        'numero_factura' => 'required|string|max:255',
        'fecha_emision' => 'required|date',
        'monto_total' => 'nullable|numeric|min:0',
        'estado_pago' => 'required|in:pendiente,pagado',
    ]);

    // Solo permitir la edición del total si no tiene guía de despacho
    if (!is_null($factura->guia_despacho_id)) {
        unset($validatedData['monto_total']);
    }

    $factura->update($validatedData);

    return redirect()->route('facturas.index')->with('success', 'Factura actualizada correctamente.');
}


    public function destroy(Factura $factura)
    {
        if (auth()->user()->hasRole('bodeguero')) {
            abort(403, 'No tienes permiso para editar este producto.');
        }

        $factura->delete();
        return redirect()->route('facturas.index')->with('success', 'Factura eliminada con éxito.');
    }

    public function getDetalles($id)
    {
        $guiaDespacho = GuiaDespacho::with('detalles.producto', 'ordenCompra.proveedor', 'ordenCompra.detalles')
                                     ->findOrFail($id);
    
        $detalles = $guiaDespacho->detalles->map(function ($detalle) use ($guiaDespacho) {
            // Buscar la cantidad solicitada en la orden de compra, si existe
            $cantidadSolicitada = optional(
                $guiaDespacho->ordenCompra?->detalles
                    ->where('producto_id', $detalle->producto_id)
                    ->first()
            )->cantidad;
    
            return [
                'producto' => $detalle->producto,
                'cantidad_entregada' => $detalle->cantidad_entregada,
                'precio_compra' => $detalle->precio_compra,
                'subtotal' => $detalle->subtotal,
                'cantidad_solicitada' => $cantidadSolicitada ?? 0, // Valor predeterminado si no hay orden
            ];
        });
    
        // Calcular el monto total (sumatoria de subtotales de los detalles)
        $montoTotal = $guiaDespacho->detalles->sum(function ($detalle) {
            return $detalle->cantidad_entregada * $detalle->precio_compra;
        });
    
        return response()->json([
            'detalles' => $detalles,
            'proveedor' => $guiaDespacho->ordenCompra->proveedor->nombre ?? 'Proveedor no disponible',
            'total' => $montoTotal, // Agregar el total al JSON de respuesta
        ]);
    }
    

}
