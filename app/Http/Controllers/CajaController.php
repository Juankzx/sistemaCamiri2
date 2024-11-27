<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Caja;
use App\Models\Sucursale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
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
        // Si es un vendedor, mostrar solo sus propias cajas
    if ($user->hasRole('vendedor')) {
        $cajas = Caja::where('user_id', $user->id)
            ->with('sucursal', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    } else {
        // Si no es vendedor (por ejemplo, administrador), mostrar todas las cajas
        $cajas = Caja::with('sucursal', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }
        
        $sucursales = Sucursale::all();

        $cajaAbierta = Caja::where('estado', true)
        ->where('user_id', $user->id) // Solo la caja abierta del usuario autenticado
        ->first();

        // Calcular el monto total de ventas durante el periodo de caja abierta
        $montoVentas = 0;
        if ($cajaAbierta) {
            $montoVentas = Venta::where('fecha', '>=', $cajaAbierta->fecha_apertura)
                                ->where('sucursal_id', $cajaAbierta->sucursal_id)
                                ->sum('total');
        }

        return view('cajas.index', compact('cajas', 'sucursales', 'cajaAbierta', 'montoVentas'));
    }

    public function abrir(Request $request)
    {
        $validatedData = $request->validate([
            'sucursal_id' => 'required|exists:sucursales,id',
            'monto_apertura' => 'required|numeric|min:0',
        ]);

        $caja = new Caja([
            'sucursal_id' => $validatedData['sucursal_id'],
            'user_id' => Auth::id(),
            'fecha_apertura' => Carbon::now(),
            'monto_apertura' => $validatedData['monto_apertura'],
            'monto_cierre' => 0, // Default value for closing amount
            'estado' => true,
        ]);
        $caja->save();

        return redirect()->route('ventas.create')->with('success', 'Caja abierta con éxito.');
    }

    public function cerrar(Request $request, $id)
    {
        $validatedData = $request->validate([
            'monto_cierre' => 'required|numeric',
        ]);

        $caja = Caja::findOrFail($id);

    // Verificar que el usuario tenga acceso a esta caja
    if (auth()->user()->hasRole('vendedor') && auth()->user()->id !== $caja->user_id) {
        abort(403, 'No tienes permiso para cerrar esta caja.');
    }

        DB::beginTransaction();
        try {
            $caja = Caja::findOrFail($id);
            $caja->fecha_cierre = now();
            $caja->monto_cierre = $validatedData['monto_cierre'];
            $caja->estado = false;
            $caja->save();

            DB::commit();
            return redirect()->route('cajas.index')->with('success', 'Caja cerrada con éxito.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("Error al cerrar la caja: " . $e->getMessage());
            return back()->with('error', "Error al cerrar la caja: " . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        // Obtener la caja por su ID
        $caja = Caja::findOrFail($id);

        // Si el usuario es vendedor, verificar que sea propietario de la caja
    if (auth()->user()->hasRole('vendedor') && auth()->user()->id !== $caja->user_id) {
        abort(403, 'No tienes permiso para acceder a esta caja.');
    }

        // Obtener las ventas realizadas durante el periodo de apertura de la caja
        $fechaCierre = $caja->fecha_cierre ? $caja->fecha_cierre : now();
        $ventas = Venta::whereBetween('created_at', [$caja->fecha_apertura, $fechaCierre])
            ->where('sucursal_id', $caja->sucursal_id)
            ->get();

        // Calcular el total de ventas durante el periodo de apertura de la caja
        $totalVentas = $ventas->sum('total');

        // Contar ventas por método de pago y sumar totales
        $ventasEfectivo = $ventas->where('metodo_pago_id', 1)->count(); // Asumiendo que 1 es ID de Efectivo
        $totalEfectivo = $ventas->where('metodo_pago_id', 1)->sum('total');
        $ventasTarjeta = $ventas->where('metodo_pago_id', 2)->count(); // Asumiendo que 2 es ID de Tarjeta
        $totalTarjeta = $ventas->where('metodo_pago_id', 2)->sum('total');

        return view('cajas.show', compact('caja', 'ventas', 'totalVentas', 'ventasEfectivo', 'totalEfectivo', 'ventasTarjeta', 'totalTarjeta'));
    }
 
    public function imprimirboleta($id)
{
    // Obtener la caja por su ID con la relación de sucursal y usuario
    $caja = Caja::with(['user', 'sucursal'])->findOrFail($id);

    // Determinar la fecha de cierre (si está cerrada se usa `fecha_cierre`, si no se usa la fecha actual)
    $fechaCierre = $caja->fecha_cierre ? $caja->fecha_cierre : now();

    // Obtener las ventas realizadas durante el periodo de apertura de la caja en la sucursal correspondiente
    $ventas = Venta::where('sucursal_id', $caja->sucursal_id)
                   ->whereBetween('created_at', [$caja->fecha_apertura, $fechaCierre])
                   ->get();

    // Calcular el total de ventas realizadas
    $totalVentas = $ventas->sum('total');

    // Contar y sumar las ventas realizadas con cada método de pago
    $ventasEfectivo = $ventas->where('metodo_pago_id', 1)->count(); // ID 1 = Efectivo
    $totalEfectivo = $ventas->where('metodo_pago_id', 1)->sum('total');

    $ventasTarjeta = $ventas->where('metodo_pago_id', 2)->count(); // ID 2 = Tarjeta
    $totalTarjeta = $ventas->where('metodo_pago_id', 2)->sum('total');

    $ventasAmipass = $ventas->where('metodo_pago_id', 3)->count(); // ID 3 = Amipass
    $totalAmipass = $ventas->where('metodo_pago_id', 3)->sum('total');

    // Calcular los gastos de caja (esto es opcional, puedes asignar un valor si no usas esta funcionalidad)
    $totalGastos = 0; // Puedes calcular esto si tienes una tabla de gastos

    // Calcular el balance final
    $balanceFinal = $caja->monto_apertura + $totalVentas - $totalGastos;

    // Total de ventas realizadas en todas las formas de pago
    $totalVentasRealizadas = $ventasEfectivo + $ventasTarjeta + $ventasAmipass;

    // Retornar la vista `boleta_arqueo` con todos los datos necesarios
    return view('cajas.boleta_arqueo', compact(
        'caja', 'totalEfectivo', 'ventasEfectivo', 'totalTarjeta', 'ventasTarjeta',
        'totalAmipass', 'ventasAmipass', 'totalVentas', 'totalGastos',
        'balanceFinal', 'totalVentasRealizadas'
    ));
}

public function edit($id)
{
    if (auth()->user()->hasRole('vendedor')) {
        abort(403, 'No tienes permiso para editar esta caja.');
    }
}

public function destroy($id)
{
    if (auth()->user()->hasRole('vendedor')) {
        abort(403, 'No tienes permiso para editar esta caja.');
    }
}



}
