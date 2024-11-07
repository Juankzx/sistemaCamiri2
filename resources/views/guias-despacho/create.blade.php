@extends('adminlte::page')

@section('title', 'Crear Guía de Despacho')

@section('content')
<div class="container">
    <h1>Crear Guía de Despacho</h1>
    <form action="{{ route('guias-despacho.store') }}" method="POST">
        @csrf
        <!-- Número de Guía -->
        <div class="form-group">
            <label for="numero_guia">Número de Guía</label>
            <input type="text" class="form-control" id="numero_guia" name="numero_guia" required>
        </div>

        <!-- Fecha de Entrega -->
        <div class="form-group">
            <label for="fecha_entrega">Fecha de Entrega</label>
            <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
        </div>

        <!-- Selección de Orden de Compra -->
        <div class="form-group">
            <label for="orden_compra_id">Orden de Compra</label>
            <select class="form-control" id="orden_compra_id" name="orden_compra_id" required>
                <option value="" disabled selected>Seleccione una Orden de Compra</option>
                @foreach($ordenCompra as $orden)
                    <option value="{{ $orden->id }}">
                        N°: {{ $orden->numero_orden }} 
                        - Proveedor: {{ $orden->proveedor->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Estado -->
        <div class="form-group">
            <label for="estado">Estado</label>
            <input type="text" class="form-control" id="estado" name="estado" value="emitida" readonly>
        </div>
        
        <!-- Detalles de la Guía de Despacho -->
        <div class="card mt-3">
            <div class="card-header">
                <h4>Detalles de la Orden de Compra</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="detalles-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad Entregada</th>
                            <th>Precio Compra</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los detalles se cargarán aquí vía JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Total -->
        <div class="card mt-3">
            <div class="card-body">
                <h4>Total: <span id="total">$0</span></h4>
            </div>
        </div>

        <!-- Botón de Guardar -->
        <button type="submit" class="btn btn-primary mt-3">Guardar Guía</button>
    </form>
</div>
@endsection

@section('css')
<style>
    .card {
        margin-top: 20px;
    }
    .card-header {
        background-color: #007bff;
        color: white;
    }
    .form-control {
        border-radius: 5px;
        height: 38px;
    }
</style>
@endsection

@section('js')
<script>
    document.getElementById('orden_compra_id').addEventListener('change', function() {
        var ordenCompraId = this.value;
        
        // Hacer la solicitud AJAX para obtener los detalles de la orden de compra
        fetch('/api/ordenes-compra/' + ordenCompraId)
            .then(response => response.json())
            .then(data => {
                var tbody = document.querySelector('#detalles-table tbody');
                tbody.innerHTML = '';  // Limpiar tabla

                // Iterar sobre los detalles de la orden de compra
                data.detalles.forEach((detalle, index) => {
                    var precioCompra = parseFloat(detalle.precio_compra) || 0;  // Asegurar que sea un número
                    var subtotal = detalle.cantidad * precioCompra;

                    var row = `
                        <tr>
                            <td>
                                ${detalle.producto.nombre}
                                <input type="hidden" name="detalles[${index}][producto_id]" value="${detalle.producto_id}">
                            </td>
                            <td>
                                <input type="number" class="form-control" name="detalles[${index}][cantidad_entregada]" value="${detalle.cantidad}" oninput="updateSubtotal(${index})" required>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="detalles[${index}][precio_compra]" value="${precioCompra}" oninput="updateSubtotal(${index})" required>
                            </td>
                            <td>
                                <input type="number" class="form-control subtotal" name="detalles[${index}][subtotal]" value="${subtotal}" readonly>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });

                updateTotal();  // Actualizar el total después de cargar los productos
            });
    });

    // Función para actualizar los subtotales
    function updateSubtotal(index) {
        const cantidad = parseFloat(document.querySelector(`[name="detalles[${index}][cantidad_entregada]"]`).value) || 0;
        const precioCompra = parseFloat(document.querySelector(`[name="detalles[${index}][precio_compra]"]`).value) || 0;
        const subtotalField = document.querySelector(`[name="detalles[${index}][subtotal]"]`);

        const subtotal = cantidad * precioCompra;
        subtotalField.value = subtotal.toFixed(0); // Actualizar el subtotal en la tabla

        updateTotal();  // Actualizar el total después de cada cambio
    }

    // Función para actualizar el total general
    function updateTotal() {
        const subtotales = document.querySelectorAll('.subtotal');
        let total = 0;
        subtotales.forEach(subtotal => {
            total += parseFloat(subtotal.value) || 0;
        });
        document.getElementById('total').innerText = `$${total.toFixed(0)}`;
    }
</script>
@endsection
