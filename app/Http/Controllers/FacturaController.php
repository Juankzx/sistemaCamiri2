<?php

namespace App\Http\Controllers;

use App\Models\GuiaDespacho;

use App\Models\Factura;
use App\Models\MetodosPago;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::all();
        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $guiasDespacho = GuiaDespacho::all();
        return view('facturas.create', compact('guiasDespacho'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'guia_despacho_id' => 'required|exists:guias_despacho,id',
            'numero_factura' => 'required|string|unique:facturas,numero_factura',
            'fecha_factura' => 'required|date',
        ]);

        $guiaDespacho = GuiaDespacho::with('ordenCompra.detalles')->findOrFail($validatedData['guia_despacho_id']);
        $totalFactura = $guiaDespacho->ordenCompra->detalles->sum(function ($detalle) {
            return $detalle->precio_compra * $detalle->cantidad;
        });

        $factura = new Factura();
        $factura->guia_despacho_id = $validatedData['guia_despacho_id'];
        $factura->numero_factura = $validatedData['numero_factura'];
        $factura->fecha_factura = $validatedData['fecha_factura'];
        $factura->total_factura = $totalFactura;
        $factura->estado_pago = 'pendiente';  // Estado inicial
        $factura->save();

        return redirect()->route('facturas.index')->with('success', 'Factura creada exitosamente.');
    }

    public function show(Factura $factura)
    {
        return view('facturas.show', compact('factura'));
    }

    public function edit(Factura $factura)
    {
        return view('facturas.edit', compact('factura'));
    }

    public function update(Request $request, Factura $factura)
    {
        $factura->update($request->all());
        return redirect()->route('facturas.index')->with('success', 'Factura actualizada con éxito.');
    }

    public function destroy(Factura $factura)
    {
        $factura->delete();
        return redirect()->route('facturas.index')->with('success', 'Factura eliminada con éxito.');
    }

    public function getDetalles($id)
{
    $factura = Factura::with('guiaDespacho.ordenCompra.proveedor')->findOrFail($id);
    $proveedor = $factura->guiaDespacho->ordenCompra->proveedor;

    return response()->json([
        'factura' => $factura,
        'proveedor' => $proveedor
    ]);
}

}
