<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\GuiaDespacho;
use App\Models\OrdenCompra;
use App\Models\Producto;
use App\Models\DetalleGuiaDespacho; // Asegúrate de tener este modelo
use Illuminate\Http\Request;
use App\Models\Inventario;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GuiaDespachoController extends Controller
{
    public function index()
    {
        // Obtener guías con las relaciones necesarias
        $guias = GuiaDespacho::with('ordenCompra.proveedor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('guias-despacho.index', compact('guias'));
    }

    public function create()
    {
        // Obtener las órdenes de compra en estado 'en_transito'
        $ordenCompra = OrdenCompra::where('estado', 'solicitado')->get();
        return view('guias-despacho.create', compact('ordenCompra'));
    }

    public function store(Request $request)
    {
        \Log::info('Datos recibidos en la creación de Guía de Despacho:', $request->all());
    
        // Validación de datos
        $validatedData = $request->validate([
            'numero_guia' => 'required|string|max:255|unique:guias_despacho,numero_guia',
            'fecha_entrega' => 'required|date',
            'orden_compra_id' => 'required|exists:ordenes_compras,id',
            'detalles' => 'required|array',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad_entregada' => 'required|integer|min:1',
            'detalles.*.precio_compra' => 'required|numeric|min:0',
        ]);
    
        try {
            // Iniciar transacción
            DB::beginTransaction();
    
            // Crear la Guía de Despacho
            $guiaDespacho = GuiaDespacho::create([
                'numero_guia' => $validatedData['numero_guia'],
                'fecha_entrega' => $validatedData['fecha_entrega'],
                'orden_compra_id' => $validatedData['orden_compra_id'],
                'estado' => 'emitida',
            ]);
    
            $total = 0;
    
            // Iterar sobre los detalles para guardarlos en la base de datos y actualizar el inventario
            foreach ($request->detalles as $detalle) {
                $subtotal = $detalle['cantidad_entregada'] * $detalle['precio_compra'];
                $total += $subtotal;
    
                // Guardar los detalles de la guía de despacho
                DetalleGuiaDespacho::create([
                    'guia_despacho_id' => $guiaDespacho->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad_entregada' => $detalle['cantidad_entregada'],
                    'precio_compra' => $detalle['precio_compra'],
                    'subtotal' => $subtotal,
                ]);
    
                // Actualizar el inventario general (Bodega General con ID 1)
                Inventario::addStock($detalle['producto_id'], $detalle['cantidad_entregada'], 1);
    
                // Registrar el movimiento de inventario
                Movimiento::create([
                    'producto_id' => $detalle['producto_id'],
                    'bodega_id' => 1, // Bodega General con ID 1
                    'sucursal_id' => null,
                    'tipo' => 'compra',
                    'cantidad' => $detalle['cantidad_entregada'],
                    'fecha' => now(),
                    'user_id' => auth()->id(),
                ]);
            }
    
            // Actualizar el total de la Guía de Despacho
            $guiaDespacho->update(['total' => $total]);
    
            // Cambiar el estado de la Orden de Compra a "en_transito"
            $ordenCompra = $guiaDespacho->ordenCompra;
            if ($ordenCompra->estado === 'solicitado') {
                $ordenCompra->update(['estado' => 'en_transito']);
            }
    
            // Confirmar transacción
            DB::commit();
    
            return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho creada con éxito y stock actualizado.');
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();
            \Log::error("Error al crear la Guía de Despacho: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear la Guía de Despacho: ' . $e->getMessage());
        }
    }
    
    public function getOrdenCompraDetails($id)
    {
        // Obtener la orden de compra con los detalles y el producto asociado
        $ordenCompra = OrdenCompra::with('detalles.producto')->findOrFail($id);
        return response()->json($ordenCompra);
    }

    public function getDetalles($id)
{
    // Buscar la guía de despacho con sus detalles y el producto asociado
    $guiaDespacho = GuiaDespacho::with('detalles.producto', 'ordenCompra.proveedor')->findOrFail($id);
    
    // Obtener el proveedor desde la orden de compra asociada
    $proveedor = $guiaDespacho->ordenCompra->proveedor;

    // Devolver una respuesta JSON con los detalles y el proveedor
    return response()->json([
        'detalles' => $guiaDespacho->detalles,
        'proveedor' => $proveedor
    ]);
}


    public function destroy($id)
    {
        $guiaDespacho = GuiaDespacho::findOrFail($id);

        try {
            // Eliminar la guía de despacho
            $guiaDespacho->delete();

            // Actualizar el estado de la orden de compra asociada a 'solicitado'
            $ordenCompra = OrdenCompra::find($guiaDespacho->orden_compra_id);
            if ($ordenCompra) {
                $ordenCompra->update(['estado' => 'solicitado']);
            }

            return redirect()->route('guias-despacho.index')->with('success', 'Guía de despacho eliminada con éxito.');
        } catch (\Exception $e) {
            Log::error("Error al eliminar la guía de despacho: " . $e->getMessage());
            return redirect()->route('guias-despacho.index')->with('error', 'No se pudo eliminar la guía de despacho: ' . $e->getMessage());
        }
    }
    
    
}
