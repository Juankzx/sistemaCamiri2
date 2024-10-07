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
use App\Models\Caja;
use App\Models\Movimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;

class VentaController extends Controller
{
    
    public function index()
{
    // Obtén las ventas con sus relaciones
    $ventas = Venta::with(['user', 'sucursal', 'metodo_pago', 'detallesVenta.producto', 'inventarios'])
                    ->paginate(1000)
                    ->withQueryString();

    // Obtén las sucursales para pasarlas a la vista
    $sucursales = Sucursale::all();
    $metodosPago = MetodosPago::all(); // Asegúrate de que el modelo Sucursale esté correctamente importado

    // Pasar ventas y sucursales a la vista
    return view('ventas.index', compact('ventas', 'sucursales', 'metodosPago'));
}


// Controlador VentaController.php
public function create()
{
    // Obtener el usuario autenticado
    $user = auth()->user();
    
    // Obtener todos los métodos de pago y sucursales
    $metodosPago = MetodosPago::all();
    $sucursales = Sucursale::all();

    // Verificar si el usuario tiene una caja abierta
    $cajaAbierta = Caja::where('user_id', $user->id)
                        ->where('estado', true)
                        ->first();

    // Si no hay una caja abierta, redirigir a la vista de gestión de cajas con un mensaje de error
    if (!$cajaAbierta) {
        return redirect()->route('cajas.index')
                         ->with('error', 'No tiene una caja abierta. Por favor, abra una caja antes de realizar una venta.');
    }

    // Obtener la sucursal activa de la caja abierta
    $sucursalActiva = $cajaAbierta->sucursal_id;

    // Obtener todos los productos con inventarios de la sucursal activa
    $productos = Producto::with(['inventarios' => function ($query) use ($sucursalActiva) {
        $query->where('sucursal_id', $sucursalActiva);
    }])
    ->whereHas('inventarios', function ($query) use ($sucursalActiva) {
        $query->where('sucursal_id', $sucursalActiva);
    })
    ->get();

    // Retornar la vista de creación de ventas con los datos correspondientes
    return view('ventas.create', compact('sucursales', 'productos', 'metodosPago', 'user', 'cajaAbierta', 'sucursalActiva'));
}

public function store(Request $request)
{
    // Validación de los datos de entrada
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

    // Verificar si el usuario tiene una caja abierta
    $cajaAbierta = Caja::where('user_id', auth()->user()->id)
                        ->where('estado', true)
                        ->first();

    if (!$cajaAbierta) {
        return response()->json(['success' => false, 'error' => 'No tiene una caja abierta. Por favor, abra una caja antes de realizar una venta.'], 500);
    }

    // Iniciar una transacción
    DB::beginTransaction();

    try {
        // Crear la venta con el total inicial en cero y agregar `caja_id`
        $venta = new Venta([
            'user_id' => $validatedData['user_id'],
            'sucursal_id' => $validatedData['sucursal_id'],
            'metodo_pago_id' => $validatedData['metodo_pago_id'],
            'caja_id' => $cajaAbierta->id, // Asignar caja_id al crear la venta
            'fecha' => $validatedData['fecha'],
            'total' => 0,  // Total temporalmente en cero
            'caja_id' => $cajaAbierta->id,  // Asignar el ID de la caja abierta
        ]);
        $venta->save();

        $total = 0; // Inicializar el total de la venta

        // Procesar cada detalle de la venta
        foreach ($validatedData['detalles'] as $detalleData) {
            $producto = Producto::findOrFail($detalleData['producto_id']);
            \Log::info("Producto encontrado: {$producto->nombre} con ID: {$producto->id}");

            // Verificar inventario disponible para la sucursal
            $inventario = Inventario::where('producto_id', $producto->id)
                                    ->where('sucursal_id', $validatedData['sucursal_id'])
                                    ->first();

            if (!$inventario) {
                throw new \Exception("Inventario no encontrado para el producto: {$producto->nombre} en la sucursal con ID: {$validatedData['sucursal_id']}");
            }

            if ($inventario->cantidad < $detalleData['cantidad']) {
                throw new \Exception("No hay suficiente inventario para el producto: {$producto->nombre}. Disponible: {$inventario->cantidad}, Requerido: {$detalleData['cantidad']}");
            }

            // Decrementar la cantidad del inventario
            $inventario->decrement('cantidad', $detalleData['cantidad']);

            // Crear el detalle de la venta
            $detalle = $venta->detallesVenta()->create([
                'producto_id' => $producto->id,
                'inventario_id' => $detalleData['inventario_id'],
                'cantidad' => $detalleData['cantidad'],
                'precio_unitario' => $detalleData['precio_unitario']
            ]);
            \Log::info("Detalle creado: ID Detalle = {$detalle->id}");

            // Calcular el total de la venta
            $total += $detalle->cantidad * $detalle->precio_unitario;

            // Crear un registro de movimiento de inventario
            Movimiento::create([
                'producto_id' => $producto->id,
                'sucursal_id' => $validatedData['sucursal_id'],
                'tipo' => 'venta',
                'cantidad' => $detalleData['cantidad'],
                'fecha' => now(),
                'user_id' => $validatedData['user_id']
            ]);
        }

        // Actualizar el total de la venta y guardar
        $venta->total = $total;
        $venta->save();

        DB::commit();

        // Retornar la respuesta en JSON para el modal de confirmación
        return response()->json(['success' => true, 'venta_id' => $venta->id], 200);

    } catch (\Exception $e) {
        // Rollback de la transacción en caso de error
        DB::rollback();
        \Log::error("Error al procesar la venta: " . $e->getMessage());

        // Asegúrate de que el catch devuelva siempre un JSON
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    public function show(Venta $venta)
    {
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

    public function getProductosPorSucursal($sucursal_id)
    {
        $productos = Producto::whereHas('inventarios', function($query) use ($sucursal_id) {
            $query->where('sucursal_id', $sucursal_id);
        })->with(['inventarios' => function($query) use ($sucursal_id) {
            $query->where('sucursal_id', $sucursal_id);
        }])->get();

        return response()->json($productos);
    }

    public function productosPorSucursal($sucursalId)
    {
        $productos = Producto::whereHas('inventarios', function ($query) use ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        })->with(['inventarios' => function ($query) use ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        }])->get();

        return response()->json($productos);
    }

    public function print($id)
    {
        $venta = Venta::with(['user', 'sucursal', 'detallesVenta.producto'])->findOrFail($id);

        // Crear el código QR
        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->data(route('ventas.show', $venta->id))
            ->encoding(new Encoding('UTF-8'))
            
            ->build();

        // Obtener la URI de la imagen del código QR
        $qrCodeDataUri = $qrCode->getDataUri();

        // Renderizar la vista de la boleta como HTML
        $pdf = Pdf::loadView('ventas.boleta', compact('venta', 'qrCodeDataUri'));

        // Devolver el PDF generado en el navegador
        return $pdf->stream('boleta_venta_'.$venta->id.'.pdf');
    }

}
