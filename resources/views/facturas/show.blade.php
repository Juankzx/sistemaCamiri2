@extends('adminlte::page')

@section('title', 'Detalle de la Factura')

@section('content_header')
    <h1>Detalle de la Factura: N° {{ $factura->numero_factura }}</h1>
@stop

@section('content')
<div class="container">
    <!-- Información General de la Factura -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4>Información de la Factura</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Detalles de la Factura -->
                <div class="col-md-6">
                    <h5 class="text-primary">Detalles de la Factura</h5>
                    <p><strong>Número de Factura:</strong> {{ $factura->numero_factura }}</p>
                    <p><strong>Fecha de Factura:</strong> 
                        {{ $factura->fecha_emision ? \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y') : 'No disponible' }}
                    </p>
                    <p><strong>Total Factura:</strong> ${{ number_format($factura->monto_total, 0) }}</p>
                    <p><strong>Estado de Pago:</strong> 
                        <span class="badge {{ $factura->estado_pago === 'pendiente' ? 'badge-danger' : 'badge-success' }}">
                            {{ ucfirst($factura->estado_pago) }}
                        </span>
                    </p>
                </div>
                <!-- Información del Proveedor -->
                <div class="col-md-6">
                    <h5 class="text-primary">Información del Proveedor</h5>
                    @if ($factura->guiaDespacho && $factura->guiaDespacho->ordenCompra)
                        <p><strong>Nombre:</strong> {{ $factura->guiaDespacho->ordenCompra->proveedor->nombre ?? 'No disponible' }}</p>
                        <p><strong>RUT:</strong> {{ $factura->guiaDespacho->ordenCompra->proveedor->rut ?? 'No disponible' }}</p>
                    @else
                        <p>No hay información de proveedor disponible</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de los Productos -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h4>Detalles de los Productos</h4>
        </div>
        <div class="card-body">
            @if ($detalles->isNotEmpty())
                <table class="table table-hover table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad Entregada</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detalles as $detalle)
                        <tr>
                            <td>{{ $detalle['producto'] }}</td>
                            <td>{{ $detalle['cantidad_entregada'] }}</td>
                            <td>${{ number_format($detalle['precio_unitario'], 0) }}</td>
                            <td>${{ number_format($detalle['subtotal'], 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No hay productos disponibles para esta factura.</p>
            @endif
        </div>
    </div>

    <!-- Resumen de Totales -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h4>Resumen de Totales</h4>
        </div>
        <div class="card-body">
            <p><strong>Total Neto:</strong> ${{ number_format($totalNeto, 0) }}</p>
            <p><strong>IVA (19%):</strong> ${{ number_format($totalIVA, 0) }}</p>
            <p><strong>Total Factura:</strong> ${{ number_format($totalFactura, 0) }}</p>
        </div>
    </div>

    <!-- Botón de Volver -->
    <div class="text-right">
        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>
@stop


@section('css')
<style>
    .card {
        margin-bottom: 20px;
    }
    .card-header {
        font-size: 1.2rem;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }
    .badge-danger {
        background-color: #dc3545;
    }
    .badge-success {
        background-color: #28a745;
    }
</style>
@endsection
