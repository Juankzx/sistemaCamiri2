<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Factura;  
use App\Models\MetodosPago; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::all();
        $facturas = Factura::all();
        $metodosPago = MetodosPago::all();
        return view('pagos.index', compact('pagos', 'facturas', 'metodosPago'));
        
    }

    public function create()
    {
        $facturas = Factura::all();
        $metodosPago = MetodosPago::all();
        return view('pagos.create', compact('facturas', 'metodosPago'));
    }

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'factura_id' => 'required|exists:facturas,id',
        'metodo_pago_id' => 'required|exists:metodos_pagos,id',
        'monto' => 'required|numeric',
        'fecha_pago' => 'required|date',
        'estado_pago' => 'required|in:pendiente,pagado',
        'numero_transferencia' => 'nullable|string'
    ]);

    DB::transaction(function () use ($validatedData, $request) {
        // Crear el pago
        $pago = Pago::create($validatedData);

        // Verificar si el estado del pago es 'pagado'
        if ($validatedData['estado_pago'] === 'pagado') {
            // Obtener la factura asociada
            $factura = Factura::find($validatedData['factura_id']);

            // Cambiar el estado de la factura a 'pagado' si aún está en 'pendiente'
            if ($factura && $factura->estado_pago === 'pendiente') {
                $factura->update(['estado_pago' => 'pagado']);
            }
        }
    });

    return redirect()->route('pagos.index')->with('success', 'Pago registrado con éxito y estado de factura actualizado.');
}


    public function show(Pago $pago)
    {
        return view('pagos.show', compact('pago'));
    }

    public function edit(Pago $pago)
    {
        return view('pagos.edit', compact('pago'));
    }

    public function update(Request $request, Pago $pago)
    {
        $pago->update($request->all());
        return redirect()->route('pagos.index')->with('success', 'Pago actualizado con éxito.');
    }

    public function destroy(Pago $pago)
    {
        $pago->delete();
        return redirect()->route('pagos.index')->with('success', 'Pago eliminado con éxito.');
    }
}