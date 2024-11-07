<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\OrdenCompra;
use App\Models\GuiaDespacho;
use App\Models\Factura;
use App\Models\DetallesVentum;
use App\Models\Sucursale;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $sucursales = Sucursale::all();
        $sucursalId = $request->input('sucursal_id', $sucursales->first()->id ?? null);

        $ventasHoy = Venta::whereDate('created_at', now())
            ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
            ->sum('total');

        $ventasMes = Venta::whereMonth('created_at', now()->month)
            ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
            ->sum('total');

        $comprasMes = Factura::whereMonth('fecha_emision', now()->month)
            ->sum('monto_total');

        $productosBajoStock = Producto::whereHas('inventarios', fn($q) => $q->where('cantidad', '<', 10))->count();

        $productosMasVendidos = Producto::withSum('detallesVentas as total_vendido', 'cantidad')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        $productosMenosVendidos = Producto::withSum('detallesVentas as total_vendido', 'cantidad')
            ->orderBy('total_vendido')
            ->limit(5)
            ->get();

        $ultimasOrdenesCompra = OrdenCompra::latest()->limit(5)->get();
        $ultimasfacturas = Factura::latest()->limit(5)->get();

        $ventasSemanaActual = Venta::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
            ->select(DB::raw('DAYNAME(created_at) as dia'), DB::raw('SUM(total) as total'))
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $ventasSemanaAnterior = Venta::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
            ->select(DB::raw('DAYNAME(created_at) as dia'), DB::raw('SUM(total) as total'))
            ->groupBy('dia')
            ->pluck('total', 'dia');

        return view('home', compact(
            'sucursales', 'ventasHoy', 'ventasMes', 'comprasMes', 'productosBajoStock', 
            'productosMasVendidos', 'productosMenosVendidos', 'ultimasOrdenesCompra', 
            'ultimasfacturas', 'ventasSemanaActual', 'ventasSemanaAnterior', 'sucursalId'
        ));
    }
}
