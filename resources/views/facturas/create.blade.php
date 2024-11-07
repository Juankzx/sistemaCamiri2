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
                            <select class="form-control" id="guia_despacho_id" name="guia_despacho_id" required>
                                <option value="" selected disabled>Seleccione la Guía de Despacho</option>
                                @foreach($guiasDespacho as $guia)
                                    <option value="{{ $guia->id }}">
                                        N°: {{ $guia->numero_guia }} - Proveedor: {{ $guia->ordenCompra->proveedor->nombre }}
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
                            <input type="number" class="form-control" id="total_factura" name="monto_total" readonly>
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
                            <th>Cantidad</th>
                            <th>Precio Compra</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los detalles de los productos se cargarán aquí mediante AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botón de Guardado -->
        <button type="submit" class="btn btn-primary mt-3">Guardar Factura</button>
    </form>
</div>
@endsection

@section('js')
<script>
document.getElementById('guia_despacho_id').addEventListener('change', function() {
    const guiaDespachoId = this.value;

    if (!guiaDespachoId) {
        return;
    }

    // Realizar la petición AJAX para obtener los detalles de la Guía de Despacho seleccionada
    fetch(`/api/facturas/${guiaDespachoId}/detalles`)
        .then(response => response.json())
        .then(data => {
            console.log(data); // Agregar este para verificar la estructura completa de la respuesta

            if (data && data.detalles) {
                const tbody = document.querySelector('#detalles_factura tbody');
                tbody.innerHTML = ''; // Limpiar tabla de detalles

                let totalFactura = 0;

                // Iterar sobre los detalles de la guía y mostrarlos en la tabla
                data.detalles.forEach(detalle => {
                    const precioCompra = parseFloat(detalle.precio_compra) || 0;
                    const cantidad = parseFloat(detalle.cantidad_entregada) || 0;
                    const subtotal = cantidad * precioCompra;

                    const row = `
                        <tr>
                            <td>${detalle.producto.nombre}</td>
                            <td><input type="number" class="form-control" value="${cantidad}" readonly></td>
                            <td><input type="number" class="form-control" value="${precioCompra}" readonly></td>
                            <td><input type="number" class="form-control" value="${subtotal}" readonly></td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                    totalFactura += subtotal; // Calcular el total de la factura
                });

                // Mostrar el total de la factura en el campo correspondiente
                document.getElementById('total_factura').value = totalFactura;

                // Mostrar el proveedor
                if (data.proveedor) {
                    document.getElementById('proveedor').value = `${data.proveedor.nombre} - ${data.proveedor.rut}`;
                } else {
                    document.getElementById('proveedor').value = 'Proveedor no disponible';
                }
            } else {
                console.error("Detalles de la factura no encontrados o mal estructurados");
            }
        })
        .catch(error => {
            console.error('Error al cargar los detalles de la factura:', error);
        });
});
</script>


@endsection
