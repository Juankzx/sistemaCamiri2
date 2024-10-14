@extends('adminlte::page')

@section('title', 'Registrar Pago')

@section('content_header')
    <h1>Registrar Pago</h1>
@stop

@section('content')
<div class="container">
    <form action="{{ route('pagos.store') }}" method="POST">
        @csrf

        <div class="invoice p-3 mb-3">
            <!-- Title row -->
            <div class="row">
                <div class="col-12">
                    <h4>
                        <i class="fas fa-globe"></i> Registrar Pago
                        <small class="float-right">Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</small>
                    </h4>
                </div>
                <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    <div class="form-group">
                        <label for="factura_id">Factura</label>
                        <select class="form-control" id="factura_id" name="factura_id" onchange="loadInvoiceDetails()">
                            <option value="" disabled selected>Seleccione una Factura</option>
                            @foreach($facturas as $factura)
                                <option value="{{ $factura->id }}" data-factura="{{ json_encode($factura) }}">N°: {{ $factura->numero_factura }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    <address id="proveedor_info">
                        <strong>Seleccione una factura</strong><br>
                    </address>
                </div>
            </div>
            <!-- /.row -->

            <!-- Details row -->
            <div class="row">
                <div class="col-12">
                    <h5>Detalles de la Orden de Compra</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Compra</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detalles_factura">
                            <!-- Aquí se cargarán los detalles de la factura -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <!-- accepted payments column -->
                <div class="col-12">
                    <p class="lead">Métodos de Pago:</p>
                    <div class="form-group">
                        <label for="metodo_pago_id">Método de Pago</label>
                        <select class="form-control" id="metodo_pago_id" name="metodo_pago_id" required>
                            <option value="">Seleccione un método de pago</option>
                            @foreach ($metodosPago as $metodo)  <!-- Usar $metodosPago con el nombre correcto -->
                                <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="monto">Monto</label>
                        <input type="number" class="form-control" id="monto" name="monto" readonly>
                    </div>
                    <div class="form-group" id="transferencia-group" style="display: none;">
                        <label for="numero_transferencia">Número de Transferencia</label>
                        <input type="text" class="form-control" id="numero_transferencia" name="numero_transferencia">
                    </div>
                    <div class="form-group">
                        <label for="fecha_pago">Fecha de Pago</label>
                        <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" required>
                    </div>
                    <div class="form-group">
                        <label for="estado_pago">Estado de Pago</label>
                        <input type="text" class="form-control" id="estado_pago" name="estado_pago" value="pagado" readonly>
                    </div>
                </div>
            </div>
            <!-- /.row -->

            <!-- this row will not appear when printing -->
            <div class="row no-print">
                <div class="col-12">
                    <button type="submit" class="btn btn-success float-right"><i class="far fa-credit-card"></i> Registrar Pago</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function loadInvoiceDetails() {
    const facturaSelect = document.getElementById('factura_id');
    const facturaData = facturaSelect.options[facturaSelect.selectedIndex].getAttribute('data-factura');
    const factura = JSON.parse(facturaData);

    document.getElementById('monto').value = factura.total_factura;
    document.getElementById('proveedor_info').innerHTML = `
        <strong>${factura.guia_despacho.orden_compra.proveedor.nombre ?? 'No asignado'}</strong><br>
        RUT: ${factura.guia_despacho.orden_compra.proveedor.rut ?? 'No asignado'}
    `;

    const detallesContainer = document.getElementById('detalles_factura');
    detallesContainer.innerHTML = '';
    factura.guia_despacho.orden_compra.detalles.forEach(detalle => {
        detallesContainer.insertAdjacentHTML('beforeend', `
            <tr>
                <td>${detalle.producto.nombre}</td>
                <td>${detalle.cantidad}</td>
                <td>${detalle.precio_compra}</td>
                <td>${detalle.cantidad * detalle.precio_compra}</td>
            </tr>
        `);
    });
}

function toggleTransferencia() {
    const metodoPagoSelect = document.getElementById('metodo_pago_id');
    const transferenciaGroup = document.getElementById('transferencia-group');
    
    if (metodoPagoSelect.options[metodoPagoSelect.selectedIndex].text === 'Transferencia') {
        transferenciaGroup.style.display = 'block';
    } else {
        transferenciaGroup.style.display = 'none';
    }
}
</script>
@stop

@section('css')
<style>
    .invoice {
        margin: 20px;
        padding: 20px;
        background: #fff;
        border: 1px solid #dee2e6;
    }
</style>
@endsection
