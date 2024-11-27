@extends('adminlte::page')

@section('title', 'Crear Factura')

@section('content_header')
    <h1>Crear Factura</h1>
@stop

@section('content')
<div class="container">
    <!-- Mostrar errores de validación -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('facturas.store') }}" method="POST">
        @csrf
        <!-- Selección de Guía de Despacho -->
        <div class="card">
            <div class="card-header">
                <h3>Datos de la Factura</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="guia_despacho_id">Guía de Despacho</label>
                            <select class="form-control" id="guia_despacho_id" name="guia_despacho_id">
                            <option value="" selected>Sin Guía de Despacho</option>
                            @foreach($guiasDespacho as $guia)
                                <option value="{{ $guia->id }}">
                                    N°: {{ $guia->numero_guia }} - Proveedor: {{ $guia->ordenCompra->proveedor->nombre ?? 'Sin Proveedor' }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="proveedor">Proveedor</label>
                            <input type="text" id="proveedor" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero_factura">Número de Factura</label>
                            <input type="text" class="form-control" id="numero_factura" name="numero_factura" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                    <div class="form-group">
                        <label for="fecha_factura">Fecha de Factura</label>
                        <input type="date" class="form-control" id="fecha_factura" name="fecha_emision" required>
                    </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="total_factura">Total de la Factura</label>
                            <input type="text" class="form-control" id="total_factura" name="monto_total" oninput="recalcularTotales()">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="estado_pago">Estado de Pago</label>
                            <input type="text" class="form-control" id="estado_pago" name="estado_pago" value="pendiente" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles de la Factura -->
        <div class="card mt-3">
            <div class="card-header">
                <h4>Detalles de la Factura</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="detalles_factura">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad Solicitada</th>
                            <th>Cantidad Entregada</th>
                            <th>Precio Unitario</th>
                            <th>% Dcto</th>
                            <th>Descuento $</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los detalles de los productos se cargarán aquí mediante AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Total, IVA y Subtotal -->
        <div class="card mt-3">
            <div class="card-header">
                <h4>Resumen de Totales</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="subtotal_sin_iva">Total Neto</label>
                            <input type="text" class="form-control" id="subtotal_sin_iva" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="iva">IVA (19%)</label>
                            <input type="text" class="form-control" id="iva" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="total_con_iva">Total</label>
                            <input type="text" class="form-control" id="total_con_iva" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón de Guardado -->
        <button type="submit" class="btn btn-primary mt-3">Guardar Factura</button>
    </form>
</div>
@endsection

@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar Select2
    const selectGuiaDespacho = $('#guia_despacho_id');
    selectGuiaDespacho.select2({
        placeholder: 'Seleccione una Guía de Despacho(No tengo)',
        allowClear: true // Permite limpiar la selección
    });

    // Elementos DOM relacionados
    const tbody = document.querySelector('#detalles_factura tbody');
    const proveedorInput = document.getElementById('proveedor');
    const totalFacturaInput = document.getElementById('total_factura');
    const detallesFacturaSection = document.querySelector('#detalles_factura').closest('.card'); // Sección completa

    // Establecer la fecha actual
    const fechaFacturaInput = document.getElementById('fecha_factura');
    const today = new Date().toISOString().split('T')[0];
    fechaFacturaInput.value = today;

    // Función para actualizar la vista
    function actualizarVista() {
        if (!selectGuiaDespacho.val()) {
            // No hay guía seleccionada
            proveedorInput.value = 'Sin Proveedor';
            totalFacturaInput.readOnly = false; // Permitir edición de Total
            detallesFacturaSection.style.display = 'none'; // Ocultar detalles
        } else {
            // Hay guía seleccionada
            totalFacturaInput.readOnly = true; // Bloquear edición del Total
            detallesFacturaSection.style.display = 'block'; // Mostrar detalles
        }
    }

    // Inicializa la vista al cargar
    actualizarVista();

    // Evento change en el select de Guía de Despacho
    selectGuiaDespacho.on('change', function () {
        const guiaDespachoId = this.value;

        if (!guiaDespachoId) {
            // Si no hay guía seleccionada
            tbody.innerHTML = ''; // Limpiar los detalles
            proveedorInput.value = 'Sin Proveedor'; // Actualizar proveedor
            totalFacturaInput.value = ''; // Limpiar el total
            actualizarVista(); // Actualizar vista
            actualizarTotales(); // Limpiar totales
            return;
        }

        // Si hay guía seleccionada, cargar detalles
        fetch(`/api/facturas/${guiaDespachoId}/detalles`)
    .then(response => response.json())
    .then(data => {
        if (data && data.detalles) {
            const tbody = document.querySelector('#detalles_factura tbody');
            tbody.innerHTML = ''; // Limpiar tabla de detalles

            // Actualizar el proveedor
            document.getElementById('proveedor').value = data.proveedor || 'Sin proveedor';

            // Asignar el monto total desde la API al input de "total_factura"
            if (data.total) {
                document.getElementById('total_factura').value = data.total;
            } else {
                document.getElementById('total_factura').value = ''; // Si no hay total, limpiamos el campo
            }
                    // Iterar sobre los detalles y generar las filas
                    data.detalles.forEach(detalle => {
                        const precioCompra = parseFloat(detalle.precio_compra) || 0;
                        const cantidadSolicitada = parseFloat(detalle.cantidad_solicitada) || 0;
                        const cantidadEntregada = parseFloat(detalle.cantidad_entregada) || 0;
                        const subtotal = cantidadEntregada * precioCompra;

                        const row = `
                            <tr>
                                <td>${detalle.producto.nombre}</td>
                                <td><input type="text" class="form-control" value="${cantidadSolicitada}" readonly></td>
                                <td><input type="text" class="form-control cantidad-entregada" value="${cantidadEntregada}" readonly></td>
                                <td><input type="text" class="form-control precio-unitario" value="$${precioCompra.toFixed(0)}" readonly></td>
                                <td><input type="text" class="form-control porcentaje-descuento" value="0" oninput="calcularDescuento(this)"></td>
                                <td><input type="text" class="form-control valor-descuento" value="$0" oninput="calcularDescuento(this, true)"></td>
                                <td><input type="text" class="form-control subtotal" value="$${subtotal.toFixed(0)}" readonly></td>
                            </tr>
                        `;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });

                    actualizarVista(); // Actualizar vista
                    actualizarTotales(); // Actualizar totales
                } else {
                    console.error("Detalles de la factura no encontrados o mal estructurados");
                }
            })
            .catch(error => {
                console.error('Error al cargar los detalles de la factura:', error);
                actualizarVista(); // En caso de error, actualizar la vista
            });
    });
});

