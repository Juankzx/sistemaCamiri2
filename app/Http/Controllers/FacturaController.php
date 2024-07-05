<?php

namespace App\Http\Controllers;

use App\Models\GuiaDespacho;

use App\Models\Factura;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::all();
        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $guias_despacho = GuiaDespacho::all();
        return view('facturas.create', compact('guias_despacho'));
    }


    public function store(Request $request)
    {
        Factura::create($request->all());
        return redirect()->route('facturas.index')->with('success', 'Factura creada con éxito.');
    }

    public function show(Factura $factura)
    {
        return view('facturas.show', compact('factura'));
    }

    public function edit(Factura $factura)
    {
        return view('facturas.edit', compact('factura'));
    }

    public function update(Request $request, Factura $factura)
    {
        $factura->update($request->all());
        return redirect()->route('facturas.index')->with('success', 'Factura actualizada con éxito.');
    }

    public function destroy(Factura $factura)
    {
        $factura->delete();
        return redirect()->route('facturas.index')->with('success', 'Factura eliminada con éxito.');
    }
}
