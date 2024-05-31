<?php

namespace App\Http\Controllers;

use App\Models\DetallesVentum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\DetallesVentumRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DetallesVentumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $detallesVenta = DetallesVentum::paginate();

        return view('detalles-ventum.index', compact('detallesVenta'))
            ->with('i', ($request->input('page', 1) - 1) * $detallesVenta->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $detallesVentum = new DetallesVentum();

        return view('detalles-ventum.create', compact('detallesVentum'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DetallesVentumRequest $request): RedirectResponse
    {
        DetallesVentum::create($request->validated());

        return Redirect::route('detalles-venta.index')
            ->with('success', 'DetallesVentum created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $detallesVentum = DetallesVentum::find($id);

        return view('detalles-ventum.show', compact('detallesVentum'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $detallesVentum = DetallesVentum::find($id);

        return view('detalles-ventum.edit', compact('detallesVentum'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DetallesVentumRequest $request, DetallesVentum $detallesVentum): RedirectResponse
    {
        $detallesVentum->update($request->validated());

        return Redirect::route('detalles-venta.index')
            ->with('success', 'DetallesVentum updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        DetallesVentum::find($id)->delete();

        return Redirect::route('detalles-venta.index')
            ->with('success', 'DetallesVentum deleted successfully');
    }
}
