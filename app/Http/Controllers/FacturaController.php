<?php

namespace App\Http\Controllers;

use App\Models\GuiaDespacho;

use App\Models\OrdenCompra;
use App\Models\Factura;
use App\Models\MetodosPago;
use App\Models\DetalleGuiaDespachoPago;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['guiaDespacho', 'ordenCompra'])
        ->orderBy('created_at', 'desc') // Ordenar por la fecha de creación en orden descendente
        ->paginate(15); 
        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $guiasDespacho = GuiaDespacho::whereIn('estado', ['emitida', 'en_transito'])->get();
        
        return view('facturas.create', compact('guiasDespacho'));
    }


    public function store(Request $request)
{
    // Validar los datos que llegan en la solicitud
    $validatedData = $request->validate([
        'guia_despacho_id' => 'required|exists:guias_despacho,id', // Validar que la guía de despacho existe
        'numero_factura' => 'required|string|unique:facturas,numero_factura', // Número de factura debe ser único
        'fecha_emision' => 'required|date', // La fecha de emisión debe ser una fecha válida
        'monto_total' => 'required|numeric|min:0', // Monto total debe ser numérico y mayor o igual a 0
        'estado_pago' => 'required|in:pendiente,pagado', // Estado de pago (pendiente o pagado)
    ]);

    try {
        // Crear la nueva factura
        $factura = Factura::create([
            'guia_despacho_id' => $validatedData['guia_despacho_id'], // Asociar la factura con la guía de despacho
            'numero_factura' => $validatedData['numero_factura'], // Número de factura único
            'fecha_emision' => $validatedData['fecha_emision'], // Fecha de emisión
            'monto_total' => $validatedData['monto_total'], // Monto total de la factura
            'estado_pago' => $validatedData['estado_pago'], // Estado de pago
        ]);

        // Opcional: Actualizar el estado de la guía de despacho si se requiere algún cambio
        $guiaDespacho = GuiaDespacho::findOrFail($validatedData['guia_despacho_id']);
        if ($factura->estado_pago === 'pagado') {
            $guiaDespacho->update(['estado' => 'entregada']); // Actualiza el estado de la guía a "entregada" si la factura está pagada
        }

        return redirect()->route('facturas.index')->with('success', 'Factura creada exitosamente.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error al crear la factura: ' . $e->getMessage());
    }
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
    $guiaDespacho = GuiaDespacho::with('detallesGuiaDespacho.producto', 'ordenCompra.proveedor')
                                 ->findOrFail($id);

    return response()->json([
        'detalles' => $guiaDespacho->detallesGuiaDespacho,
        'proveedor' => $guiaDespacho->ordenCompra->proveedor
    ]);
}



}