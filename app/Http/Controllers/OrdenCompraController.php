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
        $productos = Producto::all();
        $nuevoNumeroOrden = $this->generateOrderNumber();

        return view('ordenes.create', compact('proveedores', 'productos', 'nuevoNumeroOrden'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'detalles' => 'required|array',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
        ]);

        $orden = OrdenCompra::create([
            'proveedor_id' => $request->proveedor_id,
            'numero_orden' => $request->numero_orden,
            'estado' => 'solicitado'
        ]);

        foreach ($request->detalles as $detalle) {
            DetalleOrdenCompra::create([
                'orden_compra_id' => $orden->id,
                'producto_id' => $detalle['producto_id'],
                'cantidad' => $detalle['cantidad'],
                'precio_compra' => 0,
                'subtotal' => 0,
            ]);
        }

        return redirect()->route('ordenes.index')->with('success', 'Orden de Compra creada exitosamente.');
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

// Método para generar un número de orden único
private function generateOrderNumber()
{
    $ultimoNumeroOrden = OrdenCompra::max('numero_orden') ?? 0;
    return $ultimoNumeroOrden + 1;
}

public function exportarPdf($id)
{
    $orden = OrdenCompra::with(['proveedor', 'detalles.producto'])->findOrFail($id);

    // Cargar una vista específica para el PDF y pasarle los datos
    $pdf = \PDF::loadView('ordenes.pdf', compact('orden'));

    // Descargar el archivo PDF
    return $pdf->stream('orden_compra_' . $orden->numero_orden . '.pdf');
}

}
