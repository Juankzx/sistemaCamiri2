@extends('adminlte::page')

@section('title', 'Detalle de la Guía de Despacho')

@section('content_header')
    <h1>Detalle de la Guía de Despacho: N° {{ $guia->numero_guia }}</h1>
@stop

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Detalles de la Guía de Despacho</h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4 class="text-primary">Información del Proveedor</h4>
                    <p><strong>Nombre:</strong> {{ $guia->ordenCompra->proveedor->nombre ?? 'No asignado' }}</p>
                    <p><strong>RUT:</strong> {{ $guia->ordenCompra->proveedor->rut ?? 'No asignado' }}</p>
                </div>
                <div class="col-md-6">
                    <h4 class="text-primary">Detalles de la Guía</h4>
                    <p><strong>Estado:</strong> <span class="badge badge-info">{{ $guia->estado }}</span></p>
                    <p><strong>Fecha de Entrega:</strong> {{ $guia->fecha_entrega ? \Carbon\Carbon::parse($guia->fecha_entrega)->format('d/m/Y') : 'N/A' }}</p>
                </div>
            </div>

            @if ($guia->ordenCompra)
                <h4 class="text-primary">Detalles de la Orden de Compra</h4>
                <table class="table table-hover table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Bruto</th>
                            <th>Precio Neto</th>
                            <th>IVA</th>
                            <th>Subtotal</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalNeto = 0;
                            $totalIVA = 0;
                            $totalCompra = 0;
                        @endphp
                        @foreach($guia->ordenCompra->detalles as $detalle)
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
                            <td>${{ number_format($iva, 0) }}</td>
                            <td>${{ number_format($subtotal, 0) }}</td>
                            <td>${{ number_format($detalle->precio_compra * $detalle->cantidad, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right">Total Neto:</th>
                            <th>${{ number_format($totalNeto, 0) }}</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th colspan="6" class="text-right">Total IVA:</th>
                            <th>${{ number_format($totalIVA, 0) }}</th>
                        </tr>
                        <tr>
                            <th colspan="6" class="text-right">Total Compra:</th>
                            <th colspan="2">${{ number_format($totalCompra, 0) }}</th>
                        </tr>
                    </tfoot>
                </table>
            @else
                <p>No hay detalles de la orden de compra asociados.</p>
            @endif
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
    console.log('Detalle de la Guía de Despacho cargado correctamente!');
</script>
@endsection
