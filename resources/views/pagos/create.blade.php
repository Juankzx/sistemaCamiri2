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
            </div>

            <!-- Info row -->
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    <div class="form-group">
                        <label for="factura_id">Factura</label>
                        <select class="form-control" id="factura_id" name="factura_id" onchange="loadInvoiceDetails()">
                            <option value="" disabled selected>Seleccione una Factura</option>
                            @foreach($facturas as $factura)
                                <option value="{{ $factura->id }}">N°: {{ $factura->numero_factura }} - Proveedor: {{ $factura->guiaDespacho->ordenCompra->proveedor->nombre ?? 'Sin proveedor' }} - Total: ${{ $factura->monto_total }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-4 invoice-col">
                    <address id="proveedor_info">
                        <strong>Seleccione una factura</strong><br>
                    </address>
                </div>
            </div>

            <!-- Details row -->
            <div id="detalles_factura_container" style="display: none;">
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

            <div class="row">
                <!-- Payment method column -->
                <div class="col-12">
                    <div class="form-group">
                        <label for="metodo_pago_id">Método de Pago</label>
                        <select class="form-control" id="metodo_pago_id" name="metodo_pago_id" onchange="toggleTransferencia()" required>
                            <option value="">Seleccione un método de pago</option>
                            @foreach ($metodosPago as $metodo)
                                <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="descripcion-group" style="display: none;">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Ingrese una descripción para el pago"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="monto">Monto</label>
                        <input type="number" class="form-control" id="monto" name="monto" readonly>
                    </div>
                    <div class="form-group" id="transferencia-group" style="display: none;">
                        <label for="numero_transferencia">Número de Transferencia</label>
                        <input type="text" class="form-control" id="numero_transferencia" name="numero_transferencia" placeholder="Ingrese el número de transferencia">
                    </div>
                    <div class="form-group">
                        <label for="fecha_pago">Fecha de Pago</label>
                        <input type="datetime-local" class="form-control" id="fecha_pago" name="fecha_pago" required>
                    </div>
                    <div class="form-group">
                        <label for="estado_pago">Estado de Pago</label>
                        <input type="text" class="form-control" id="estado_pago" name="estado_pago" value="pagado" readonly>
                    </div>
                </div>
            </div>

            <!-- Submit button -->
            <div class="row no-print">
                <div class="col-12">
                    <button type="submit" class="btn btn-success float-right"><i class="far fa-credit-card"></i> Registrar Pago</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    
    document.addEventListener('DOMContentLoaded', function () {
    // Inicializar select2 para el campo de facturas
    $('#factura_id').select2({
        placeholder: 'Seleccione una Factura',
        allowClear: true,
        width: '100%',
        templateResult: function (data) {
            if (!data.id) {
                return data.text;
            }
            const [numeroFactura, proveedor, total] = data.text.split(' - ');
            return $(`
                <div>
                    <strong>${numeroFactura}</strong><br>
                    <small>${proveedor}</small><br>
                    <small>Total: ${total}</small>
                </div>
            `);
        },
        templateSelection: function (data) {
            return data.text ? data.text.split(' - ')[0] : ''; // Mostrar solo el número de factura
        }
    });

    // Establecer fecha y hora actual al iniciar
    const fechaPagoInput = document.getElementById('fecha_pago');
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    fechaPagoInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;

    // Simular el cambio inicial en el selector para configurar la vista inicial
    document.getElementById('factura_id').dispatchEvent(new Event('change'));
});

// Función para manejar el cambio en el selector de facturas
function loadInvoiceDetails() {
    const facturaSelect = document.getElementById('factura_id');
    const facturaId = facturaSelect.value;
    const descripcionGroup = document.getElementById('descripcion-group');
    const detallesContainer = document.getElementById('detalles_factura_container');
    const detallesTableBody = document.getElementById('detalles_factura');
    const montoInput = document.getElementById('monto');
    const proveedorInfo = document.getElementById('proveedor_info');
    const descripcionField = document.getElementById('descripcion');

    if (!facturaId) {
        // Si no hay factura seleccionada, mostrar descripción y habilitar el monto
        descripcionGroup.style.display = 'block';
        descripcionField.required = true;
        montoInput.readOnly = false;

        // Limpiar los detalles y proveedor
        detallesTableBody.innerHTML = '';
        montoInput.value = '';
        proveedorInfo.innerHTML = '<strong>Sin proveedor</strong><br>';
        detallesContainer.style.display = 'none';
        return;
    }

    // Si hay factura seleccionada, ocultar descripción y deshabilitar el monto
    descripcionGroup.style.display = 'none';
    descripcionField.required = false;
    montoInput.readOnly = true;

    // Obtener detalles de la factura desde el servidor
    fetch(`/api/pagos/${facturaId}/detalles`)
        .then(response => response.json())
        .then(data => {
            const detalles = data.detalles;
            detallesTableBody.innerHTML = ''; // Limpiar los detalles previos

            // Cargar los detalles en la tabla si existen
            if (detalles && detalles.length > 0) {
                detalles.forEach(detalle => {
                    detallesTableBody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${detalle.producto}</td>
                            <td>${detalle.cantidad_entregada}</td>
                            <td>${detalle.precio_compra}</td>
                            <td>${detalle.subtotal}</td>
                        </tr>
                    `);
                });
                detallesContainer.style.display = 'block'; // Mostrar el contenedor
            } else {
                detallesContainer.style.display = 'none'; // Ocultar si no hay productos
            }

            // Mostrar datos generales de la factura
            montoInput.value = data.monto_total || 0;
            proveedorInfo.innerHTML = `
                <strong>${data.proveedor || 'Sin proveedor'}</strong><br>
                RUT: ${data.rut_proveedor || 'Sin RUT'}
            `;
        })
        .catch(error => {
            console.error('Error al cargar los detalles de la factura:', error);
            detallesContainer.style.display = 'none'; // Ocultar en caso de error
        });
}



function toggleTransferencia() {
    const metodoPagoSelect = document.getElementById('metodo_pago_id');
    const transferenciaGroup = document.getElementById('transferencia-group');

    // Verificar si el texto seleccionado es "Tarjeta"
    if (metodoPagoSelect.options[metodoPagoSelect.selectedIndex].text === 'Tarjeta') {
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
