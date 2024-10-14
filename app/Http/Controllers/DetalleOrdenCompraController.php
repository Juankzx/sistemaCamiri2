<?php

namespace App\Http\Controllers;

use App\Models\DetalleOrdenCompra;
use App\Models\OrdenCompra;
use Illuminate\Http\Request;

class DetalleOrdenCompraController extends Controller
{
    public function index()
    {
        $detalles = DetalleOrdenCompra::all();
        return view('detalles.index', compact('detalles'));
    }

    public function create()
    {
        return view('detalles.create');
    }

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'orden_compra_id' => 'required|exists:ordenes_compras,id',
        'producto_id' => 'required|exists:productos,id',
        'cantidad' => 'required|integer|min:1',
        'precio_compra' => 'required|numeric',
        'inventario_id' => 'required|exists:inventarios,id',  // Asegúrate de validar este campo
    ]);

    DetalleOrdenCompra::create($validatedData);
    return redirect()->route('detalles.index')->with('success', 'Detalle de orden de compra creado con éxito.');
}


    public function show(DetalleOrdenCompra $detalle)
    {
        return view('detalles.show', compact('detalle'));
    }

    public function edit(DetalleOrdenCompra $detalle)
    {
        return view('detalles.edit', compact('detalle'));
    }

    public function update(Request $request, DetalleOrdenCompra $detalle)
    {
        $detalle->update($request->all());
        return redirect()->route('detalles.index')->with('success', 'Detalle de orden de compra actualizado con éxito.');
    }

    public function destroy(DetalleOrdenCompra $detalle)
    {
        $detalle->delete();
        return redirect()->route('detalles.index')->with('success', 'Detalle de orden de compra eliminado con éxito.');
    }
}