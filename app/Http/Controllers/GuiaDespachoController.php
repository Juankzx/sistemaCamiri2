<?php

namespace App\Http\Controllers;

use App\Models\GuiaDespacho;
use App\Models\OrdenCompra;
use Illuminate\Http\Request;

class GuiaDespachoController extends Controller
{
    public function index()
    {
        $guias = GuiaDespacho::all();
        $ordenCompra = OrdenCompra::all();
        return view('guias-despacho.index', compact('guias', 'ordenCompra'));
    }

    public function create()
    {
        $ordenCompra = OrdenCompra::all();
        return view('guias-despacho.create', compact('ordenCompra'));
    }

    public function store(Request $request)
{
    $request->validate([
        'numero_guia' => 'required|string|unique:guias_despacho,numero_guia',
        'fecha_entrega' => 'required|date',
        'orden_compra_id' => 'required|exists:ordenes_compras,id',
        'estado' => 'required|in:emitida,en_transito,entregada',
    ]);

    try {
        GuiaDespacho::create($request->all());
        return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho creada con éxito.');
    } catch (\Illuminate\Database\QueryException $e) {
        // Aquí puedes verificar el código de error y personalizar el mensaje si es necesario
        return redirect()->back()->withErrors('Número de guía duplicado, por favor ingresa un número diferente.')->withInput();
    }
}

    public function show(GuiaDespacho $guia)
    {
        return view('guias-despacho.show', compact('guia'));
    }

    public function edit(GuiaDespacho $guia)
    {
        return view('guias-despacho.edit', compact('guia'));
    }

    public function update(Request $request, GuiaDespacho $guia)
    {
        $guia->update($request->all());
        return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho actualizada con éxito.');
    }

    public function destroy(GuiaDespacho $guia)
    {
        $guia->delete();
        return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho eliminada con éxito.');
    }
}
