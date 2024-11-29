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
                        <input type="number" class="form-control" id="monto" name="monto">
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
    
    
    // Establecer fecha y hora actual al iniciar
    const fechaPagoInput = document.getElementById('fecha_pago');
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    fechaPagoInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;

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
