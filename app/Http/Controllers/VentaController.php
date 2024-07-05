<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetallesVentum;
use App\Models\Producto;
use App\Models\MetodosPago;
use App\Models\Sucursale;
use App\Models\Inventario;
use App\Models\User;
use App\Models\Movimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{

    public function index()
{
    // Asegúrate de incluir las relaciones necesarias y utilizar la paginación.
    $ventas = Venta::with(['user', 'sucursal', 'metodo_pago', 'detallesVenta.producto', 'inventarios'])
                    ->paginate(10)
                    ->withQueryString();

    return view('ventas.index', compact('ventas'));
}


    public function create()
    {
        $inventarios = Inventario::all();
        $user = auth()->user(); // Asegúrate de modificar según necesidades
        $productos = Producto::all(); // Asegúrate de modificar según necesidades
        $metodosPago = MetodosPago::all(); // Asegúrate de tener este modelo
        $sucursales = Sucursale::all(); // Asegúrate de tener este modelo
        return view('ventas.create', compact('productos', 'metodosPago', 'sucursales', 'user', 'inventarios'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'metodo_pago_id' => 'required|exists:metodos_pagos,id',
            'fecha' => 'required|date',
            'detalles' => 'required|array',
            'detalles.*.inventario_id' => 'required|exists:inventarios,id',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric',
        ]);
    
        DB::beginTransaction();
        try {
            $total = 0;
    
            $venta = new Venta([
                'user_id' => $validatedData['user_id'],
                'sucursal_id' => $validatedData['sucursal_id'],
                'metodo_pago_id' => $validatedData['metodo_pago_id'],
                'fecha' => $validatedData['fecha'],
                'total' => $total,
            ]);
            $venta->save();
    
            foreach ($request->detalles as $detalleData) {
                $producto = Producto::findOrFail($detalleData['producto_id']);
                $inventario = Inventario::where('producto_id', $producto->id)
                                        ->where('sucursal_id', $request->sucursal_id)
                                        ->firstOrFail();
    
                if ($inventario->cantidad < $detalleData['cantidad']) {
                    throw new \Exception("No hay suficiente inventario para el producto: {$producto->nombre}");
                }
    
                $inventario->decrement('cantidad', $detalleData['cantidad']);
    
                $detalle = $venta->detallesVenta()->create([
                    'producto_id' => $producto->id,
                    'inventario_id' => $detalleData['inventario_id'],
                    'cantidad' => $detalleData['cantidad'],
                    'precio_unitario' => $detalleData['precio_unitario']
                ]);
    
                $total += $detalle->cantidad * $detalle->precio_unitario;
    
                // Create movement
                Movimiento::create([
                    'producto_id' => $producto->id,
                    'sucursal_id' => $request->sucursal_id,
                    'tipo' => 'salida',
                    'cantidad' => $detalleData['cantidad'],
                    'fecha' => now(),
                    'user_id' => $request->user_id
                ]);
            }
    
            $venta->total = $total;
            $venta->save();
    
            DB::commit();
            return redirect()->route('ventas.index')->with('success', 'Venta registrada con éxito.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("Error en store de Venta: " . $e->getMessage());
            return back()->with('error', "Error al registrar la venta: " . $e->getMessage())->withInput();
        }
    }
    

public function show(Venta $venta)
{
    // Carga las relaciones necesarias, incluyendo las relaciones anidadas
    $venta->load('user', 'sucursal', 'metodo_pago', 'detallesVenta.producto', 'detallesVenta.inventarios.sucursal');

    return view('ventas.show', compact('venta'));
}



    public function edit(Venta $venta)
    {
        $metodosPago = MetodosPago::all();
        $sucursales = Sucursale::all();
        return view('ventas.edit', compact('venta', 'metodosPago', 'sucursales'));
    }

    public function update(Request $request, Venta $venta)
    {
        // Lógica similar a store pero ajustando los detalles de la venta y el inventario correspondiente
    }

    public function destroy(Venta $venta)
    {
        $venta->delete();
        return redirect()->route('ventas.index')->with('success', 'Venta eliminada con éxito.');
    }
}
