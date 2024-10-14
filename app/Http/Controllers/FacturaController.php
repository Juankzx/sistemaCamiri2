<?php

namespace App\Http\Controllers;

use App\Models\GuiaDespacho;

use App\Models\OrdenCompra;
use App\Models\Factura;
use App\Models\MetodosPago;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['guiaDespacho', 'ordenCompra'])->paginate(15); 
        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $guiasDespacho = GuiaDespacho::all();
        return view('facturas.create', compact('guiasDespacho'));
    }


    public function store(Request $request)
{
    // Validar los datos de la factura
    $validatedData = $request->validate([
        'guia_despacho_id' => 'required|exists:guias_despacho,id',
        'numero_factura' => 'required|unique:facturas,numero_factura',
        'fecha_factura' => 'required|date',
        'total_factura' => 'required|numeric',
        'estado_pago' => 'required|in:pendiente,pagado',
        'orden_compra_id' => 'required|exists:ordenes_compras,id',
    ]);

    // Crear la factura
    $factura = Factura::create($validatedData);

    // Actualizar estado de la Orden de Compra a "en_transito"
    $ordenCompra = OrdenCompra::find($validatedData['orden_compra_id']);
    $ordenCompra->update(['estado' => 'en_transito']);

    return redirect()->route('facturas.index')->with('success', 'Factura creada y estado de la orden de compra actualizado a "en_transito".');
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