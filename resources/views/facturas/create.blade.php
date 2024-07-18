@extends('adminlte::page')

@section('title', 'Crear Factura')

@section('content_header')
    <h1>Crear Factura</h1>
@stop

@section('content')
<div class="container">
    <form action="{{ route('facturas.store') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Datos de la Factura</h3>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="guia_despacho_id">Guía de Despacho</label>
                        <select class="form-control" id="guia_despacho_id" name="guia_despacho_id" onchange="loadInvoiceDetails()">
                            <option value="" disabled selected>Seleccione una Guía de Despacho</option>
                            @foreach($guiasDespacho as $guia)
                                <option value="{{ $guia->id }}">N°: {{ $guia->numero_guia }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="proveedor_info">Proveedor</label>
                        <input type="text" class="form-control" id="proveedor_info" name="proveedor_info" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="numero_factura">Número de Factura</label>
                    <input type="text" class="form-control" id="numero_factura" name="numero_factura" required>
                </div>
                <div class="form-group">
                    <label for="fecha_factura">Fecha de Factura</label>
                    <input type="date" class="form-control" id="fecha_factura" name="fecha_factura" required>
                </div>
                <div class="form-group">
                    <label for="estado_pago">Estado de Pago</label>
                    <input type="text" class="form-control" id="estado_pago" name="estado_pago" value="pendiente" readonly>
                </div>
                <div id="invoiceDetails" class="mt-4">
                    <!-- Aquí se cargarán los detalles de la factura -->
                </div>
                <div class="form-group mt-3">
                    <label for="total_factura">Total de Factura</label>
                    <input type="number" class="form-control" id="total_factura" name="total_factura" readonly>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Guardar Factura</button>
            </div>
        </div>
    </form>
</div>

<script>
function loadInvoiceDetails() {
    const guiaDespachoId = document.getElementById('guia_despacho_id').value;
    if (guiaDespachoId) {
        fetch(`/api/guias-despacho/${guiaDespachoId}/detalles`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('invoiceDetails');
                container.innerHTML = '';

                let totalFactura = 0;

                data.detalles.forEach((detalle, index) => {
                    const subtotal = detalle.precio_compra * detalle.cantidad;
                    totalFactura += subtotal;

                    const html = `
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="producto-${index}">Producto</label>
                                <input type="text" class="form-control" id="producto-${index}" name="productos[${index}][nombre]" value="${detalle.producto.nombre}" readonly>
                            </div>
                            <div class="col-md-2">
                                <label for="cantidad-${index}">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad-${index}" name="productos[${index}][cantidad]" value="${detalle.cantidad}" required>
                            </div>
                            <div class="col-md-2">
                                <label for="precio_compra-${index}">Precio Compra</label>
                                <input type="number" class="form-control" id="precio_compra-${index}" name="productos[${index}][precio_compra]" value="${detalle.precio_compra}" required>
                            </div>
                            <div class="col-md-2">
                                <label for="subtotal-${index}">Subtotal</label>
                                <input type="number" class="form-control" id="subtotal-${index}" name="productos[${index}][subtotal]" value="${subtotal}" readonly>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                });

                document.getElementById('total_factura').value = totalFactura;

                // Actualizar información del proveedor
                const proveedor = data.proveedor;
                const proveedorInfo = `${proveedor.nombre} - ${proveedor.rut}`;
                document.getElementById('proveedor_info').value = proveedorInfo;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
}
</script>
@stop
