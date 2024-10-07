@extends('adminlte::page')

@section('template_title')
    Reportes
@endsection

@section('content')
<div class="container">
    <h2>Reporte de Compras</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
            <tr>
                <td>{{ $purchase->created_at }}</td>
                <td>{{ $purchase->proveedor->nombre }}</td>
                <td>{{ $purchase->producto->nombre }}</td>
                <td>{{ $purchase->cantidad }}</td>
                <td>{{ $purchase->total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection