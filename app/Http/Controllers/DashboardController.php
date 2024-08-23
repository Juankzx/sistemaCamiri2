<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Venta;
use App\Models\Sucursale;


class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        // Obtener todas las sucursales
        $sucursales = Sucursale::all();

        // Seleccionar la sucursal en base a la solicitud o seleccionar la primera por defecto
        $selectedSucursal = $request->input('sucursal_id', $sucursales->first()->id);

        // Obtener datos de ventas específicos de la sucursal seleccionada
        $ventas7dias = Venta::where('sucursal_id', $selectedSucursal)
            ->whereBetween('fecha', [now()->subDays(7), now()])
            ->sum('total');

        $ventasUltimosMeses = Venta::where('sucursal_id', $selectedSucursal)
            ->whereBetween('fecha', [now()->subMonths(6), now()])
            ->selectRaw('DATE_FORMAT(fecha, "%Y-%m") as mes, SUM(total) as total')
            ->groupBy('mes')
            ->get();

        $ventasEfectivoTarjeta = Venta::where('sucursal_id', $selectedSucursal)
            ->whereBetween('fecha', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('metodo_pago_id, SUM(total) as total')
            ->groupBy('metodo_pago_id')
            ->get();

        return view('home', compact('sucursales', 'ventas7dias', 'ventasUltimosMeses', 'ventasEfectivoTarjeta', 'selectedSucursal'));
    }
}
