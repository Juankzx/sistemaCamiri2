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
                    <select class="form-control" id="guia_despacho_id" name="guia_despacho_id">
                        <option value="" disabled selected>Seleccione una Guía de Despacho</option>
                        @foreach($guiasDespacho as $guia)
                            <option value="{{ $guia->id }}" data-orden-compra-id="{{ $guia->orden_compra_id }}">
                                N°: {{ $guia->numero_guia }}
                            </option>
                        @endforeach
                    </select>
                </div>
                    <div class="form-group col-md-6">
                        <label for="proveedor_info">Proveedor</label>
                        <input type="text" class="form-control" id="proveedor_info" name="proveedor_info" readonly>
                    </div>
                </div>
                
                <!-- Campo oculto para el ID de la orden de compra -->
                <input type="hidden" id="orden_compra_id" name="orden_compra_id" value="">

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
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const guiaSelectElement = document.getElementById('guia_despacho_id');
    const ordenCompraInput = document.getElementById('orden_compra_id');
    const proveedorInfoInput = document.getElementById('proveedor_info');
    const totalFacturaInput = document.getElementById('total_factura');
    const invoiceDetailsContainer = document.getElementById('invoiceDetails');

    // Verificar que los elementos HTML existan antes de intentar asignar eventos
    if (!guiaSelectElement || !ordenCompraInput || !proveedorInfoInput || !totalFacturaInput || !invoiceDetailsContainer) {
        console.error("Error: No se encontraron uno o más elementos requeridos en el DOM.");
        return;
    }

    // Asignar evento para actualizar el campo oculto de la orden de compra al seleccionar la guía de despacho
    guiaSelectElement.addEventListener('change', function () {
        const guiaDespachoId = this.value;
        const selectedOption = document.querySelector(`#guia_despacho_id option[value='${guiaDespachoId}']`);
        const ordenCompraId = selectedOption ? selectedOption.dataset.ordenCompraId : null;

        if (ordenCompraId) {
            ordenCompraInput.value = ordenCompraId;
        } else {
            console.error("Error: No se pudo asignar el ID de la orden de compra. Revisa los datos en la opción seleccionada.");
            ordenCompraInput.value = ""; // Limpiar el campo oculto en caso de error
        }

        // Cargar los detalles de la factura correspondientes a la guía de despacho seleccionada
        loadInvoiceDetails(guiaDespachoId);
    });

    // Función para cargar detalles de la guía de despacho seleccionada
    function loadInvoiceDetails(guiaDespachoId) {
        // Validar que la guía de despacho seleccionada tenga un valor
        if (!guiaDespachoId) {
            console.error("No se seleccionó una guía de despacho válida.");
            clearInvoiceDetails();
            return;
        }

        fetch(`/api/guias-despacho/${guiaDespachoId}/detalles`)
            .then(response => response.json())
            .then(data => {
                // Validar que la respuesta contenga datos
                if (data && data.detalles && data.detalles.length > 0) {
                    displayInvoiceDetails(data);
                } else {
                    console.warn("No se encontraron detalles para la guía de despacho seleccionada.");
                    clearInvoiceDetails();
                }
            })
            .catch(error => {
                console.error('Error al cargar los detalles de la guía de despacho:', error);
                clearInvoiceDetails();
            });
    }

    // Función para mostrar los detalles de la factura en el contenedor
    function displayInvoiceDetails(data) {
        invoiceDetailsContainer.innerHTML = ''; // Limpiar el contenedor antes de mostrar nuevos detalles

        let totalFactura = 0;

        // Recorrer cada detalle para construir los campos en la interfaz
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
                        <input type="number" class="form-control" id="cantidad-${index}" name="productos[${index}][cantidad]" value="${detalle.cantidad}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label for="precio_compra-${index}">Precio Compra</label>
                        <input type="number" class="form-control" id="precio_compra-${index}" name="productos[${index}][precio_compra]" value="${detalle.precio_compra}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label for="subtotal-${index}">Subtotal</label>
                        <input type="number" class="form-control" id="subtotal-${index}" name="productos[${index}][subtotal]" value="${subtotal}" readonly>
                    </div>
                </div>
            `;
            invoiceDetailsContainer.insertAdjacentHTML('beforeend', html);
        });

        // Asignar el total de la factura al campo correspondiente
        totalFacturaInput.value = totalFactura;

        // Actualizar la información del proveedor en el campo de proveedor_info
        const proveedor = data.proveedor;
        const proveedorInfo = proveedor ? `${proveedor.nombre} - ${proveedor.rut}` : 'Proveedor desconocido';
        proveedorInfoInput.value = proveedorInfo;
    }

    // Función para limpiar los detalles de la factura en la vista
    function clearInvoiceDetails() {
        invoiceDetailsContainer.innerHTML = '<p class="text-muted">No se encontraron detalles para mostrar.</p>';
        totalFacturaInput.value = 0;
        proveedorInfoInput.value = '';
    }
});
</script>
@stop
