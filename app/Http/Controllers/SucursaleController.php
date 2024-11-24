<?php

namespace App\Http\Controllers;

use App\Models\Sucursale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\SucursaleRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SucursaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        if (auth()->check() && auth()->user()->hasRole(['bodeguero', 'vendedor'])) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        return $next($request);
    });
}


    public function index(Request $request): View
    {
        // Solo mostrar sucursales activas
        $sucursales = Sucursale::where('estado', true)->paginate(15);

        return view('sucursale.index', compact('sucursales'))
            ->with('i', ($request->input('page', 1) - 1) * $sucursales->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $sucursale = new Sucursale();

        return view('sucursale.create', compact('sucursale'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'nombre' => 'required|string|max:255|unique:sucursales,nombre',
        'direccion' => 'required|string|max:255'
    ], [
        'nombre.unique' => 'El nombre de la sucursal ya existe. Por favor, elija un nombre diferente.'
    ]);

    Sucursale::create($request->all());

    return redirect()->route('sucursales.index')
        ->with('success', 'Sucursal creada exitosamente.');
}


    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $sucursale = Sucursale::find($id);

        return view('sucursale.show', compact('sucursale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $sucursale = Sucursale::find($id);

        return view('sucursale.edit', compact('sucursale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sucursale $sucursale): RedirectResponse
{
    $request->validate([
        'nombre' => 'required|string|max:255|unique:sucursales,nombre,' . $sucursale->id,
        'direccion' => 'nullable|string|max:255'
    ], [
        'nombre.unique' => 'El nombre de la sucursal ya existe. Por favor, elija un nombre diferente.'
    ]);

    $sucursale->update($request->all());

    return redirect()->route('sucursales.index')
        ->with('success', 'Sucursal actualizada exitosamente.');
}

    public function destroy($id): RedirectResponse
    {
        // Cambiar el estado a false en lugar de eliminar
        $sucursal = Sucursale::find($id);
        if ($sucursal) {
            $sucursal->estado = false;
            $sucursal->save();
        }

        return Redirect::route('sucursales.index')
            ->with('success', 'Sucursal borrada exitosamente');
    }
}
