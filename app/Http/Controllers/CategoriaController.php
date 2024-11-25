<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CategoriaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CategoriaController extends Controller
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
    // Especifica 15 elementos por página
    $categorias = Categoria::where('estado', true)->paginate(15);

    return view('categoria.index', compact('categorias'))
        ->with('i', ($request->input('page', 1) - 1) * 15); // Ajusta el cálculo para 15 elementos por página
}

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categoria = new Categoria();

        return view('categoria.create', compact('categoria'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación que asegura que el nombre sea único
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:255',
            'sin_stock' => 'nullable|boolean', // Validación para sin_stock
        ], [
            'nombre.unique' => 'El nombre de la categoría ya existe. Por favor, elija un nombre diferente.',
        ]);

        Categoria::create($request->all());

        return Redirect::route('categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $categoria = Categoria::find($id);

        return view('categoria.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $categoria = Categoria::find($id);

        return view('categoria.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        // Validación que asegura que el nombre no se duplique excepto para la categoría actual
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'required|boolean',
            'sin_stock' => 'required|boolean',
        ], [
            'nombre.unique' => 'El nombre de la categoría ya existe. Por favor, elija un nombre diferente.',
        ]);

        $categoria->update($request->all());

        return Redirect::route('categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy($id): RedirectResponse
    {
        // Actualizar el estado en lugar de eliminar físicamente
        $categoria = Categoria::find($id);
        $categoria->estado = false;
        $categoria->save();

        return Redirect::route('categorias.index')
            ->with('success', 'Categoria borrada exitosamente');
    }
}
