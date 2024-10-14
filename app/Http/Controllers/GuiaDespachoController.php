<?php

namespace App\Http\Controllers;

use App\Models\GuiaDespacho;
use App\Models\OrdenCompra;
use App\Models\Producto;
use Illuminate\Http\Request;

class GuiaDespachoController extends Controller
{
    public function index()
    {
        // Obtener guías con las relaciones necesarias
        $guias = GuiaDespacho::with('ordenCompra.proveedor')->paginate(15);
        return view('guias-despacho.index', compact('guias'));
    }

    public function create()
    {
        $ordenCompra = OrdenCompra::where('estado', 'solicitado')->get();
        $productos = Producto::all();
        return view('guias-despacho.create', compact('ordenCompra', 'productos'));
    }

    public function store(Request $request)
{
    // Mostrar los datos que se están enviando para verificar si están completos
    \Log::info('Datos recibidos en la creación de Guía de Despacho:', $request->all());

    // Validar los datos recibidos
    $validatedData = $request->validate([
        'numero_guia' => 'required|string|max:255|unique:guias_despacho,numero_guia',
        'fecha_entrega' => 'required|date',
        'orden_compra_id' => 'required|exists:ordenes_compras,id',
    ]);

    try {
        // Crear la nueva guía de despacho
        $guiaDespacho = GuiaDespacho::create([
            'numero_guia' => $validatedData['numero_guia'],
            'fecha_entrega' => $validatedData['fecha_entrega'],
            'orden_compra_id' => $validatedData['orden_compra_id'],
            'estado' => 'emitida', // Estado inicial
        ]);

        // Actualizar el estado de la Orden de Compra a 'en_transito'
        $ordenCompra = OrdenCompra::find($validatedData['orden_compra_id']);
        if ($ordenCompra) {
            $ordenCompra->update(['estado' => 'en_transito']);
        } else {
            \Log::error("Orden de compra no encontrada con ID: {$validatedData['orden_compra_id']}");
            return redirect()->back()->with('error', 'Error al encontrar la Orden de Compra asociada.');
        }

        return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho creada con éxito y estado de la orden de compra actualizado.');
    } catch (\Exception $e) {
        // Registrar el error y redirigir con el mensaje de error
        \Log::error("Error al crear la guía de despacho: " . $e->getMessage());
        return redirect()->back()->with('error', 'Error al crear la guía de despacho: ' . $e->getMessage());
    }
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

    public function destroy($id)
{
    // Buscar la guía de despacho por ID
    $guiaDespacho = GuiaDespacho::findOrFail($id);

    try {
        // Eliminar la guía de despacho
        $guiaDespacho->delete();
        
        // Opcionalmente, actualizar el estado de la orden de compra relacionada
        $ordenCompra = OrdenCompra::find($guiaDespacho->orden_compra_id);
        if ($ordenCompra) {
            // Puedes decidir si actualizar el estado a 'solicitado' o cualquier otro
            $ordenCompra->update(['estado' => 'solicitado']);
        }

        return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho eliminada con éxito.');
    } catch (\Exception $e) {
        return redirect()->route('guias-despacho.index')->with('error', 'No se pudo eliminar la guía de despacho: ' . $e->getMessage());
    }
}

}
