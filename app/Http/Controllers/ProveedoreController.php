<?php

namespace App\Http\Controllers;

use App\Models\Proveedore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProveedoreRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProveedoreController extends Controller
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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Filtrar solo proveedores activos
    $proveedores = Proveedore::where('estado', true)->paginate(15);

        return view('proveedore.index', compact('proveedores'))
            ->with('i', ($request->input('page', 1) - 1) * $proveedores->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $proveedore = new Proveedore();

        return view('proveedore.create', compact('proveedore'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:proveedores,nombre',
            'rut' => [
                'required',
                'string',
                'max:20',
                'unique:proveedores,rut',
                'regex:/^(\d{1,2}\.\d{3}\.\d{3}-[0-9kK]|\d{7,8}-[0-9kK])$/'
            ], // Validación para el formato con puntos y sin puntos
            'razon_social' => 'nullable|string|max:255', // Nueva validación para razón social
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:255',
        ], [
            'rut.unique' => 'El RUT ingresado ya está registrado.',
            'rut.regex' => 'El formato del RUT no es válido. Debe ser 11.111.111-1 o 11111111-1.',
            'telefono.regex' => 'El teléfono solo puede contener números.',
        ]);
    
        Proveedore::create($request->all());
    
        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado con éxito.');
    }
    


    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $proveedore = Proveedore::find($id);

        return view('proveedore.show', compact('proveedore'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $proveedore = Proveedore::find($id);

        return view('proveedore.edit', compact('proveedore'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedore $proveedore): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rut' => [
                'required',
                'string',
                'max:20',
                'unique:proveedores,rut,' . $proveedore->id,
                'regex:/^(\d{1,2}\.\d{3}\.\d{3}-[0-9kK]|\d{7,8}-[0-9kK])$/'
            ], // Validar formato de RUT e ignorar unicidad para el actual proveedor
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:255',
        ], [
            'rut.unique' => 'El RUT ingresado ya está registrado para otro proveedor.',
            'rut.regex' => 'El formato del RUT no es válido. Debe ser 11.111.111-1 o 11111111-1.',
            'telefono.regex' => 'El teléfono solo puede contener números.',
        ]);
    
        $proveedore->update($request->all());
    
        return Redirect::route('proveedores.index')->with('success', 'Proveedor actualizado con éxito.');
    }
    

    public function destroy($id): RedirectResponse
    {
        // Cambiar el estado a inactivo en lugar de eliminar
    $proveedor = Proveedore::find($id);
    if ($proveedor) {
        $proveedor->estado = false;
        $proveedor->save();
    }

        return Redirect::route('proveedores.index')
            ->with('success', 'Proveedor eliminado exitosamente');
    }
}
