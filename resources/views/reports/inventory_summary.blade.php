@extends('adminlte::page')

@section('template_title')
    Reportes
@endsection

@section('content')
<div class="container">
    <h2>Resumen de Inventario</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad en Inventario</th>
                <th>Bodega</th>
                <th>Sucursal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventories as $inventory)
            <tr>
                <td>{{ $inventory->producto->nombre }}</td>
                <td>{{ $inventory->cantidad }}</td>
                <td>{{ $inventory->bodega->nombre }}</td>
                <td>{{ $inventory->sucursal->nombre }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection