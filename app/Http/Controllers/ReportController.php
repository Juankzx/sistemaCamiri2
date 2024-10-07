<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Venta;
use App\Models\Report;
use Illuminate\Http\Request;
use PDF;

class ReportController extends Controller
{
    public function index()
    {
        // Muestra la lista de reportes
        return view('reports.index');
    }

    public function salesOverTime(Request $request)
    {
        // Lógica para generar reporte de ventas por rango de fechas
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $sales = Venta::whereBetween('created_at', [$start_date, $end_date])->get();

        // Generar PDF o mostrar vista
        return view('reports.sales_over_time', compact('sales', 'start_date', 'end_date'));
    }

    public function inventorySummary()
    {
        // Lógica para generar reporte de resumen de inventario
        $inventories = Inventario::all();

        // Generar PDF o mostrar vista
        return view('reports.inventory_summary', compact('inventories'));
    }

    public function purchaseReports()
    {
        // Lógica para generar reporte de compras
        $purchases = Compra::all();

        // Generar PDF o mostrar vista
        return view('reports.purchase_reports', compact('purchases'));
    }

    public function paymentMethodsReport()
    {
        // Lógica para generar reporte de métodos de pago
        $methods = MetodoPago::with('ventas')->get();

        // Generar PDF o mostrar vista
        return view('reports.payment_methods_report', compact('methods'));
    }
}
