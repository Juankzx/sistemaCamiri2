<?php

namespace App\Http\Controllers;

use App\Models\GuiaDespacho;
use App\Models\OrdenCompra;
use App\Models\Producto;
use App\Models\MetodosPago;
use Illuminate\Http\Request;

class GuiaDespachoController extends Controller
{
    public function index()
    {
        $guias = GuiaDespacho::all();
        $ordenCompra = OrdenCompra::where('estado', 'solicitado')->get();
        return view('guias-despacho.index', compact('guias', 'ordenCompra'));
    }

    public function create()
    {
        $ordenCompra = OrdenCompra::all();
        $productos = Producto::all();
        return view('guias-despacho.create', compact('ordenCompra', 'productos'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'numero_guia' => 'required|string|max:255',
            'fecha_entrega' => 'required|date',
            'orden_compra_id' => 'required|exists:ordenes_compras,id',
            // 'estado' => 'required|string|max:255', // Ya no es necesario validar esto
        ]);
    
        GuiaDespacho::create([
            'numero_guia' => $validatedData['numero_guia'],
            'fecha_entrega' => $validatedData['fecha_entrega'],
            'orden_compra_id' => $validatedData['orden_compra_id'],
            'estado' => 'emitida', // Establecer el estado inicial
        ]);
    
        return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho creada con éxito.');
    }

    public function show($id)
    {
        $guia = GuiaDespacho::with('ordenCompra.proveedor', 'ordenCompra.detalles.producto')->findOrFail($id);
        return view('guias-despacho.show', compact('guia'));
    }

    public function edit(GuiaDespacho $guia)
    {
        return view('guias-despacho.edit', compact('guia'));
    }

    public function update(Request $request, GuiaDespacho $guia)
    {
        $guia->update($request->all());
        return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho actualizada con éxito.');
    }

    public function destroy(GuiaDespacho $guia)
    {
        $guia->delete();
        return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho eliminada con éxito.');
    }
    
    public function getOrdenCompraDetails($id)
    {
        $ordenCompra = OrdenCompra::with('detalles.producto')->findOrFail($id);
        return response()->json($ordenCompra);
    }

    public function getDetalles($id)
    {
        $guiaDespacho = GuiaDespacho::with('ordenCompra.detalles.producto', 'ordenCompra.proveedor')->findOrFail($id);
        $detalles = $guiaDespacho->ordenCompra->detalles;
        $proveedor = $guiaDespacho->ordenCompra->proveedor;

        return response()->json([
            'detalles' => $detalles,
            'proveedor' => $proveedor
        ]);
    }
}
