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
        $pagos = Pago::paginate(15);
        $metodoPago = MetodosPago::all();
        return view('pagos.index', compact('pagos', 'metodoPago'));
    }

    public function create()
    {
        $facturas = Factura::with('guiaDespacho.ordenCompra.detalles.producto', 'guiaDespacho.ordenCompra.proveedor')->get();
        $metodosPago = MetodosPago::all();

        return view('pagos.create', compact('facturas', 'metodosPago'));
    }

    public function store(Request $request)
{
    // Validación de los datos de pago
    $validatedData = $request->validate([
        'factura_id' => 'required|exists:facturas,id',
        'monto' => 'required|numeric|min:0.01',
        'fecha_pago' => 'required|date',
        'estado_pago' => 'required|in:pendiente,pagado',
        'metodo_pago_id' => 'required|exists:metodos_pagos,id',  // Asegurar que el método de pago esté presente
    ]);

    // Crear el registro de pago con los datos validados
    $pago = Pago::create([
        'factura_id' => $validatedData['factura_id'],
        'monto' => $validatedData['monto'],
        'fecha_pago' => $validatedData['fecha_pago'],
        'estado_pago' => $validatedData['estado_pago'],
        'metodo_pago_id' => $validatedData['metodo_pago_id'],  // Asignar el método de pago correcto
    ]);

    // Obtener la factura y actualizar el estado a pagado si corresponde
    $factura = Factura::find($validatedData['factura_id']);
    $factura->update(['estado_pago' => $validatedData['estado_pago']]);

    // Verificar si todas las facturas de la orden de compra están pagadas
    $ordenCompra = $factura->ordenCompra;

    // Comprobar si la orden de compra y sus facturas están pagadas
    if ($ordenCompra) {
        $todasFacturasPagadas = $ordenCompra->facturas->every(function ($factura) {
            return $factura->estado_pago === 'pagado';
        });

        // Si todas las facturas están pagadas, actualizar estado de la orden de compra y guías de despacho
        if ($todasFacturasPagadas) {
            $ordenCompra->update(['estado' => 'entregado']);

            foreach ($ordenCompra->guiasDespacho as $guia) {
                $guia->update(['estado' => 'entregada']);
            }
        }
    }

    return redirect()->route('pagos.index')->with('success', 'Pago registrado y estado de la orden de compra actualizado.');
}


    

    public function show(Pago $pago)
    {
        return view('pagos.show', compact('pago'));
    }

    public function edit(Pago $pago)
    {
        $metodosPago = MetodosPago::all();
        return view('pagos.edit', compact('pago', 'metodosPago'));
    }

    public function update(Request $request, Pago $pago)
    {
        $validatedData = $request->validate([
            'metodo_pago_id' => 'required|exists:metodos_pagos,id',
            'monto' => 'required|numeric|min:1',
            'fecha_pago' => 'required|date',
            'numero_transferencia' => 'nullable|string|max:255',
            'estado_pago' => 'required|in:pendiente,pagado'
        ]);

        $validatedData['estado_pago'] = 'pagado'; // Asegurar que siempre sea "pagado"
        $pago->update($validatedData);

        return redirect()->route('pagos.index')->with('success', 'Pago actualizado con éxito.');
    }

    public function destroy(Pago $pago)
    {
        $pago->delete();
        return redirect()->route('pagos.index')->with('success', 'Pago eliminado con éxito.');
    }
}