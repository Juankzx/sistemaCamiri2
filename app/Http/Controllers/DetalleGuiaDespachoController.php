<?php

namespace App\Http\Controllers;

use App\Models\DetalleGuiaDespacho;
use App\Models\GuiaDespacho;
use App\Models\Producto;
use Illuminate\Http\Request;

class DetalleGuiaDespachoController extends Controller
{
    public function index($guiaDespachoId)
    {
        $guiaDespacho = GuiaDespacho::with('detalles.producto')->findOrFail($guiaDespachoId);
        return view('detalles_guias_despacho.index', compact('guiaDespacho'));
    }

    public function create($guiaDespachoId)
    {
        $guiaDespacho = GuiaDespacho::findOrFail($guiaDespachoId);
        $productos = Producto::all();
        return view('detalles_guias_despacho.create', compact('guiaDespacho', 'productos'));
    }

    public function store(Request $request, $guiaDespachoId)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad_entregada' => 'required|integer|min:1',
            'precio_compra' => 'required|numeric|min:0',
        ]);

        $guiaDespacho = GuiaDespacho::findOrFail($guiaDespachoId);

        $subtotal = $request->cantidad_entregada * $request->precio_compra;

        DetalleGuiaDespacho::create([
            'guia_despacho_id' => $guiaDespachoId,
            'producto_id' => $request->producto_id,
            'cantidad_entregada' => $request->cantidad_entregada,
            'precio_compra' => $request->precio_compra,
            'subtotal' => $subtotal,
        ]);

        // Actualizar el total de la guía de despacho
        $guiaDespacho->total += $subtotal;
        $guiaDespacho->save();

        return redirect()->route('detalles_guias_despacho.index', $guiaDespachoId)->with('success', 'Detalle de la Guía de Despacho agregado correctamente.');
    }

    public function edit($id)
    {
        $detalle = DetalleGuiaDespacho::findOrFail($id);
        $productos = Producto::all();
        return view('detalles_guias_despacho.edit', compact('detalle', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad_entregada' => 'required|integer|min:1',
            'precio_compra' => 'required|numeric|min:0',
        ]);

        $detalle = DetalleGuiaDespacho::findOrFail($id);
        $guiaDespacho = $detalle->guiaDespacho;

        $subtotalAnterior = $detalle->subtotal;
        $subtotalNuevo = $request->cantidad_entregada * $request->precio_compra;

        $detalle->update([
            'producto_id' => $request->producto_id,
            'cantidad_entregada' => $request->cantidad_entregada,
            'precio_compra' => $request->precio_compra,
            'subtotal' => $subtotalNuevo,
        ]);

        // Actualizar el total de la guía de despacho
        $guiaDespacho->total += ($subtotalNuevo - $subtotalAnterior);
        $guiaDespacho->save();

        return redirect()->route('detalles_guias_despacho.index', $guiaDespacho->id)->with('success', 'Detalle de la Guía de Despacho actualizado correctamente.');
    }

    public function destroy($id)
    {
        $detalle = DetalleGuiaDespacho::findOrFail($id);
        $guiaDespacho = $detalle->guiaDespacho;

        $guiaDespacho->total -= $detalle->subtotal;
        $guiaDespacho->save();

        $detalle->delete();

        return redirect()->route('detalles_guias_despacho.index', $guiaDespacho->id)->with('success', 'Detalle de la Guía de Despacho eliminado correctamente.');
    }
}
