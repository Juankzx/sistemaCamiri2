<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\GuiaDespacho;
use App\Models\OrdenCompra;
use App\Models\Producto;
use App\Models\DetalleGuiaDespacho;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GuiaDespachoController extends Controller
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
        $guias = GuiaDespacho::with('ordenCompra.proveedor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('guias-despacho.index', compact('guias'));
    }

    public function create()
    {
        $ordenCompra = OrdenCompra::where('estado', 'solicitado')->get();
        $productos = Producto::where('estado', 1)->get();

        return view('guias-despacho.create', compact('ordenCompra', 'productos'));
    }

    public function store(Request $request)
{
    \Log::info('Iniciando creación de Guía de Despacho.');

    // Validar los datos
    try {
        \Log::info('Validando datos recibidos', $request->all());

        $validatedData = $request->validate([
            'numero_guia' => 'required|string|max:255|unique:guias_despacho,numero_guia',
            'fecha_entrega' => 'required|date',
            'orden_compra_id' => 'nullable|exists:ordenes_compras,id',
            'detalles' => 'required|array',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_compra' => 'required|numeric|min:0',
        ]);
        
        \Log::info('Datos validados correctamente', $validatedData);
    } catch (\Exception $e) {
        \Log::error('Error en validación: ' . $e->getMessage());
        return redirect()->back()->withErrors($e->getMessage());
    }

    try {
        DB::beginTransaction();
        \Log::info('Transacción iniciada.');

        // Crear la Guía de Despacho
        $guiaDespacho = GuiaDespacho::create([
            'numero_guia' => $validatedData['numero_guia'],
            'fecha_entrega' => $validatedData['fecha_entrega'],
            'orden_compra_id' => $validatedData['orden_compra_id'] ?? null,
            'estado' => 'emitida',
        ]);
        
        \Log::info('Guía de Despacho creada: ', $guiaDespacho->toArray());

        $total = 0;

        foreach ($request->detalles as $detalle) {
            \Log::info('Procesando detalle', $detalle);

            if (!isset($detalle['producto_id'], $detalle['cantidad'], $detalle['precio_compra'])) {
                \Log::error("Faltan detalles para el producto ID: {$detalle['producto_id']}");
                throw new \Exception("Faltan detalles para el producto con ID: {$detalle['producto_id']}");
            }

            $subtotal = $detalle['cantidad'] * $detalle['precio_compra'];
            $total += $subtotal;

            DetalleGuiaDespacho::create([
                'guia_despacho_id' => $guiaDespacho->id,
                'producto_id' => $detalle['producto_id'],
                'cantidad_entregada' => $detalle['cantidad'],
                'precio_compra' => $detalle['precio_compra'],
                'subtotal' => $subtotal,
            ]);

            \Log::info("Detalle creado para producto ID: {$detalle['producto_id']}");

            Inventario::addStock($detalle['producto_id'], $detalle['cantidad'], 1);

            Movimiento::create([
                'producto_id' => $detalle['producto_id'],
                'bodega_id' => 1,
                'sucursal_id' => null,
                'tipo' => 'compra',
                'cantidad' => $detalle['cantidad'],
                'fecha' => now(),
                'user_id' => auth()->id(),
            ]);
        }

        $guiaDespacho->update(['total' => $total]);
        \Log::info('Total actualizado: ' . $total);

        $ordenCompra = $guiaDespacho->ordenCompra;
        if ($ordenCompra && $ordenCompra->estado === 'solicitado') {
            $ordenCompra->update(['estado' => 'en_transito']);
            \Log::info('Estado de Orden de Compra actualizado a en_transito');
        }

        DB::commit();
        \Log::info('Transacción completada con éxito.');

        return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho creada con éxito y stock actualizado.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("Error al crear la Guía de Despacho: " . $e->getMessage());
        return redirect()->back()->with('error', 'Error al crear la Guía de Despacho: ' . $e->getMessage());
    }
}

public function getOrdenCompraDetails($id)
{
    $ordenCompra = OrdenCompra::with('detalles.producto')->findOrFail($id);
    return response()->json($ordenCompra);
}

public function getDetalles($id)
{
    // Cargar la guía de despacho con sus detalles y el proveedor de la orden de compra
    $guiaDespacho = GuiaDespacho::with('detalles.producto', 'ordenCompra.proveedor')->findOrFail($id);
    
    // Obtener el proveedor de la orden de compra, si está disponible
    $proveedor = $guiaDespacho->ordenCompra->proveedor ?? null;

    return response()->json([
        'detalles' => $guiaDespacho->detalles,
        'proveedor' => $proveedor
    ]);
}

    public function destroy($id)
    {
        if (auth()->user()->hasRole('bodeguero')) {
            abort(403, 'No tienes permiso para editar este producto.');
        }

        $guiaDespacho = GuiaDespacho::findOrFail($id);

        try {
            if ($guiaDespacho->facturas()->exists()) {
                return redirect()->route('guias-despacho.index')->with('error', 'No se puede eliminar la guía de despacho porque tiene facturas asociadas.');
            }

            $guiaDespacho->delete();

            $ordenCompra = OrdenCompra::find($guiaDespacho->orden_compra_id);
            if ($ordenCompra && $ordenCompra->guiasDespacho()->count() === 0) {
                $ordenCompra->update(['estado' => 'solicitado']);
            }

            return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho eliminada con éxito.');
        } catch (\Exception $e) {
            Log::error("Error al eliminar la guía de despacho: " . $e->getMessage());
            return redirect()->route('guias-despacho.index')->with('error', 'No se pudo eliminar la guía de despacho: ' . $e->getMessage());
        }
    }
}
