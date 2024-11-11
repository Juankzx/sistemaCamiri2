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
        $pagos = Pago::orderBy('created_at', 'desc')->paginate(15);
        $metodoPago = MetodosPago::all();
        return view('pagos.index', compact('pagos', 'metodoPago'));
    }

    public function create()
    {
        $facturas = Factura::with('guiaDespacho.ordenCompra.detalles.producto', 'guiaDespacho.ordenCompra.proveedor')
                       ->where('estado_pago', 'pendiente')
                       ->get();
        $metodosPago = MetodosPago::all();
        return view('pagos.create', compact('facturas', 'metodosPago'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'factura_id' => 'required|exists:facturas,id',
            'monto' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'estado_pago' => 'required|in:pendiente,pagado',
            'metodo_pago_id' => 'required|exists:metodos_pagos,id',
            'numero_transferencia' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            $pago = Pago::create($validatedData);

            // Actualizar estado de la factura
            $factura = Factura::find($validatedData['factura_id']);
            $montoPagado = $factura->pagos()->sum('monto');

            if ($montoPagado >= $factura->monto_total) {
                $factura->update(['estado_pago' => 'pagado']);
            }

            // Actualizar estado de orden y guías de despacho si todas las facturas están pagadas
            $guiaDespacho = $factura->guiaDespacho;
            if ($guiaDespacho && $guiaDespacho->ordenCompra) {
                $ordenCompra = $guiaDespacho->ordenCompra;
                $todasFacturasPagadas = $ordenCompra->facturas->every(function ($factura) {
                    return $factura->estado_pago === 'pagado';
                });

                if ($todasFacturasPagadas) {
                    $ordenCompra->update(['estado' => 'entregado']);
                    $ordenCompra->guiasDespacho()->update(['estado' => 'entregada']);
                }
            }

            DB::commit();
            return redirect()->route('pagos.index')->with('success', 'Pago registrado y estado actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    public function getFacturaDetalles($id)
    {
        $factura = Factura::with('guiaDespacho.detalles.producto', 'guiaDespacho.ordenCompra.proveedor')
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

        DB::beginTransaction();

        try {
            $pago->update($validatedData);

            // Recalcular el estado de la factura
            $factura = $pago->factura;
            $montoPagado = $factura->pagos()->sum('monto');
            $factura->update(['estado_pago' => $montoPagado >= $factura->monto_total ? 'pagado' : 'pendiente']);

            // Recalcular estado de la orden de compra y guías de despacho si corresponde
            $guiaDespacho = $factura->guiaDespacho;
            if ($guiaDespacho && $guiaDespacho->ordenCompra) {
                $ordenCompra = $guiaDespacho->ordenCompra;
                $todasFacturasPagadas = $ordenCompra->facturas->every(function ($factura) {
                    return $factura->estado_pago === 'pagado';
                });

                if ($todasFacturasPagadas) {
                    $ordenCompra->update(['estado' => 'entregado']);
                    $ordenCompra->guiasDespacho()->update(['estado' => 'entregada']);
                }
            }

            DB::commit();
            return redirect()->route('pagos.index')->with('success', 'Pago actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el pago: ' . $e->getMessage());
        }
    }

    public function destroy(Pago $pago)
    {
        DB::beginTransaction();

        try {
            $factura = $pago->factura;
            $pago->delete();

            // Recalcular el estado de la factura
            $montoPagado = $factura->pagos()->sum('monto');
            $factura->update(['estado_pago' => $montoPagado >= $factura->monto_total ? 'pagado' : 'pendiente']);

            DB::commit();
            return redirect()->route('pagos.index')->with('success', 'Pago eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar el pago: ' . $e->getMessage());
        }
    }
}
