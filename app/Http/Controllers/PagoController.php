<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Factura;
use App\Models\MetodosPago;
use App\Models\DetalleGuiaDespachoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index()
{
    // Ordenar los pagos por la fecha de creación en orden descendente
    $pagos = Pago::orderBy('created_at', 'desc') // Ordenar por 'created_at'
                 ->paginate(15);

    // Obtener todos los métodos de pago
    $metodoPago = MetodosPago::all();

    // Retornar la vista con los datos
    return view('pagos.index', compact('pagos', 'metodoPago'));
}

    public function create()
    {
        $facturas = Factura::with('guiaDespacho.ordenCompra.detalles.producto', 'guiaDespacho.ordenCompra.proveedor')
                       ->whereIn('estado_pago', ['pendiente', 'en_proceso'])
                       ->get();
        $metodosPago = MetodosPago::all();

        return view('pagos.create', compact('facturas', 'metodosPago'));
    }

    public function store(Request $request)
{
    // Validación de datos
    $validatedData = $request->validate([
        'factura_id' => 'required|exists:facturas,id',
        'monto' => 'required|numeric|min:0.01',
        'fecha_pago' => 'required|date',
        'estado_pago' => 'required|in:pendiente,pagado',
        'metodo_pago_id' => 'required|exists:metodos_pagos,id',
        'numero_transferencia' => 'nullable|string|max:255'
    ]);

    // Crear el pago
    $pago = Pago::create([
        'factura_id' => $validatedData['factura_id'],
        'monto' => $validatedData['monto'],
        'fecha_pago' => $validatedData['fecha_pago'],
        'estado_pago' => $validatedData['estado_pago'],
        'metodo_pago_id' => $validatedData['metodo_pago_id'],
        'numero_transferencia' => $validatedData['numero_transferencia'] ?? null,
    ]);

    // Actualizar estado de la factura
    $factura = Factura::find($validatedData['factura_id']);
    $factura->update(['estado_pago' => $validatedData['estado_pago']]);

    // Obtener la guía de despacho asociada a la factura
    $guiaDespacho = $factura->guiaDespacho;
    if ($guiaDespacho) {
        // Obtener la orden de compra asociada a la guía de despacho
        $ordenCompra = $guiaDespacho->ordenCompra;

        if ($ordenCompra) {
            // Cargar las facturas de la orden de compra como colección
            $facturas = $ordenCompra->facturas;

            // Verificar si todas las facturas de la colección están pagadas
            $todasFacturasPagadas = $facturas->every(function ($factura) {
                return $factura->estado_pago === 'pagado';
            });

            // Si todas las facturas están pagadas, actualizar la orden y las guías
            if ($todasFacturasPagadas) {
                $ordenCompra->update(['estado' => 'entregado']);

                // Cargar guías de despacho y verificar que no sea null antes de iterar
                $ordenCompra->load('guiasDespacho'); // Asegura que la relación esté cargada
                if ($ordenCompra->guiasDespacho) {
                    foreach ($ordenCompra->guiasDespacho as $guia) {
                        $guia->update(['estado' => 'entregada']);
                    }
                }
            }
        }
    }

    return redirect()->route('pagos.index')->with('success', 'Pago registrado y estado de la orden de compra actualizado.');
}
  

    public function getFacturaDetalles($id)
{
    $factura = Factura::with('guiaDespacho.detallesGuiaDespacho.producto', 'guiaDespacho.ordenCompra.proveedor')
                      ->findOrFail($id);

    return response()->json($factura);
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