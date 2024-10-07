@extends('adminlte::page')

@section('template_title')
    Reportes
@endsection

@section('content')
<div class="container">
    <h2>Reporte de Ventas por Tiempo</h2>
    <p>Desde: {{ $start_date }} Hasta: {{ $end_date }}</p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
            <tr>
                <td>{{ $sale->created_at }}</td>
                <td>{{ $sale->producto->nombre }}</td>
                <td>{{ $sale->cantidad }}</td>
                <td>{{ $sale->total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection