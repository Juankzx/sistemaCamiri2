<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Producto;
use Illuminate\Http\Request;

class BodegaController extends Controller
{
    public function index()
    {
        $bodegas = Bodega::all();
        return view('bodegas.index', compact('bodegas'));
    }

    public function create()
    {
        return view('bodegas.create');
    }

    public function store(Request $request)
    {
    $request->validate([
        'nombre' => 'required|string|max:255|unique:bodegas,nombre',
    ], [
        'nombre.unique' => 'El nombre de la bodega ya existe. Por favor, elija un nombre diferente.',
    ]);

    Bodega::create($request->all());
    return redirect()->route('bodegas.index')->with('success', 'Bodega creada exitosamente.');
    }

    public function update(Request $request, Bodega $bodega)
{
    $request->validate([
        'nombre' => 'required|string|max:255|unique:bodegas,nombre,' . $bodega->id,
    ], [
        'nombre.unique' => 'El nombre de la bodega ya existe. Por favor, elija un nombre diferente.',
    ]);

    $bodega->update($request->all());
    return redirect()->route('bodegas.index')->with('success', 'Bodega actualizada exitosamente.');
}

    public function edit(Bodega $bodega)
    {
        return view('bodegas.edit', compact('bodega'));
    }

    public function destroy(Bodega $bodega)
    {
        $bodega->delete();
        return redirect()->route('bodegas.index')->with('success', 'Bodega eliminada exitosamente.');
    }
    public function show($id)
{
    $bodegaGeneral = Bodega::findOrFail($id);
    $productos = Producto::whereHas('inventarios', function ($query) use ($id) {
        $query->where('bodega_id', $id);
    })->with(['inventarios' => function ($query) use ($id) {
        $query->where('bodega_id', $id);
    }])->get();

    return view('bodegas.show', compact('bodegaGeneral', 'productos'));
}

}