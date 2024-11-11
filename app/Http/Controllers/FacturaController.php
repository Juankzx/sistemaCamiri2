<?php

namespace App\Http\Controllers;

use App\Models\GuiaDespacho;
use App\Models\Factura;
use App\Models\Proveedore;
use App\Models\OrdenCompra;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['guiaDespacho.ordenCompra.proveedor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $guiasDespacho = GuiaDespacho::whereIn('estado', ['emitida', 'en_transito'])->get();
        $proveedor = Proveedore::all();
        $ordenCompra = OrdenCompra::all();
        return view('facturas.create', compact('guiasDespacho', 'ordenCompra'));
    }

    public function store(Request $request)
    {
        // Validación de los datos recibidos
        $validatedData = $request->validate([
            'guia_despacho_id' => 'required|exists:guias_despacho,id',
            'numero_factura' => 'required|string|unique:facturas,numero_factura',
            'fecha_emision' => 'required|date',
            'monto_total' => 'required|numeric|min:0',
            'estado_pago' => 'required|in:pendiente,pagado',
        ]);

        try {
            // Crear la factura y asociarla a la guía de despacho
            $factura = Factura::create($validatedData);

            // Actualizar el estado de la guía de despacho si el estado de pago es "pagado"
            if ($factura->estado_pago === 'pagado') {
                $guiaDespacho = GuiaDespacho::findOrFail($validatedData['guia_despacho_id']);
                $guiaDespacho->update(['estado' => 'entregada']);
            }

            return redirect()->route('facturas.index')->with('success', 'Factura creada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

    public function show(Factura $factura)
    {
        $factura->load('guiaDespacho.ordenCompra.proveedor');
        return view('facturas.show', compact('factura'));
    }

    public function edit(Factura $factura)
    {
        return view('facturas.edit', compact('factura'));
    }

    public function update(Request $request, Factura $factura)
    {
        // Validación de los datos de actualización
        $validatedData = $request->validate([
            'guia_despacho_id' => 'required|exists:guias_despacho,id',
            'numero_factura' => 'required|string|unique:facturas,numero_factura,' . $factura->id,
            'fecha_emision' => 'required|date',
            'monto_total' => 'required|numeric|min:0',
            'estado_pago' => 'required|in:pendiente,pagado',
        ]);

        // Actualizar los datos de la factura y la guía de despacho si el estado de pago cambia
        $factura->update($validatedData);

        if ($validatedData['estado_pago'] === 'pagado' && $factura->guiaDespacho->estado !== 'entregada') {
            $factura->guiaDespacho->update(['estado' => 'entregada']);
        }

        return redirect()->route('facturas.index')->with('success', 'Factura actualizada con éxito.');
    }

    public function destroy(Factura $factura)
    {
        $factura->delete();
        return redirect()->route('facturas.index')->with('success', 'Factura eliminada con éxito.');
    }

    public function getDetalles($id)
    {
        // Cargar los detalles de la guía de despacho asociada y el proveedor de la orden de compra
        $guiaDespacho = GuiaDespacho::with('detalles.producto', 'ordenCompra.proveedor')
                                     ->findOrFail($id);

        return response()->json([
            'detalles' => $guiaDespacho->detalles,
            'proveedor' => $guiaDespacho->ordenCompra ? $guiaDespacho->ordenCompra->proveedor : 'Proveedor no disponible',
        ]);
    }
}
