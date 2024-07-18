@extends('adminlte::page')

@section('title', 'Detalle Orden de Compra')

@section('content_header')
    <h1>Detalle Orden de Compra: N° {{ $ordenCompra->numero_orden }}</h1>
@stop

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Detalles de la Orden de Compra</h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4 class="text-primary">Información del Proveedor</h4>
                    <p><strong>Nombre:</strong> {{ $ordenCompra->proveedor->nombre }}</p>
                    <p><strong>RUT:</strong> {{ $ordenCompra->proveedor->rut }}</p>
                </div>
                <div class="col-md-6">
                    <h4 class="text-primary">Detalles de la Orden</h4>
                    <p><strong>Estado:</strong> <span class="badge badge-info">{{ $ordenCompra->estado }}</span></p>
                    <p><strong>Fecha de Creación:</strong> {{ $ordenCompra->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>

            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Neto</th>
                        <th>Precio Compra (con IVA)</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalNeto = 0;
                        $totalIVA = 0;
                        $totalCompra = 0;
                    @endphp
                    @foreach($ordenCompra->detalles as $detalle)
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
    console.log('Detalles de la Orden de Compra cargados correctamente!');
</script>
@endsection
