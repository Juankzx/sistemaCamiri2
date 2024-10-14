<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function edit()
    {
        $configuracion = Configuracion::first();
        return view('configuracion.edit', compact('configuracion'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre_sistema' => 'required|string|max:255',
            'logo_sistema' => 'nullable|image|max:2048',
        ]);

        $configuracion = Configuracion::first();
        $configuracion->nombre_sistema = $request->nombre_sistema;

        if ($request->hasFile('logo_sistema')) {
            $path = $request->file('logo_sistema')->store('logos', 'public');
            $configuracion->logo_sistema = $path;
        }

        $configuracion->save();

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}
