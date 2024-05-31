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
    public function index(Request $request): View
    {
        $sucursales = Sucursale::paginate();

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
    public function store(SucursaleRequest $request): RedirectResponse
    {
        Sucursale::create($request->validated());

        return Redirect::route('sucursales.index')
            ->with('success', 'Sucursale created successfully.');
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
    public function update(SucursaleRequest $request, Sucursale $sucursale): RedirectResponse
    {
        $sucursale->update($request->validated());

        return Redirect::route('sucursales.index')
            ->with('success', 'Sucursale updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Sucursale::find($id)->delete();

        return Redirect::route('sucursales.index')
            ->with('success', 'Sucursale deleted successfully');
    }
}
