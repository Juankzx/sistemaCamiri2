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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $metodosPagos = MetodosPago::paginate();

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
        MetodosPago::create($request->validated());

        return Redirect::route('metodos-pagos.index')
            ->with('success', 'MetodosPago created successfully.');
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
    public function update(MetodosPagoRequest $request, MetodosPago $metodosPago): RedirectResponse
    {
        $metodosPago->update($request->validated());

        return Redirect::route('metodos-pagos.index')
            ->with('success', 'MetodosPago updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        MetodosPago::find($id)->delete();

        return Redirect::route('metodos-pagos.index')
            ->with('success', 'MetodosPago deleted successfully');
    }
}
