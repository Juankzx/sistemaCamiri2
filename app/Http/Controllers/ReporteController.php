<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sucursale;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\OrdenCompra;
use App\Models\Factura;
use App\Models\Inventario;
use DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        if (auth()->check() && auth()->user()->hasRole(['bodeguero', 'vendedor'])) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        return $next($request);
    });
}


    public function ventas(Request $request)
    {
        $sucursales = Sucursale::all();
        $sucursalId = $request->input('sucursal_id', null);
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->toDateString());

        // Ventas Diarias en el Rango de Fechas Seleccionado
        $ventasDiarias = Venta::selectRaw('DATE(created_at) as fecha, SUM(total) as total')
            ->when($sucursalId, function($query) use ($sucursalId) {
                $query->where('sucursal_id', $sucursalId);
            })
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha', 'desc')
            ->get();

        // Comparación de Ventas Semanal
        $ventasSemanaActual = Venta::selectRaw('DAYNAME(created_at) as dia, SUM(total) as total')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->when($sucursalId, function($query) use ($sucursalId) {
                $query->where('sucursal_id', $sucursalId);
            })
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $ventasSemanaAnterior = Venta::selectRaw('DAYNAME(created_at) as dia, SUM(total) as total')
            ->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->when($sucursalId, function($query) use ($sucursalId) {
                $query->where('sucursal_id', $sucursalId);
            })
            ->groupBy('dia')
            ->pluck('total', 'dia');

        // Productos Más y Menos Vendidos
        $productosMasVendidos = Producto::withCount(['detallesVentas as total_vendido' => function($query) {
            $query->select(DB::raw("SUM(cantidad)"));
        }])->orderBy('total_vendido', 'desc')->take(5)->get();

        $productosMenosVendidos = Producto::withCount(['detallesVentas as total_vendido' => function($query) {
            $query->select(DB::raw("SUM(cantidad)"));
        }])->orderBy('total_vendido', 'asc')->take(5)->get();

        return view('reportes.ventas', compact(
            'sucursales', 'ventasDiarias', 'ventasSemanaActual', 'ventasSemanaAnterior', 
            'productosMasVendidos', 'productosMenosVendidos', 'sucursalId', 'fechaInicio', 'fechaFin'
        ));
    }


    public function inventario()
    {
        $productosConBajoStock = Inventario::where('cantidad', '<', 10)->get();
        $inventarioActual = Producto::with('inventario')->get();

        return view('reportes.inventario', compact('productosConBajoStock', 'inventarioActual'));
    }

    public function compras()
    {
        $ordenesRecientes = OrdenCompra::latest()->take(5)->get();
        $facturasRecientes = Factura::latest()->take(5)->get();

        return view('reportes.compras', compact('ordenesRecientes', 'facturasRecientes'));
    }

    public function financieros()
    {
        $ingresosMensuales = Venta::selectRaw('MONTH(created_at) as mes, SUM(total) as ingresos')
            ->groupBy('mes')
            ->get();
        
        $gastosMensuales = Factura::selectRaw('MONTH(fecha_emision) as mes, SUM(monto_total) as gastos')
            ->groupBy('mes')
            ->get();

        return view('reportes.financieros', compact('ingresosMensuales', 'gastosMensuales'));
    }
}
