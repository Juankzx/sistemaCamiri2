@extends('adminlte::page')

@section('template_title')
    Reportes
@endsection

@section('content')
<div class="container">
    <h2>Módulo de Reportes</h2>
    <ul>
        <li><a href="{{ route('reports.sales_over_time') }}">Reporte de Ventas por Tiempo</a></li>
        <li><a href="{{ route('reports.inventory_summary') }}">Resumen de Inventario</a></li>
        <li><a href="{{ route('reports.purchase_reports') }}">Reporte de Compras</a></li>
        <li><a href="{{ route('reports.payment_methods_report') }}">Reporte de Métodos de Pago</a></li>
    </ul>
</div>
@endsection