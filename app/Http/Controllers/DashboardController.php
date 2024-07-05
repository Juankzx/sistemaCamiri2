<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Datos para el gráfico de ventas de los últimos 7 días
        $salesLast7Days = $this->getSalesLast7Days();

        // Datos para el gráfico de ventas de los últimos meses
        $salesLastMonths = $this->getSalesLastMonths();

        // Datos para el gráfico de ventas en efectivo y tarjeta del último mes
        $salesPaymentMethod = $this->getSalesPaymentMethod();

        return view('dashboard', compact('salesLast7Days', 'salesLastMonths', 'salesPaymentMethod'));
    }

    private function getSalesLast7Days()
    {
        $sales = DB::table('ventas')
                    ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
                    ->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->groupBy('date')
                    ->orderBy('date', 'ASC')
                    ->get();

        return $sales;
    }

    private function getSalesLastMonths()
    {
        $sales = DB::table('ventas')
                    ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
                    ->where('created_at', '>=', Carbon::now()->subMonths(12))
                    ->groupBy('month')
                    ->orderBy('month', 'ASC')
                    ->get();

        return $sales;
    }

    private function getSalesPaymentMethod()
    {
        $sales = DB::table('ventas')
                    ->select('payment_method', DB::raw('SUM(amount) as total'))
                    ->where('created_at', '>=', Carbon::now()->subMonth())
                    ->groupBy('payment_method')
                    ->get();

        return $sales;
    }
}