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
                <h3>Datos de la Guía de Despacho</h3>
            </div>
            <div class="card-body">
                @include('guias-despacho.partials.form')
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3>Datos de la Factura</h3>
            </div>
            <div class="card-body">
                @include('facturas.partials.form')
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4>Detalles de la Orden de Compra</h4>
            </div>
            <div class="card-body" id="detallesOrdenCompra">
                <button type="button" class="btn btn-secondary" onclick="addDetail()">Agregar Producto</button>
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="total_factura">Total de Factura</label>
            <input type="number" class="form-control" id="total_factura" name="total_factura" required readonly>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Guardar Todo</button>
    </form>
</div>

<script>
let totalFactura = 0;

function addDetail() {
    const container = document.getElementById('detallesOrdenCompra');
    const index = container.children.length;  // Asegúrate de que el índice es único incluso después de eliminar detalles
    const html = `
        <div class="detail-group form-group" style="border-top: 1px solid #ccc; padding-top: 10px; margin-top: 10px;">
            <div class="row">
                <div class="col-md-4">
                    <label for="producto_id-${index}">Producto</label>
                    <select class="form-control" id="producto_id-${index}" name="detalles[${index}][producto_id]" onchange="updateInventarioOptions(${index})">
                        <option value="">Seleccione un producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="inventario_id-${index}">Inventario</label>
                    <select class="form-control" id="inventario_id-${index}" name="detalles[${index}][inventario_id]">
                        <option value="">Seleccione un inventario</option>
                        @foreach($inventarios as $inventario)
                            <option value="{{ $inventario->id }}">{{ $inventario->producto->nombre }} - {{ $inventario->sucursal->nombre }} - {{ $inventario->cantidad }} en stock - {{ $inventario->bodega->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="cantidad-${index}">Cantidad</label>
                    <input type="number" class="form-control" id="cantidad-${index}" name="detalles[${index}][cantidad]" required placeholder="Cantidad" oninput="calculateTotal()">
                </div>
                <div class="col-md-4">
                    <label for="precio_compra-${index}">Precio de Compra</label>
                    <input type="text" class="form-control" id="precio_compra-${index}" name="detalles[${index}][precio_compra]" required placeholder="Precio de Compra" oninput="calculateTotal()">
                </div>
            </div>
            <button type="button" class="btn btn-danger" style="margin-top: 5px;" onclick="removeDetail(this)">Eliminar</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function updateInventarioOptions(index) {
    const productoId = document.getElementById(`producto_id-${index}`).value;
    const inventarioSelect = document.getElementById(`inventario_id-${index}`);
    fetch(`/api/inventarios/${productoId}`) // Asumiendo que tienes una ruta API que devuelve los inventarios por producto
        .then(response => response.json())
        .then(data => {
            inventarioSelect.innerHTML = data.map(inv => `<option value="${inv.id}">${inv.sucursal.nombre} - ${inv.cantidad} en stock</option>`).join('');
        });
}

function calculateTotal() {
    totalFactura = 0;
    document.querySelectorAll('.detail-group').forEach(group => {
        const cantidad = group.querySelector('[name$="[cantidad]"]').value;
        const precio = group.querySelector('[name$="[precio_compra]"]').value;
        totalFactura += cantidad * precio;
    });
    document.getElementById('total_factura').value = totalFactura.toFixed(0);
}

function removeDetail(element) {
    element.closest('.detail-group').remove();
    calculateTotal();  // Recalcular el total después de eliminar un detalle
}
</script>
@stop
