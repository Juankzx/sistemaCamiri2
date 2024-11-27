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
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        if (auth()->check() && auth()->user()->hasRole('bodeguero')) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        return $next($request);
    });
}

    
    public function index()
{
    $user = auth()->user(); // Usuario autenticado

    // Inicializa la consulta base
    $ventasQuery = Venta::with([
        'user', 
        'sucursal', 
        'metodo_pago', 
        'detallesVenta.producto', 
        'inventarios'
    ])
    ->orderBy('created_at', 'desc'); // Ordenar por fecha de creación en orden descendente

    // Si el usuario es vendedor, filtrar solo sus propias ventas
    if ($user->hasRole('vendedor')) {
        $ventasQuery->where('user_id', $user->id);
    }

    // Paginar las ventas
    $ventas = $ventasQuery->paginate(15)->withQueryString();

    // Obtener sucursales y métodos de pago
    $sucursales = Sucursale::all();
    $metodosPago = MetodosPago::all();

    // Pasar datos a la vista
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

    // Obtener la sucursal activa como objeto completo
    $sucursalActiva = Sucursale::find($cajaAbierta->sucursal_id);

    // Obtener todos los productos con inventarios de la sucursal activa o categorías "sin stock"
    $productos = Producto::with(['categoria', 'unidadMedida', 'inventarios' => function ($query) use ($sucursalActiva) {
        $query->where('sucursal_id', $sucursalActiva->id);
    }])
    ->where(function ($query) use ($sucursalActiva) {
        $query->whereHas('inventarios', function ($subQuery) use ($sucursalActiva) {
            $subQuery->where('sucursal_id', $sucursalActiva->id);
        })
        ->orWhereHas('categoria', function ($subQuery) {
            $subQuery->where('sin_stock', true);
        });
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
        'detalles.*.producto_id' => 'required|exists:productos,id',
        'detalles.*.cantidad' => 'required|numeric|min:0.01',
        'detalles.*.precio_unitario' => 'required|numeric',
        // Validar inventario_id solo si el producto no pertenece a una categoría "sin_stock"
        'detalles.*.inventario_id' => [
            'nullable',
            function ($attribute, $value, $fail) use ($request) {
                $detalleIndex = explode('.', $attribute)[1]; // Obtener el índice del detalle
                $productoId = $request->input("detalles.$detalleIndex.producto_id");
    
                $producto = Producto::with('categoria')->find($productoId);
    
                if ($producto && !$producto->categoria->sin_stock && !$value) {
                    $fail("El campo $attribute es obligatorio para productos con inventario.");
                }
            },
        ],
    ]);
    

    // Log para verificar los datos validados
    \Log::info("Datos validados: ", $validatedData);

    // Verificar si el usuario tiene una caja abierta
    $cajaAbierta = Caja::where('user_id', auth()->user()->id)
        ->where('estado', true)
        ->first();

    if (!$cajaAbierta) {
        \Log::error("No hay caja abierta para el usuario.");
        return response()->json([
            'success' => false,
            'error' => 'No tiene una caja abierta. Por favor, abra una caja antes de realizar una venta.'
        ], 500);
    }

    // Log para verificar la caja abierta
    \Log::info("Caja abierta encontrada: ", $cajaAbierta->toArray());

    // Iniciar una transacción
    DB::beginTransaction();

    try {
        // Crear la venta
        $venta = new Venta([
            'user_id' => $validatedData['user_id'],
            'sucursal_id' => $validatedData['sucursal_id'],
            'metodo_pago_id' => $validatedData['metodo_pago_id'],
            'caja_id' => $cajaAbierta->id,
            'fecha' => $validatedData['fecha'],
            'total' => 0,
        ]);
        $venta->save();

        // Log para verificar la venta creada
        \Log::info("Venta creada: ", $venta->toArray());

        $total = 0; // Inicializar el total de la venta

        // Procesar cada detalle de la venta
        foreach ($validatedData['detalles'] as $detalleIndex => $detalleData) {
            \Log::info("Procesando detalle $detalleIndex: ", $detalleData);

            $producto = Producto::with('categoria')->findOrFail($detalleData['producto_id']);

            \Log::info("Producto encontrado: {$producto->nombre} (ID: {$producto->id})");

            // Verificar si el producto pertenece a una categoría "sin stock"
            if ($producto->categoria && $producto->categoria->sin_stock) {
                \Log::info("Producto 'sin stock': {$producto->nombre}");

                // Crear el detalle de la venta sin inventario
                $detalle = $venta->detallesVenta()->create([
                    'producto_id' => $producto->id,
                    'inventario_id' => null,
                    'cantidad' => $detalleData['cantidad'],
                    'precio_unitario' => $detalleData['precio_unitario'],
                ]);

                \Log::info("Detalle creado para producto 'sin stock': ", $detalle->toArray());
            } else {
                // Verificar inventario disponible
                $inventario = Inventario::where('producto_id', $producto->id)
                    ->where('sucursal_id', $validatedData['sucursal_id'])
                    ->first();

                if (!$inventario) {
                    \Log::error("Inventario no encontrado para producto: {$producto->nombre}");
                    throw new \Exception("Inventario no encontrado para el producto: {$producto->nombre} en la sucursal.");
                }

                if ($inventario->cantidad < $detalleData['cantidad']) {
                    \Log::error("Inventario insuficiente para producto: {$producto->nombre}");
                    throw new \Exception("No hay suficiente inventario para el producto: {$producto->nombre}. Disponible: {$inventario->cantidad}, Requerido: {$detalleData['cantidad']}");
                }

                // Decrementar la cantidad del inventario
                $inventario->decrement('cantidad', $detalleData['cantidad']);
                \Log::info("Cantidad de inventario actualizada para producto: {$producto->nombre}");

                // Crear el detalle de la venta
                $detalle = $venta->detallesVenta()->create([
                    'producto_id' => $producto->id,
                    'inventario_id' => $detalleData['inventario_id'],
                    'cantidad' => $detalleData['cantidad'],
                    'precio_unitario' => $detalleData['precio_unitario'],
                ]);

                \Log::info("Detalle creado: ", $detalle->toArray());

                // Crear un registro de movimiento de inventario
                Movimiento::create([
                    'producto_id' => $producto->id,
                    'sucursal_id' => $validatedData['sucursal_id'],
                    'tipo' => 'venta',
                    'cantidad' => $detalleData['cantidad'],
                    'fecha' => now(),
                    'user_id' => $validatedData['user_id'],
                ]);

                \Log::info("Movimiento de inventario creado para producto: {$producto->nombre}");
            }

            // Calcular el total de la venta
            $total += $detalleData['cantidad'] * $detalleData['precio_unitario'];
        }

        // Actualizar el total de la venta y guardar
        $venta->total = $total;
        $venta->save();

        \Log::info("Venta actualizada con el total: ", $venta->toArray());

        DB::commit();

        return response()->json(['success' => true, 'venta_id' => $venta->id], 200);
    } catch (\Exception $e) {
        DB::rollback();

        // Log del error detallado para depuración
        \Log::error("Error al procesar la venta: " . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

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
        if (auth()->user()->hasRole('vendedor')) {
            abort(403, 'No tienes permiso para editar esta venta.');
        }

        $metodosPago = MetodosPago::all();
        $sucursales = Sucursale::all();
        return view('ventas.edit', compact('venta', 'metodosPago', 'sucursales'));
    }

    public function update(Request $request, Venta $venta)
    {
        if (auth()->user()->hasRole('vendedor')) {
            abort(403, 'No tienes permiso para editar esta venta.');
        }
        // Lógica similar a store pero ajustando los detalles de la venta y el inventario correspondiente
    }

    public function destroy(Venta $venta)
    {
        if (auth()->user()->hasRole('vendedor')) {
            abort(403, 'No tienes permiso para editar esta venta.');
        }
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
