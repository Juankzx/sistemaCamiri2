<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UnidadMedidaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;


class UnidadMedidaController extends Controller
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


    public function index()
{
    // Filtrar solo unidades activas y agregar paginación
    $unidades = UnidadMedida::where('estado', 1)->paginate(15); // Ajusta el número según el tamaño de página que desees
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
        'abreviatura' => 'required|string|max:255|unique:unidad_medida,abreviatura', // Validación de abreviatura única
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
        'nombre' => 'required|unique:unidad_medida,nombre,' . $unidad->id,
        'abreviatura' => 'required|string|max:255|unique:unidad_medida,abreviatura,' . $unidad->id, // Validación de abreviatura única para actualización
    ]);

    $unidad->nombre = $request->nombre;
    $unidad->abreviatura = $request->abreviatura;
    $unidad->save();

    return redirect()->route('unidades.index')->with('success', 'Unidad de medida actualizada con éxito.');
}

    public function destroy($id): RedirectResponse
    {
        // Cambiar el estado de la unidad de medida a inactivo en lugar de eliminarla
    $unidad = UnidadMedida::find($id);
    if ($unidad) {
        $unidad->estado = false; // Cambiar el estado a inactivo
        $unidad->save();
    }
        return redirect()->route('unidades.index')->with('success', 'Unidad de medida eliminada con éxito.');
    }
}
