@extends('adminlte::page')

@section('template_title')
    Reportes
@endsection

@section('content')
<div class="container">
    <h2>Reporte de Métodos de Pago</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Método de Pago</th>
                <th>Total Ventas</th>
                <th>Monto Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($methods as $method)
            <tr>
                <td>{{ $method->nombre }}</td>
                <td>{{ $method->ventas->count() }}</td>
                <td>{{ $method->ventas->sum('total') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection