@extends('adminlte::page')

@section('title', 'Detalle del Pago')

@section('content_header')
    <h1>Detalle del Pago</h1>
@stop

@section('content')
<div class="container">
    <div class="invoice p-3 mb-3">
        <!-- Title row -->
        <div class="row">
            <div class="col-12">
                <h4>
                    <i class="fas fa-globe"></i> Detalle del Pago
                    <small class="float-right">Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</small>
                </h4>
            </div>
            <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                Factura:
                <address>
                    <strong>Número de Factura: {{ $pago->factura->numero_factura }}</strong><br>
                    Fecha: {{ \Carbon\Carbon::parse($pago->factura->fecha_factura)->format('d/m/Y') }}<br>
                    Total: ${{ number_format($pago->factura->total_factura, 0) }}<br>
                    Estado: <span class="badge {{ $pago->factura->estado_pago === 'pendiente' ? 'badge-danger' : 'badge-success' }}">
                        {{ ucfirst($pago->factura->estado_pago) }}
                    </span>
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                Proveedor:
                <address>
                    <strong>{{ $pago->factura->guiaDespacho->ordenCompra->proveedor->nombre ?? 'No asignado' }}</strong><br>
                    RUT: {{ $pago->factura->guiaDespacho->ordenCompra->proveedor->rut ?? 'No asignado' }}<br>
                </address>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Compra</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pago->factura->guiaDespacho->ordenCompra->detalles as $detalle)
                        @php
                            $subtotal = $detalle->precio_compra * $detalle->cantidad;
                        @endphp
                        <tr>
                            <td>{{ $detalle->producto->nombre }}</td>
                            <td>{{ $detalle->cantidad }}</td>
                            <td>${{ number_format($detalle->precio_compra, 0) }}</td>
                            <td>${{ number_format($subtotal, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total Compra:</th>
                            <th>${{ number_format($pago->factura->total_factura, 0) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
            <!-- accepted payments column -->
            <div class="col-6">
                <p class="lead">Método de Pago:</p>
                <p>{{ $pago->metodoPago->nombre }}</p>
                @if($pago->metodo_pago_id == 2) <!-- Asumiendo que el id 2 corresponde a Transferencia -->
                <p><strong>Número de Transferencia:</strong> {{ $pago->numero_transferencia ?? 'N/A' }}</p>
                @endif
            </div>
            <!-- /.col -->
            <div class="col-6">
                <p class="lead">Fecha de Pago: {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</p>

                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th style="width:50%">Monto:</th>
                            <td>${{ number_format($pago->monto, 0) }}</td>
                        </tr>
                        <tr>
                            <th>Estado de Pago:</th>
                            <td><span class="badge {{ $pago->estado_pago == 'pendiente' ? 'badge-danger' : 'badge-success' }}">{{ ucfirst($pago->estado_pago) }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- this row will not appear when printing -->
        <div class="row no-print">
            <div class="col-12">
                <a href="{{ route('pagos.index') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Volver</a>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .invoice {
        margin: 20px;
        padding: 20px;
        background: #fff;
        border: 1px solid #dee2e6;
    }
    .badge-danger { background-color: #dc3545; }
    .badge-success { background-color: #28a745; }
</style>
@endsection
