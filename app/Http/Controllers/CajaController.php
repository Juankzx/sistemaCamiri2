<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CajaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CajaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $cajas = Caja::paginate();

        return view('caja.index', compact('cajas'))
            ->with('i', ($request->input('page', 1) - 1) * $cajas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $caja = new Caja();

        return view('caja.create', compact('caja'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CajaRequest $request): RedirectResponse
    {
        Caja::create($request->validated());

        return Redirect::route('cajas.index')
            ->with('success', 'Caja created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $caja = Caja::find($id);

        return view('caja.show', compact('caja'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $caja = Caja::find($id);

        return view('caja.edit', compact('caja'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CajaRequest $request, Caja $caja): RedirectResponse
    {
        $caja->update($request->validated());

        return Redirect::route('cajas.index')
            ->with('success', 'Caja updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Caja::find($id)->delete();

        return Redirect::route('cajas.index')
            ->with('success', 'Caja deleted successfully');
    }
}
