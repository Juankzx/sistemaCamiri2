<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    public function index()
    {
        $unidades = UnidadMedida::all();
        return view('unidades.index', compact('unidades'));
    }

    public function create()
    {
        return view('unidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:unidad_medida,nombre',
            'abreviatura' => 'required|string|max:255'
        ]);

        $unidad = new UnidadMedida();
        $unidad->nombre = $request->nombre;
        $unidad->abreviatura = $request->abreviatura;
        $unidad->save();

        return redirect()->route('unidades.index')->with('success', 'Unidad de medida creada con éxito.');
    }

    public function show(UnidadMedida $unidad)
    {
        return view('unidades.show', compact('unidad'));
    }

    public function edit(UnidadMedida $unidad)
    {
        return view('unidades.edit', compact('unidad'));
    }

    public function update(Request $request, UnidadMedida $unidad)
    {
        $request->validate([
            'nombre' => 'required|unique:unidad_medida,nombre,' . $unidad->id
        ]);

        $unidad->nombre = $request->nombre;
        $unidad->save();

        return redirect()->route('unidades.index')->with('success', 'Unidad de medida actualizada con éxito.');
    }

    public function destroy(UnidadMedida $unidad)
    {
        $unidad->delete();
        return redirect()->route('unidades.index')->with('success', 'Unidad de medida eliminada con éxito.');
    }
}
