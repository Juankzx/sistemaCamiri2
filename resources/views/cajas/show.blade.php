@extends('adminlte::page')

@section('title', 'Detalle Caja')

@section('content_header')
    <h1>Detalle Caja: N° {{ $caja->id }}</h1>
@stop

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h4>Información de la Caja:</h4>
                    <p>Sucursal: {{ $caja->sucursal->nombre }}</p>
                    <p>Usuario: {{ $caja->user->name }}</p>
                    <p>Fecha Apertura: {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i:s') }}</p>
                    <p>Fecha Cierre: {{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y H:i:s') : 'N/A' }}</p>
                    <p>Monto Apertura: ${{ number_format($caja->monto_apertura, 0) }}</p>
                    <p>Monto Cierre: ${{ $caja->monto_cierre ? number_format($caja->monto_cierre, 0) : 'Sin Ventas' }}</p>
                    <p>Estado: {{ $caja->estado ? 'Abierta' : 'Cerrada' }}</p>
                </div>
                <div class="col-md-6">
                    <h4>Resumen de Ventas:</h4>
                    <p>Total Ventas: ${{ number_format($totalVentas, 0) }}</p>
                    <p>Ventas en Efectivo: {{ $ventasEfectivo }} (${{ number_format($totalEfectivo, 0) }})</p>
                    <p>Ventas con Tarjeta: {{ $ventasTarjeta }} (${{ number_format($totalTarjeta, 0) }})</p>
                </div>
            </div>

            <h4>Detalle de Ventas:</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead">
                        <tr>
                            <th>N°</th>
                            <th>Usuario</th>
                            <th>Sucursal</th>
                            <th>Método Pago</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ventas as $venta)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $venta->user->name }}</td>
                                <td>{{ $venta->sucursal->nombre }}</td>
                                <td>{{ $venta->metodo_pago->nombre }}</td>
                                <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i:s') }}</td>
                                <td>${{ number_format($venta->total, 0) }}</td>
                                <td>
                                    <a class="btn btn-sm btn-primary " href="{{ route('ventas.show', $venta->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .container {
        padding-top: 20px;
    }
</style>
@stop

@section('js')
<script>
    console.log('Detalles de la Caja cargados correctamente!');
</script>
@stop
