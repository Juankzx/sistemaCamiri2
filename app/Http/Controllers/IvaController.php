<?php

namespace App\Http\Controllers;

use App\Models\Iva;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\IvaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class IvaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $ivas = Iva::paginate();

        return view('iva.index', compact('ivas'))
            ->with('i', ($request->input('page', 1) - 1) * $ivas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $iva = new Iva();

        return view('iva.create', compact('iva'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IvaRequest $request): RedirectResponse
    {
        Iva::create($request->validated());

        return Redirect::route('ivas.index')
            ->with('success', 'Iva created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $iva = Iva::find($id);

        return view('iva.show', compact('iva'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $iva = Iva::find($id);

        return view('iva.edit', compact('iva'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IvaRequest $request, Iva $iva): RedirectResponse
    {
        $iva->update($request->validated());

        return Redirect::route('ivas.index')
            ->with('success', 'Iva updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Iva::find($id)->delete();

        return Redirect::route('ivas.index')
            ->with('success', 'Iva deleted successfully');
    }
}
