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
use Barryvdh\DomPDF\Facade\Pdf;


class OrdenCompraController extends Controller
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
        $ordenes = OrdenCompra::with('proveedor', 'detalles.producto')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('ordenes.index', compact('ordenes'));
    }

    public function create()
{
    $proveedores = Proveedore::all();
    $productos = Producto::whereHas('categoria', function ($query) {
        $query->where('sin_stock', '!=', 1);
    })->get();
    

    $ultimoNumero = OrdenCompra::max('numero_orden') ?? 0;
    $nuevoNumeroOrden = $ultimoNumero + 1;

    return view('ordenes.create', compact('proveedores', 'productos', 'nuevoNumeroOrden'));
}


public function store(Request $request)
{
    $validated = $request->validate([
        'proveedor_id' => 'required|exists:proveedores,id',
        'detalles' => 'required|array|min:1',
        'detalles.*.producto_id' => 'required|exists:productos,id',
        'detalles.*.cantidad' => 'required|integer|min:1',
    ]);

    try {
        DB::beginTransaction();

        $ultimoNumeroOrden = OrdenCompra::max('numero_orden') ?? 0;
        $proximoNumeroOrden = $ultimoNumeroOrden + 1;

        $orden = OrdenCompra::create([
            'proveedor_id' => $request->proveedor_id,
            'numero_orden' => $proximoNumeroOrden,
            'estado' => 'solicitado',
        ]);

        foreach ($request->detalles as $detalle) {
            $producto = Producto::findOrFail($detalle['producto_id']);
            DetalleOrdenCompra::create([
                'orden_compra_id' => $orden->id,
                'producto_id' => $producto->id,
                'cantidad' => $detalle['cantidad'],
                'subtotal' => $producto->precio_compra * $detalle['cantidad'],
            ]);
        }

        $orden->calcularTotal();

        DB::commit();

        return redirect()->route('ordenes-compras.index')
            ->with('success', 'Orden de Compra creada exitosamente.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("Error al crear la orden de compra: " . $e->getMessage());
        return redirect()->back()->with('error', 'Error al crear la orden de compra.');
    }
}
    
    
    

    public function destroy($id)
{
    if (auth()->user()->hasRole('bodeguero')) {
        abort(403, 'No tienes permiso para editar este producto.');
    }

    try {
        // Buscar la orden de compra
        $ordenCompra = OrdenCompra::findOrFail($id);

        // Verificar si tiene guías de despacho asociadas o facturas que podrían impedir su eliminación
        if ($ordenCompra->guiasDespacho()->exists() || $ordenCompra->facturas()->exists()) {
            return redirect()->route('ordenes.index')->with('error', 'No se puede eliminar la orden de compra porque tiene guías de despacho o facturas asociadas.');
        }

        // Eliminar la orden de compra
        $ordenCompra->delete();

        return redirect()->route('ordenes.index')->with('success', 'Orden de compra eliminada con éxito.');
    } catch (\Exception $e) {
        \Log::error("Error al eliminar la orden de compra: " . $e->getMessage());
        return redirect()->route('ordenes.index')->with('error', 'No se pudo eliminar la orden de compra: ' . $e->getMessage());
    }
}

public function exportarPdf($id)
{
    $orden = OrdenCompra::with(['proveedor', 'detalles.producto'])->findOrFail($id);

    // Cargar una vista específica para el PDF y pasarle los datos
    $pdf = \PDF::loadView('ordenes.pdf', compact('orden'));

    // Descargar el archivo PDF
    return $pdf->stream('orden_compra_' . $orden->numero_orden . '.pdf');
}

public function show($id)
{
    $orden = OrdenCompra::with(['proveedor', 'detalles.producto'])->findOrFail($id);

    return view('ordenes.show', compact('orden'));
}

public function edit($id)
    {
        $ordenCompra = OrdenCompra::with(['detalles.producto', 'proveedor'])->findOrFail($id);
    
        $productos = Producto::all();

        return view('ordenes.edit', compact('ordenCompra', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $ordenCompra = OrdenCompra::with(['detalles'])->findOrFail($id);

        // Validar los datos sin permitir la edición del estado
        $validated = $request->validate([
            
            'detalles' => 'required|array',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
        ]);

  

        // Actualizar los detalles de la orden de compra
        $ordenCompra->detalles()->delete(); // Elimina los detalles existentes

        foreach ($request->detalles as $detalle) {
            DetalleOrdenCompra::create([
                'orden_compra_id' => $ordenCompra->id,
                'producto_id' => $detalle['producto_id'],
                'cantidad' => $detalle['cantidad'],
                'precio_compra' => 0, // Precio se actualizará en otro flujo si es necesario
                'subtotal' => 0,
            ]);
        }

        return redirect()->route('ordenes.index')->with('success', 'Orden de compra actualizada exitosamente.');
    }




}
