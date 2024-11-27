@extends('adminlte::page')

@section('title', 'Detalle del Pago')

@section('content_header')
    <h1>Detalle del Pago: #{{ $pago->id }}</h1>
@stop

@section('content')
<div class="container">
    <!-- Información General del Pago -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4>Información del Pago</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Información de la Factura -->
                <div class="col-md-6">
                    <h5 class="text-primary">Factura Asociada</h5>
                    <p><strong>Número de Factura:</strong> {{ $pago->factura->numero_factura }}</p>
                    <p><strong>Fecha de Factura:</strong> 
                        {{ $pago->factura->fecha_emision ? \Carbon\Carbon::parse($pago->factura->fecha_emision)->format('d/m/Y') : 'No disponible' }}
                    </p>
                    <p><strong>Total Factura:</strong> ${{ number_format($pago->factura->monto_total, 0) }}</p>
                    <p><strong>Estado de Factura:</strong> 
                        <span class="badge {{ $pago->factura->estado_pago === 'pendiente' ? 'badge-danger' : 'badge-success' }}">
                            {{ ucfirst($pago->factura->estado_pago) }}
                        </span>
                    </p>
                </div>
                <!-- Información del Proveedor -->
                <div class="col-md-6">
                    <h5 class="text-primary">Información del Proveedor</h5>
                    @if ($pago->factura->guiaDespacho && $pago->factura->guiaDespacho->ordenCompra)
                        <p><strong>Nombre:</strong> {{ $pago->factura->guiaDespacho->ordenCompra->proveedor->nombre ?? 'No disponible' }}</p>
                        <p><strong>RUT:</strong> {{ $pago->factura->guiaDespacho->ordenCompra->proveedor->rut ?? 'No disponible' }}</p>
                    @else
                        <p>No hay información de proveedor disponible</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

   <!-- Detalles de los Productos -->
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <h4>Detalles de los Productos</h4>
    </div>
    <div class="card-body">
        @if ($detalles->isNotEmpty())
        <table class="table table-hover table-striped">
    <thead class="thead-dark">
        <tr>
            <th>Producto</th>
            <th>Cantidad Solicitada</th>
            <th>Cantidad Entregada</th>
            <th>Precio Compra</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detalles as $detalle)
        <tr>
            <td>{{ $detalle['producto'] }}</td>
            <td>{{ $detalle['cantidad_solicitada'] }}</td>
            <td>{{ $detalle['cantidad_entregada'] }}</td>
            <td>${{ number_format($detalle['precio_compra'], 0) }}</td>
            <td>${{ number_format($detalle['subtotal'], 0) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-right">Total Neto:</th>
            <th>${{ number_format($totalNeto, 0) }}</th>
        </tr>
        <tr>
            <th colspan="4" class="text-right">IVA (19%):</th>
            <th>${{ number_format($iva, 0) }}</th>
        </tr>
        <tr>
            <th colspan="4" class="text-right">Total Factura:</th>
            <th>${{ number_format($totalFactura, 0) }}</th>
        </tr>
    </tfoot>
</table>

        @else
            <p>No hay productos asociados a esta guía de despacho.</p>
        @endif
    </div>
</div>


    <!-- Información del Pago -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h4>Detalles del Pago</h4>
        </div>
        <div class="card-body">
            <p><strong>Monto Pagado:</strong> ${{ number_format($pago->monto, 0) }}</p>
            <p><strong>Fecha de Pago:</strong> {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</p>
            <p><strong>Método de Pago:</strong> {{ $pago->metodoPago->nombre }}</p>
            @if ($pago->metodo_pago_id == 2)
                <p><strong>Número de Transferencia:</strong> {{ $pago->numero_transferencia ?? 'N/A' }}</p>
            @endif
            <p><strong>Estado del Pago:</strong> 
                <span class="badge {{ $pago->estado_pago === 'pendiente' ? 'badge-danger' : 'badge-success' }}">
                    {{ ucfirst($pago->estado_pago) }}
                </span>
            </p>
            <p><strong>Descripción:</strong> {{ $pago->descripcion ?? 'No disponible' }}</p>
        </div>
    </div>

    <!-- Botón de Volver -->
    <div class="text-right">
        <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
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
