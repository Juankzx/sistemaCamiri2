@extends('adminlte::page')

@section('content')
<div class="container">
    <h1>Crear Guía de Despacho</h1>
    <form action="{{ route('guias-despacho.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="numero_guia">Número de Guía</label>
            <input type="text" class="form-control" id="numero_guia" name="numero_guia" required>
        </div>
        <div class="form-group">
            <label for="fecha_entrega">Fecha de Entrega</label>
            <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
        </div>
        <div class="form-group">
            <label for="orden_compra_id">Orden de Compra</label>
            <select class="form-control" id="orden_compra_id" name="orden_compra_id" required>
                <option value="" disabled selected>Seleccione una Orden de Compra</option>
                @foreach($ordenCompra as $ordenCompras)
                    <option value="{{ $ordenCompras->id }}">
                        N°: {{ $ordenCompras->numero_orden }} 
                        - Estado: {{ $ordenCompras->estado }} 
                        - Proveedor: {{ $ordenCompras->proveedor->nombre }} 
                        - Fecha: {{ $ordenCompras->created_at->format('d/m/Y H:i:s') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="estado">Estado</label>
            <input type="text" class="form-control" id="estado" name="estado" value="emitida" readonly>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h4>Detalles de la Orden de Compra</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="detalles-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Compra</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Detalles se cargarán aquí vía AJAX -->
                    </tbody>
                </table>
                <button type="button" class="btn btn-secondary mt-3" onclick="addDetail()">Agregar Producto</button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Guardar Guía</button>
    </form>
</div>

@section('css')
<style>
    .card {
        margin-top: 20px;
    }
    .card-header {
        background-color: #007bff;
        color: white;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }
    .badge-info {
        background-color: #17a2b8;
    }
</style>
@endsection

@section('js')
<script>
    document.getElementById('orden_compra_id').addEventListener('change', function() {
    var ordenCompraId = this.value;
    fetch('/api/ordenes-compra/' + ordenCompraId)
        .then(response => response.json())
        .then(data => {
            var tbody = document.querySelector('#detalles-table tbody');
            tbody.innerHTML = '';
            data.detalles.forEach((detalle, index) => {
                var row = `<tr>
                    <td>
                        <input type="hidden" name="detalles[${index}][producto_id]" value="${detalle.producto.id}">
                        ${detalle.producto.nombre}
                    </td>
                    <td>
                        <input type="number" class="form-control" name="detalles[${index}][cantidad]" value="${detalle.cantidad}" readonly>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control" name="detalles[${index}][precio_compra]" value="${detalle.precio_compra}" readonly>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control" name="detalles[${index}][subtotal]" value="${(detalle.cantidad * detalle.precio_compra).toFixed(2)}" readonly>
                    </td>
                </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        });
});


    function addDetail() {
        var tbody = document.querySelector('#detalles-table tbody');
        var index = tbody.children.length;
        var row = `<tr>
            <td>
                <select class="form-control" name="detalles[${index}][producto_id]" required>
                    <option value="" disabled selected>Seleccione un producto</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" class="form-control" name="detalles[${index}][cantidad]" required>
            </td>
            <td>
                <input type="number" step="0.01" class="form-control" name="detalles[${index}][precio_compra]" required>
            </td>
            <td>
                <input type="number" step="0.01" class="form-control" name="detalles[${index}][subtotal]" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger" onclick="removeDetail(this)">Eliminar</button>
            </td>
        </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    }

    function removeDetail(element) {
        element.closest('tr').remove();
    }
</script>
@endsection

@endsection
