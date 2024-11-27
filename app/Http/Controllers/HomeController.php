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
use App\Models\User;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
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

        $productosMasVendidos = Producto::whereHas('inventarios', function ($query) use ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        })
        ->withSum('detallesVentas as total_vendido', 'cantidad')
        ->orderByDesc('total_vendido')
        ->limit(5)
        ->get();
        
        $productosMenosVendidos = Producto::whereHas('inventarios', function ($query) use ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        })
        ->withSum('detallesVentas as total_vendido', 'cantidad')
        ->orderBy('total_vendido')
        ->limit(5)
        ->get();
        $ultimasfacturaspagadas = Factura::where('estado_pago', 'pagado') // Filtrar solo facturas pendientes
        ->latest() // Ordenar desde la más reciente
        ->limit(5) // Limitar a las últimas 5
        ->get(); // Obtener los registros
        
        $ultimasfacturaspendientes = Factura::where('estado_pago', 'pendiente') // Filtrar solo facturas pendientes
    ->latest() // Ordenar desde la más reciente
    ->limit(5) // Limitar a las últimas 5
    ->get(); // Obtener los registros

        // Asegúrate de que estas variables estén definidas correctamente
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

        // Asegurarse de que estas variables siempre existan en la vista
        $diasSemana = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $ventasSemanaActualData = [];
        $ventasSemanaAnteriorData = [];

        foreach ($diasSemana as $dia) {
            $ventasSemanaActualData[] = $ventasSemanaActual->get($dia, 0);
            $ventasSemanaAnteriorData[] = $ventasSemanaAnterior->get($dia, 0);
        }

        // Cargar vistas según el rol del usuario
        if ($user->hasRole('root') || $user->hasRole('administrador')) {
            // Vista completa para root y administrador
            return view('dashboard.complete', compact(
                'sucursales', 'ventasHoy', 'ventasMes', 'comprasMes', 'productosBajoStock', 
                'productosMasVendidos', 'productosMenosVendidos', 'ultimasfacturaspagadas', 
                'ultimasfacturaspendientes', 'ventasSemanaActualData', 'ventasSemanaAnteriorData', 'sucursalId', 'user'
            ));
        } elseif ($user->hasRole('bodeguero')) {
            // Vista simplificada para bodeguero
            return view('dashboard.bodeguero', compact('user'));
        } elseif ($user->hasRole('vendedor')) {
            $isVendedor = true; // Define la variable
            return redirect()->route('cajas.index');
        } else {
            // Vista en blanco para otros roles sin permisos específicos
            return view('dashboard.blank');
        }
    }

    private function obtenerDatosVentas($periodo, $sucursalId = null)
    {
        switch ($periodo) {
            case 'semana':
                $labels = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                $data = Venta::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                    ->select(DB::raw('DAYNAME(created_at) as dia'), DB::raw('SUM(total) as total'))
                    ->groupBy('dia')
                    ->orderByRaw("FIELD(dia, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
                    ->pluck('total', 'dia')
                    ->mapWithKeys(fn($value, $key) => [__(ucfirst($key)) => $value])
                    ->toArray();
                break;

            case 'mes':
                $labels = range(1, now()->daysInMonth);
                $data = Venta::whereMonth('created_at', now()->month)
                    ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                    ->select(DB::raw('DAY(created_at) as dia'), DB::raw('SUM(total) as total'))
                    ->groupBy('dia')
                    ->pluck('total', 'dia')
                    ->toArray();
                break;

            case 'semestre':
                $labels = ['Enero-Febrero', 'Marzo-Abril', 'Mayo-Junio', 'Julio-Agosto', 'Septiembre-Octubre', 'Noviembre-Diciembre'];
                $data = [];
                for ($i = 0; $i < 12; $i += 2) {
                    $start = now()->startOfYear()->addMonths($i);
                    $end = $start->copy()->addMonth();
                    $data[] = Venta::whereBetween('created_at', [$start, $end])
                        ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                        ->sum('total');
                }
                break;

            case 'año':
                $labels = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                $data = [];
                for ($i = 1; $i <= 12; $i++) {
                    $data[] = Venta::whereMonth('created_at', $i)
                        ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                        ->sum('total');
                }
                break;

            default:
                $labels = [];
                $data = [];
                break;
        }

        return ['labels' => $labels, 'data' => array_values($data)];
    }
}
