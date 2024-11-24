<?php

namespace App\Http\Controllers;

use App\Models\DetalleGuiaDespacho;
use App\Models\GuiaDespacho;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetalleGuiaDespachoController extends Controller
{
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        if (auth()->check() && auth()->user()->hasRole('vendedor')) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        return $next($request);
    });
}


    public function index($guiaDespachoId)
    {
        $guiaDespacho = GuiaDespacho::with('detalles.producto')->findOrFail($guiaDespachoId);
        return view('detalles_guias_despacho.index', compact('guiaDespacho'));
    }

    public function create($guiaDespachoId)
    {
        $guiaDespacho = GuiaDespacho::findOrFail($guiaDespachoId);
        $productos = Producto::all();
        return view('detalles_guias_despacho.create', compact('guiaDespacho', 'productos'));
    }

    public function store(Request $request, $guiaDespachoId)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad_entregada' => 'required|integer|min:1',
            'precio_compra' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $guiaDespacho = GuiaDespacho::findOrFail($guiaDespachoId);
            $subtotal = $request->cantidad_entregada * $request->precio_compra;

            DetalleGuiaDespacho::create([
                'guia_despacho_id' => $guiaDespachoId,
                'producto_id' => $request->producto_id,
                'cantidad_entregada' => $request->cantidad_entregada,
                'precio_compra' => $request->precio_compra,
                'subtotal' => $subtotal,
            ]);

            $guiaDespacho->increment('total', $subtotal);

            DB::commit();
            return redirect()->route('detalles_guias_despacho.index', $guiaDespachoId)->with('success', 'Detalle de la Guía de Despacho agregado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al agregar el detalle: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $detalle = DetalleGuiaDespacho::findOrFail($id);
        $productos = Producto::all();
        return view('detalles_guias_despacho.edit', compact('detalle', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad_entregada' => 'required|integer|min:1',
            'precio_compra' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $detalle = DetalleGuiaDespacho::findOrFail($id);
            $guiaDespacho = $detalle->guiaDespacho;

            $subtotalAnterior = $detalle->subtotal;
            $subtotalNuevo = $request->cantidad_entregada * $request->precio_compra;

            $detalle->update([
                'producto_id' => $request->producto_id,
                'cantidad_entregada' => $request->cantidad_entregada,
                'precio_compra' => $request->precio_compra,
                'subtotal' => $subtotalNuevo,
            ]);

            $guiaDespacho->increment('total', $subtotalNuevo - $subtotalAnterior);

            DB::commit();
            return redirect()->route('detalles_guias_despacho.index', $guiaDespacho->id)->with('success', 'Detalle de la Guía de Despacho actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el detalle: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $detalle = DetalleGuiaDespacho::findOrFail($id);
            $guiaDespacho = $detalle->guiaDespacho;

            $guiaDespacho->decrement('total', $detalle->subtotal);
            $detalle->delete();

            DB::commit();
            return redirect()->route('detalles_guias_despacho.index', $guiaDespacho->id)->with('success', 'Detalle de la Guía de Despacho eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar el detalle: ' . $e->getMessage());
        }
    }
}