// Función para calcular descuentos
function calcularDescuento(element, esValor = false) {
    const row = element.closest('tr');
    const cantidadEntregada = parseFloat(row.querySelector('.cantidad-entregada').value) || 0;
    const precioUnitario = parseFloat(row.querySelector('.precio-unitario').value.replace('$', '')) || 0;
    let descuentoPorcentaje = parseFloat(row.querySelector('.porcentaje-descuento').value) || 0;
    let descuentoValor = parseFloat(row.querySelector('.valor-descuento').value.replace('$', '')) || 0;

    if (esValor) {
        descuentoPorcentaje = (descuentoValor / (cantidadEntregada * precioUnitario)) * 100;
        row.querySelector('.porcentaje-descuento').value = descuentoPorcentaje.toFixed(0);
    } else {
        descuentoValor = (cantidadEntregada * precioUnitario) * (descuentoPorcentaje / 100);
        row.querySelector('.valor-descuento').value = `$${descuentoValor.toFixed(0)}`;
    }

    const subtotalConDescuento = (cantidadEntregada * precioUnitario) - descuentoValor;
    row.querySelector('.subtotal').value = `$${subtotalConDescuento.toFixed(0)}`;

    actualizarTotales();
}

// Función para actualizar los totales
function actualizarTotales() {
    let totalConIva = 0;
    document.querySelectorAll('.subtotal').forEach(subtotal => {
        totalConIva += parseFloat(subtotal.value.replace('$', '')) || 0;
    });

    const subtotalSinIva = totalConIva / 1.19;
    const iva = totalConIva - subtotalSinIva;

    document.getElementById('subtotal_sin_iva').value = totalConIva > 0 ? `$${subtotalSinIva.toFixed(0)}` : '';
    document.getElementById('iva').value = totalConIva > 0 ? `$${iva.toFixed(0)}` : '';
    document.getElementById('total_con_iva').value = totalConIva > 0 ? `$${totalConIva.toFixed(0)}` : '';
}

// Función para recalcular totales al cambiar el Total de la Factura manualmente
function recalcularTotales() {
    const totalFacturaInput = document.getElementById('total_factura');
    const subtotalSinIvaInput = document.getElementById('subtotal_sin_iva');
    const ivaInput = document.getElementById('iva');
    const totalConIvaInput = document.getElementById('total_con_iva');

    const totalFactura = parseFloat(totalFacturaInput.value) || 0; // Lee el valor del campo "Total de la Factura"
    const subtotalSinIva = totalFactura / 1.19; // Calcula el subtotal sin IVA
    const iva = totalFactura - subtotalSinIva; // Calcula el IVA

    // Actualiza los campos
    subtotalSinIvaInput.value = `$${subtotalSinIva.toFixed(0)}`;
    ivaInput.value = `$${iva.toFixed(0)}`;
    totalConIvaInput.value = `$${totalFactura.toFixed(0)}`;
}

</script>
@endsection

@section('css')
<style>
.card.mt-3 {
    transition: all 0.3s ease-in-out;
}
</style>
@endsection