<?php

namespace App\Http\Controllers;

use App\Models\MetodosPago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\MetodosPagoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class MetodosPagoController extends Controller
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
        $metodosPagos = MetodosPago::where('estado', true)->paginate(15);

        return view('metodos-pago.index', compact('metodosPagos'))
            ->with('i', ($request->input('page', 1) - 1) * $metodosPagos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $metodosPago = new MetodosPago();

        return view('metodos-pago.create', compact('metodosPago'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MetodosPagoRequest $request): RedirectResponse
    {
        // Validar que el nombre sea único en la tabla
        $request->validate([
            'nombre' => 'required|string|max:255|unique:metodos_pagos,nombre',
        ], [
            'nombre.unique' => 'El nombre del método de pago ya existe.',
        ]);

        MetodosPago::create($request->all());

        return Redirect::route('metodos-pagos.index')
            ->with('success', 'Método de pago creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $metodosPago = MetodosPago::find($id);

        return view('metodos-pago.show', compact('metodosPago'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $metodosPago = MetodosPago::find($id);

        return view('metodos-pago.edit', compact('metodosPago'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MetodosPago $metodosPago): RedirectResponse
    {
        // Validar que el nombre no se duplique, exceptuando el registro actual
        $request->validate([
            'nombre' => 'required|string|max:255|unique:metodos_pagos,nombre,' . $metodosPago->id,
        ], [
            'nombre.unique' => 'El nombre del método de pago ya existe.',
        ]);

        $metodosPago->update($request->all());

        return Redirect::route('metodos-pagos.index')
            ->with('success', 'Método de pago actualizado exitosamente.');
    }


    public function destroy($id): RedirectResponse
    {
            $metodosPagos = MetodosPago::find($id);
        if ($metodosPagos) {
            $metodosPagos->estado = false;
            $metodosPagos->save();
        }

        return Redirect::route('metodos-pagos.index')
            ->with('success', 'Metodo Pago eliminado exitosamente');
    }
}
