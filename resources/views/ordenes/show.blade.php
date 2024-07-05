@extends('adminlte::page')

@section('title', 'Detalle Orden de Compra')

@section('content_header')
    <h1>Detalle Orden de Compra: N° {{ $ordenCompra->numero_orden }}</h1>
@stop

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h4>Información del Proveedor:</h4>
                    <p>Nombre: {{ $ordenCompra->proveedor->nombre }}</p>
                    <p>RUT: {{ $ordenCompra->proveedor->rut }}</p>
                </div>
                <div class="col-md-6">
                    <h4>Detalles de la Orden:</h4>
                    <p>Estado: {{ $ordenCompra->estado }}</p>
                    <p>Fecha de Creación: {{ $ordenCompra->created_at->format('d/m/Y H:i:s') }}</p>

                </div>
            </div>

            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Compra</th>
                        <th>Inventario (Sucursal)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordenCompra->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>${{ number_format($detalle->precio_compra) }}</td>
                        <td>{{ $detalle->inventario->sucursal->nombre ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card-body {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
</style>
@endsection

@section('js')
<script>
    console.log('Detalles de la Orden de Compra cargados correctamente!');
</script>
@endsection

