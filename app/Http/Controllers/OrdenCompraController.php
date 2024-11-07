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
        $ordenes = OrdenCompra::with('proveedor', 'detalles.producto')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('ordenes.index', compact('ordenes'));
    }

    public function create()
    {
        $proveedores = Proveedore::all();
        $productos = Producto::all();  // Se asume que todos los productos están disponibles
        $ultimoNumeroOrden = OrdenCompra::max('numero_orden') ?? 0;
        $nuevoNumeroOrden = $ultimoNumeroOrden + 1;

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
                'precio_compra' => 0,  // Se establecerá en la Guía de Despacho
                'subtotal' => 0,       // Se actualizará más tarde
            ]);
        }

        return redirect()->route('ordenes.index')->with('success', 'Orden de Compra creada exitosamente.');
    }
    public function destroy($id)
{
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
}
