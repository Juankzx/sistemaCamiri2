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
        $metodoPago = MetodosPago::all();
        return view('pagos.index', compact('pagos', 'metodoPago'));
    }

    public function create()
    {
        $facturas = Factura::with('guiaDespacho.ordenCompra.detalles.producto', 'guiaDespacho.ordenCompra.proveedor')->get();
        $metodoPago = MetodosPago::all();

        return view('pagos.create', compact('facturas', 'metodoPago'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'factura_id' => 'required|exists:facturas,id',
            'metodo_pago_id' => 'required|exists:metodos_pagos,id',
            'monto' => 'required|numeric|min:1',
            'fecha_pago' => 'required|date',
            'numero_transferencia' => 'nullable|string|max:255',
            'estado_pago' => 'required|in:pendiente,pagado'
        ]);
    
        DB::beginTransaction();
        try {
            $pago = Pago::create($validatedData);
    
            if ($pago->estado_pago == 'pagado') {
                $factura = $pago->factura;
                if ($factura->estado_pago == 'pendiente') {
                    $factura->update(['estado_pago' => 'pagado']);
                }
            }
    
            DB::commit();
            return redirect()->route('pagos.index')->with('success', 'Pago registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("Error al registrar el pago: " . $e->getMessage());
            return redirect()->route('pagos.index')->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
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
