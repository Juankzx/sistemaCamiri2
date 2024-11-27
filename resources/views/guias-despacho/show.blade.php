@extends('adminlte::page')

@section('title', 'Detalles de la Guía de Despacho')

@section('content_header')
    <h1>Detalles de la Guía de Despacho</h1>
@stop

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Guía de Despacho N° {{ $guiaDespacho->id }}</h4>
        </div>
        <div class="card-body">
            <!-- Información general -->
            <ul class="list-group mb-4">
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <strong>Proveedor:</strong>
        <span>{{ $guiaDespacho->ordenCompra->proveedor->nombre ?? 'N/A' }}</span> <!-- Fallback para proveedor -->
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <strong>Fecha de Entrega:</strong>
        <span>{{ \Carbon\Carbon::parse($guiaDespacho->fecha_entrega)->format('d/m/Y H:i') }}</span>
    </li>
</ul>

<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>Producto</th>
            <th>Cantidad Solicitada</th>
            <th>Cantidad Entregada</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detalles as $detalle)
            <tr>
                <td>{{ $detalle['producto'] }}</td>
                <td>{{ $detalle['cantidad_solicitada'] }}</td> <!-- Ya tiene fallback en el controlador -->
                <td>{{ $detalle['cantidad_entregada'] }}</td>
                <td>${{ number_format($detalle['precio_unitario'], 0) }}</td>
                <td>${{ number_format($detalle['subtotal'], 0) }}</td>
            </tr>
        @endforeach
        <!-- Totales al final de la tabla -->
        <tr class="font-weight-bold bg-light">
            <td>Totales</td>
            <td>{{ $totalCantidadSolicitada }}</td>
            <td>{{ $totalCantidadEntregada }}</td>
            <td></td>
            <td>${{ number_format($totalMonto, 0) }}</td>
        </tr>
    </tbody>
</table>

        </div>
        <div class="card-footer">
            <a href="{{ route('guias-despacho.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>
@endsection
