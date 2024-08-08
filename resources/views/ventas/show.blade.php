@extends('adminlte::page')

@section('title', 'Detalle Venta')

@section('content_header')
    <h1>Detalle Venta: N° {{ $venta->id }}</h1>
@stop

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h4>Información del Vendedor:</h4>
                    <p>Nombre: {{ $venta->user->name }}</p>
                    <p>Email: {{ $venta->user->email }}</p>
                </div>
                <div class="col-md-6">
                    <h4>Detalles de la Venta:</h4>
                    <p>Método de Pago: {{ $venta->metodo_pago->nombre }}</p>
                    <p>Fecha de Venta: {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i:s') }}</p>
                    <p>Sucursal: {{ $venta->sucursal->nombre }}</p>
                </div>
            </div>

            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario (con IVA)</th>
                        <th>SubTotal (con IVA)</th>
                        <th>Inventario (Sucursal)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalConIva = 0;
                        $totalSinIva = 0;
                    @endphp
                    @foreach($venta->detallesVenta as $detalle)
                    @php
                        $precioUnitarioSinIva = $detalle->precio_unitario / 1.19;
                        $subtotalSinIva = $detalle->cantidad * $precioUnitarioSinIva;
                        $subtotalConIva = $detalle->cantidad * $detalle->precio_unitario;
                        $totalSinIva += $subtotalSinIva;
                        $totalConIva += $subtotalConIva;
                    @endphp
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>${{ number_format($detalle->precio_unitario, 0) }}</td>
                        <td>${{ number_format($subtotalConIva, 0) }}</td>
                        <td>{{ $detalle->inventarios->sucursal->nombre }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <th colspan="3">Total Bruto (sin IVA)</th>
                        <th>${{ number_format($totalSinIva, 0) }}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="3">IVA (19%)</th>
                        <th>${{ number_format($totalSinIva * 0.19, 0) }}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="3">Total Neto (con IVA)</th>
                        <th>${{ number_format($totalConIva, 0) }}</th>
                        <th></th>
                    </tr>
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
    console.log('Detalles de la Venta cargados correctamente!');
</script>
@endsection
