<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        if (auth()->check() && auth()->user()->hasRole(['bodeguero', 'vendedor'])) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        return $next($request);
    });
}


    public function edit()
{
    $configuracion = Configuracion::first();
    
    if (!$configuracion) {
        // Si no hay una configuración, crear una con valores predeterminados
        $configuracion = new Configuracion();
        $configuracion->nombre_sistema = 'SistemaCamiri'; // Nombre predeterminado
        $configuracion->logo_sistema = 'vendor/adminlte/dist/img/AdminLTELogo.png'; // Logo predeterminado
    }

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
        // Guardar el logo en la carpeta 'public/storage/logos'
        $path = $request->file('logo_sistema')->store('logos', 'public');
        $configuracion->logo_sistema = $path;
    }

    $configuracion->save();

    return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
}

}
