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
        $productos = Producto::where('estado', 1)
        ->whereHas('unidadMedida', function ($query) {
            $query->whereNotIn('nombre', ['Kilo', 'KG', 'kilo', 'kg']);
        })
        ->whereHas('categoria', function ($query) {
            $query->where('sin_stock', 0); // Excluye las categorías sin stock
        })
        ->get();



        return view('guias-despacho.create', compact('ordenCompra', 'productos'));
    }

    public function store(Request $request)
{
    
    
        \Log::info('Iniciando creación de Guía de Despacho.');
        \Log::info('Datos recibidos:', $request->all());

        // Validar los datos
        try {
            $validatedData = $request->validate([
                'numero_guia' => 'required|string|max:255|unique:guias_despacho,numero_guia',
                'fecha_entrega' => 'required|date',
                'orden_compra_id' => 'nullable|exists:ordenes_compras,id',
                'detalles' => 'required|array|min:1',
                'detalles.*.producto_id' => 'required|exists:productos,id',
                'detalles.*.cantidad_entregada' => 'required|integer|min:1',
                'detalles.*.precio_compra' => 'required|numeric|min:0',
            ]);
            \Log::info('Datos validados correctamente.', $validatedData);
        } catch (\Exception $e) {
            \Log::error('Error en validación: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error en validación: ' . $e->getMessage()]);
        }

        // Guardar los datos
        try {
            DB::beginTransaction();
            \Log::info('Transacción iniciada.');

            $guiaDespacho = GuiaDespacho::create([
                'numero_guia' => $validatedData['numero_guia'],
                'fecha_entrega' => $validatedData['fecha_entrega'],
                'orden_compra_id' => $validatedData['orden_compra_id'] ?? null,
                'estado' => 'emitida',
            ]);
            \Log::info('Guía de Despacho creada.', $guiaDespacho->toArray());

            $total = 0;

            foreach ($request->detalles as $detalle) {
                // Validar cada detalle manualmente si es necesario
                if (empty($detalle['producto_id']) || empty($detalle['cantidad_entregada']) || empty($detalle['precio_compra'])) {
                    throw new \Exception("Faltan detalles para un producto en la guía.");
                }

                $subtotal = $detalle['cantidad_entregada'] * $detalle['precio_compra'];
                $total += $subtotal;

                DetalleGuiaDespacho::create([
                    'guia_despacho_id' => $guiaDespacho->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad_entregada' => $detalle['cantidad_entregada'],
                    'precio_compra' => $detalle['precio_compra'],
                    'subtotal' => $subtotal,
                ]);

                // Actualizar inventario
                Inventario::addStock($detalle['producto_id'], $detalle['cantidad_entregada'], 1);

                // Crear movimiento
                Movimiento::create([
                    'producto_id' => $detalle['producto_id'],
                    'bodega_id' => 1,
                    'sucursal_id' => null,
                    'tipo' => 'compra',
                    'cantidad' => $detalle['cantidad_entregada'],
                    'fecha' => now(),
                    'user_id' => auth()->id(),
                ]);
            }

            $guiaDespacho->update(['total' => $total]);
            \Log::info('Total actualizado: ' . $total);

            if ($guiaDespacho->ordenCompra && $guiaDespacho->ordenCompra->estado === 'solicitado') {
                $guiaDespacho->ordenCompra->update(['estado' => 'en_transito']);
                \Log::info('Estado de Orden de Compra actualizado a en_transito.');
            }

            DB::commit();
            \Log::info('Transacción completada con éxito.');

            return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho creada con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear la Guía de Despacho: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error al crear la Guía de Despacho: ' . $e->getMessage()]);
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

    public function show($id)
{
    $guiaDespacho = GuiaDespacho::with([
        'detalles.producto', // Relación con los detalles y productos
        'ordenCompra.proveedor', // Relación con la orden de compra y el proveedor
        'ordenCompra.detalles', // Relación con los detalles de la orden de compra
        'user', // Relación con el usuario
    ])->findOrFail($id);

    // Mapear los detalles de la guía de despacho
    $detalles = $guiaDespacho->detalles->map(function ($detalle) use ($guiaDespacho) {
        // Obtener la cantidad solicitada desde los detalles de la orden de compra, si existe
        $cantidadSolicitada = $guiaDespacho->ordenCompra
            ? $guiaDespacho->ordenCompra->detalles
                ->where('producto_id', $detalle->producto_id)
                ->first()
                ->cantidad ?? null // Usar null si no existe el detalle
            : null;

        return [
            'producto' => $detalle->producto->nombre ?? 'Producto no disponible',
            'cantidad_solicitada' => $cantidadSolicitada ?? 'N/A', // Predeterminado si no existe
            'cantidad_entregada' => $detalle->cantidad_entregada,
            'precio_unitario' => $detalle->precio_compra ?? 0, // Predeterminado si no hay precio
            'subtotal' => ($detalle->cantidad_entregada ?? 0) * ($detalle->precio_compra ?? 0),
        ];
    });

    // Calcular totales
    $totalCantidadSolicitada = $detalles->filter(function ($detalle) {
        return is_numeric($detalle['cantidad_solicitada']);
    })->sum('cantidad_solicitada');
    $totalCantidadEntregada = $detalles->sum('cantidad_entregada');
    $totalMonto = $detalles->sum('subtotal');

    // Si no hay relación directa con el usuario, mostrar "No disponible"
    $usuario = $guiaDespacho->user->name ?? 'No disponible';

    return view('guias-despacho.show', [
        'guiaDespacho' => $guiaDespacho,
        'detalles' => $detalles,
        'totalCantidadSolicitada' => $totalCantidadSolicitada,
        'totalCantidadEntregada' => $totalCantidadEntregada,
        'totalMonto' => $totalMonto,
        'usuario' => $usuario,
    ]);
}


public function edit($id)
{
    $guiaDespacho = GuiaDespacho::with(['detalles.producto', 'ordenCompra.proveedor'])
        ->findOrFail($id);

    // Verificar el estado
    if ($guiaDespacho->estado === 'entregada') {
        return redirect()->route('guias-despacho.index')
            ->with('error', 'No se puede editar una guía de despacho que ya ha sido entregada.');
    }

    return view('guias-despacho.edit', compact('guiaDespacho'));
}


public function update(Request $request, $id)
{
    $guiaDespacho = GuiaDespacho::findOrFail($id);

    // Bloquear edición si el estado es "entregado"
    if ($guiaDespacho->estado === 'entregada') {
        return redirect()->route('guias-despacho.index')
            ->with('error', 'No se puede actualizar una guía de despacho que ya ha sido entregada.');
    }

    // Validar datos
    $validatedData = $request->validate([
        'fecha_entrega' => 'required|date',
        'detalles' => 'required|array',
        'detalles.*.cantidad_entregada' => 'required|numeric|min:0',
        
    ]);

    DB::beginTransaction();

    try {
        $guiaDespacho->fecha_entrega = $validatedData['fecha_entrega'];
        $guiaDespacho->estado = $validatedData['estado'];
        $guiaDespacho->save();

        // Actualizar los detalles
        foreach ($validatedData['detalles'] as $detalleId => $detalle) {
            $detalleModel = DetalleGuiaDespacho::findOrFail($detalleId);
            $detalleModel->cantidad_entregada = $detalle['cantidad_entregada'];
            $detalleModel->save();
        }

        DB::commit();

        return redirect()->route('guias-despacho.index')
            ->with('success', 'Guía de despacho actualizada correctamente.');
    } catch (\Exception $e) {
        DB::rollBack();

        return redirect()->back()->with('error', 'Error al actualizar la guía de despacho: ' . $e->getMessage());
    }
}





}
