<?php

namespace App\Http\Controllers;

use App\Models\PagosProveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PagosProveedorRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PagosProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $pagosProveedors = PagosProveedor::paginate();

        return view('pagos-proveedor.index', compact('pagosProveedors'))
            ->with('i', ($request->input('page', 1) - 1) * $pagosProveedors->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $pagosProveedor = new PagosProveedor();

        return view('pagos-proveedor.create', compact('pagosProveedor'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PagosProveedorRequest $request): RedirectResponse
    {
        PagosProveedor::create($request->validated());

        return Redirect::route('pagos-proveedors.index')
            ->with('success', 'PagosProveedor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $pagosProveedor = PagosProveedor::find($id);

        return view('pagos-proveedor.show', compact('pagosProveedor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $pagosProveedor = PagosProveedor::find($id);

        return view('pagos-proveedor.edit', compact('pagosProveedor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PagosProveedorRequest $request, PagosProveedor $pagosProveedor): RedirectResponse
    {
        $pagosProveedor->update($request->validated());

        return Redirect::route('pagos-proveedors.index')
            ->with('success', 'PagosProveedor updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        PagosProveedor::find($id)->delete();

        return Redirect::route('pagos-proveedors.index')
            ->with('success', 'PagosProveedor deleted successfully');
    }
}
