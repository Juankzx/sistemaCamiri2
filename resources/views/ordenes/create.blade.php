@extends('adminlte::page')

@section('title', 'Crear Orden de Compra')

@section('content_header')
    <h1>Crear Orden de Compra</h1>
@stop

@section('content')
<div class="container">
    <form action="{{ route('ordenes-compras.store') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-header">
                <h3>Datos de la Orden de Compra</h3>
            </div>
            <div class="card-body">
                @include('ordenes.partials.form')
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">
                <h4>Detalles de la Orden de Compra</h4>
            </div>
            <div class="card-body">
                <div id="detallesOrdenCompra">
                    <!-- Contenedor para los detalles de la orden -->
                </div>
                <button type="button" class="btn btn-secondary mt-3" onclick="addDetail()">Agregar Producto</button>
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="total_factura">Total</label>
            <input type="number" class="form-control" id="total" name="total" required readonly>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Guardar Todo</button>
    </form>
</div>

<script>
let total = 0;

function addDetail() {
    const container = document.getElementById('detallesOrdenCompra');
    const index = container.children.length;  // Asegúrate de que el índice es único incluso después de eliminar detalles
    const html = `
        <div class="detail-group form-group" style="border-top: 1px solid #ccc; padding-top: 10px; margin-top: 10px;">
            <div class="row">
                <div class="col-md-6">
                    <label for="producto_id-${index}">Producto</label>
                    <select class="form-control" id="producto_id-${index}" name="detalles[${index}][producto_id]" onchange="checkProductoEnBodegaGeneral(${index})">
                        <option value="">Seleccione un producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="cantidad-${index}">Cantidad</label>
                    <input type="number" class="form-control" id="cantidad-${index}" name="detalles[${index}][cantidad]" required placeholder="Cantidad" oninput="calculateTotal()">
                </div>
                <div class="col-md-2">
                    <label for="precio_compra-${index}">Precio de Compra</label>
                    <input type="text" class="form-control" id="precio_compra-${index}" name="detalles[${index}][precio_compra]" required placeholder="Precio de Compra" oninput="calculateTotal()">
                </div>
                <div class="col-md-2">
                    <label for="info-${index}">Info</label>
                    <div id="info-${index}" class="form-control-plaintext"></div>
                </div>
            </div>
            <button type="button" class="btn btn-danger mt-2" onclick="removeDetail(this)">Eliminar</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function checkProductoEnBodegaGeneral(index) {
    const productoId = document.getElementById(`producto_id-${index}`).value;
    fetch(`/api/check-producto-bodega-general/${productoId}`)
        .then(response => response.json())
        .then(data => {
            const infoElement = document.getElementById(`info-${index}`);
            if (data.exists) {
                infoElement.innerHTML = `En Bodega General: ${data.cantidad}`;
                infoElement.style.color = 'green';
            } else {
                infoElement.innerHTML = 'No disponible en Bodega General';
                infoElement.style.color = 'red';
            }
        });
}

function calculateTotal() {
    total = 0;
    document.querySelectorAll('.detail-group').forEach(group => {
        const cantidad = group.querySelector('[name$="[cantidad]"]').value;
        const precio = group.querySelector('[name$="[precio_compra]"]').value;
        total += cantidad * precio;
    });
    document.getElementById('total').value = total.toFixed(0);
}

function removeDetail(element) {
    element.closest('.detail-group').remove();
    calculateTotal();  // Recalcular el total después de eliminar un detalle
}
</script>
@stop
