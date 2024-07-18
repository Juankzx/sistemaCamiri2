@extends('adminlte::page')

@section('title', 'Detalle de la Factura')

@section('content_header')
    <h1>Detalle de la Factura: N° {{ $factura->numero_factura }}</h1>
@stop

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Información de la Factura</h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4 class="text-primary">Detalles de la Factura</h4>
                    <p><strong>Número de Factura:</strong> {{ $factura->numero_factura }}</p>
                    <p><strong>Fecha de Factura:</strong> {{ \Carbon\Carbon::parse($factura->fecha_factura)->format('d/m/Y') }}</p>
                    <p><strong>Total Factura:</strong> ${{ number_format($factura->total_factura, 0) }}</p>
                    <p><strong>Estado de Pago:</strong> 
                        <span class="badge {{ $factura->estado_pago === 'pendiente' ? 'badge-danger' : 'badge-success' }}">
                            {{ ucfirst($factura->estado_pago) }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <h4 class="text-primary">Información del Proveedor</h4>
                    <p><strong>Nombre:</strong> {{ $factura->guiaDespacho->ordenCompra->proveedor->nombre ?? 'No asignado' }}</p>
                    <p><strong>RUT:</strong> {{ $factura->guiaDespacho->ordenCompra->proveedor->rut ?? 'No asignado' }}</p>
                </div>
            </div>

            <h4 class="text-primary">Detalles de la Guía de Despacho</h4>
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Número de Guía:</strong> {{ $factura->guiaDespacho->numero_guia }}</p>
                    <p><strong>Estado Guia de Despacho:</strong> 
                        <span class="badge {{ $factura->guiaDespacho->estado === 'emitida' ? 'badge-danger' : 'badge-success' }}">
                            {{ ucfirst($factura->guiaDespacho->estado) }}
                        </span>
                    </p>
                    <p><strong>Fecha de Entrega:</strong> {{ \Carbon\Carbon::parse($factura->guiaDespacho->fecha_entrega)->format('d/m/Y') }}</p>
                </div>
            </div>

            <h4 class="text-primary">Detalles de la Orden de Compra</h4>
            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Bruto</th>
                        <th>Precio Neto</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalNeto = 0;
                        $totalIVA = 0;
                        $totalCompra = 0;
                    @endphp
                    @foreach($factura->guiaDespacho->ordenCompra->detalles as $detalle)
                    @php
                        $precioNeto = $detalle->precio_compra / 1.19; // Asumiendo 19% de IVA
                        $subtotal = $precioNeto * $detalle->cantidad;
                        $iva = $detalle->precio_compra * $detalle->cantidad - $subtotal;
                        $totalNeto += $subtotal;
                        $totalIVA += $iva;
                        $totalCompra += $detalle->precio_compra * $detalle->cantidad;
                    @endphp
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>${{ number_format($precioNeto, 0) }}</td>
                        <td>${{ number_format($detalle->precio_compra, 0) }}</td>
                        <td>${{ number_format($subtotal, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Total Neto:</th>
                        <th>${{ number_format($totalNeto, 0) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">Total IVA:</th>
                        <th>${{ number_format($totalIVA, 0) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">Total Compra:</th>
                        <th>${{ number_format($totalCompra, 0) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card {
        margin-top: 20px;
    }
    .card-header {
        background-color: #007bff;
        color: white;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }
    .badge-info {
        background-color: #17a2b8;
    }
</style>
@endsection

@section('js')
<script>
    console.log('Detalle de la Factura cargado correctamente!');
</script>
@endsection
