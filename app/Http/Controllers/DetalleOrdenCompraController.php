<?php

namespace App\Http\Controllers;

use App\Models\DetalleOrdenCompra;
use App\Models\OrdenCompra;
use App\Models\Producto;
use Illuminate\Http\Request;

class DetalleOrdenCompraController extends Controller
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


    /**
     * Muestra todos los detalles de órdenes de compra.
     */
    public function index()
    {
        $detalles = DetalleOrdenCompra::with('ordenCompra', 'producto')->get();
        return view('detalles.index', compact('detalles'));
    }

    /**
     * Muestra el formulario para crear un nuevo detalle.
     */
    public function create()
    {
        $ordenesCompra = OrdenCompra::all();
        $productos = Producto::all();
        return view('detalles.create', compact('ordenesCompra', 'productos'));
    }

    /**
     * Almacena un nuevo detalle de orden de compra.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'orden_compra_id' => 'required|exists:ordenes_compras,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $detalle = new DetalleOrdenCompra([
            'orden_compra_id' => $validatedData['orden_compra_id'],
            'producto_id' => $validatedData['producto_id'],
            'cantidad' => $validatedData['cantidad'],
            'subtotal' => 0,  // Esto se calculará en una etapa posterior
        ]);
        
        $detalle->save();

        return redirect()->route('ordenes.detalles.index', $validatedData['orden_compra_id'])
            ->with('success', 'Detalle de orden de compra creado con éxito.');
    }

    /**
     * Muestra un detalle de orden de compra específico.
     */
    public function show($id)
    {
        $detalle = DetalleOrdenCompra::with('ordenCompra', 'producto')->findOrFail($id);
        return view('detalles.show', compact('detalle'));
    }

    /**
     * Muestra el formulario para editar un detalle específico.
     */
    public function edit($id)
    {
        $detalle = DetalleOrdenCompra::findOrFail($id);
        $productos = Producto::all();
        return view('detalles.edit', compact('detalle', 'productos'));
    }

    /**
     * Actualiza un detalle de orden de compra específico.
     */
    public function update(Request $request, $id)
    {
        $detalle = DetalleOrdenCompra::findOrFail($id);

        $validatedData = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $detalle->update([
            'producto_id' => $validatedData['producto_id'],
            'cantidad' => $validatedData['cantidad'],
        ]);

        return redirect()->route('ordenes.detalles.index', $detalle->orden_compra_id)
            ->with('success', 'Detalle de orden de compra actualizado con éxito.');
    }

    /**
     * Elimina un detalle de orden de compra específico.
     */
    public function destroy($id)
    {
        $detalle = DetalleOrdenCompra::findOrFail($id);
        $ordenId = $detalle->orden_compra_id;

        $detalle->delete();

        return redirect()->route('ordenes.detalles.index', $ordenId)
            ->with('success', 'Detalle de orden de compra eliminado con éxito.');
    }
}
