@extends('adminlte::page')

@section('title', 'Crear Orden de Compra')

@section('content_header')
    <h1>Crear Orden de Compra</h1>
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

    <form action="{{ route('ordenes-compras.store') }}" method="POST">
        @csrf

        <!-- Encabezado de la Orden de Compra -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Datos de la Orden de Compra</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero_orden">Número de Orden</label>
                            <input type="text" class="form-control" id="numero_orden" name="numero_orden" value="{{ $nuevoNumeroOrden }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="proveedor_id">Proveedor</label>
                            <select class="form-control" id="proveedor_id" name="proveedor_id">
                                <option value="">Seleccione un proveedor</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <input type="text" class="form-control" id="estado" name="estado" value="solicitado" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles de la Orden de Compra con Formato de Factura -->
        <div class="card">
            <div class="card-header">
                <h4>Detalles de la Orden de Compra</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="detallesOrdenCompra">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Producto</th>
                                <th style="width: 10%;">Cantidad</th>
                                <th style="width: 15%;">Precio Unitario</th>
                                <th style="width: 15%;">Descuento</th>
                                <th style="width: 15%;">SubTotal</th>
                                <th style="width: 10%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí se insertarán dinámicamente los detalles -->
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-secondary mt-3" onclick="addDetail()">Agregar Producto</button>
            </div>
        </div>

        <!-- Sección de Totales -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="total_factura">Total</label>
                    <input type="number" class="form-control" id="total" name="total" required readonly>
                </div>
            </div>
        </div>

        <!-- Botón de Guardado -->
        <div class="text-right">
            <button type="submit" class="btn btn-primary mt-3">Guardar Todo</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
let total = 0;
let selectedProducts = new Set(); // Conjunto para almacenar IDs de productos seleccionados

function addDetail() {
    const container = document.querySelector('#detallesOrdenCompra tbody');
    const index = container.children.length;

    if (index > 0) {
        const lastProductSelect = document.querySelector(`#producto_id-${index - 1}`);
        if (lastProductSelect && lastProductSelect.value === "") {
            alert('Debe seleccionar un producto antes de agregar otro.');
            return;
        }
    }

    const html = `
        <tr class="detail-group">
            <td>
                <select class="form-control" id="producto_id-${index}" name="detalles[${index}][producto_id]" onchange="checkProductoEnBodegaGeneral(${index}); validateProductSelection(${index})">
                    <option value="">Seleccione un producto</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                    @endforeach
                </select>
                <span id="info-${index}" style="font-weight:bold;color:grey;"></span>
            </td>
            <td>
                <input type="number" class="form-control" id="cantidad-${index}" name="detalles[${index}][cantidad]" required placeholder="Cantidad" oninput="calculateTotal()">
            </td>
            <td>
                <input type="number" class="form-control" id="precio_compra-${index}" name="detalles[${index}][precio_compra]" required placeholder="Precio de Compra" oninput="calculateTotal()">
            </td>
            <td>
                <input type="number" class="form-control" id="descuento-${index}" name="detalles[${index}][descuento]" value="0" placeholder="Descuento" oninput="calculateTotal()">
            </td>
            <td id="total-${index}">$0</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeDetail(this)">Eliminar</button>
            </td>
        </tr>
    `;
    container.insertAdjacentHTML('beforeend', html);
}


function validateProductSelection(index) {
    const selectedProduct = document.getElementById(`producto_id-${index}`).value;

    if (selectedProducts.has(selectedProduct)) {
        alert('El producto ya ha sido agregado. Por favor, seleccione otro producto.');
        document.getElementById(`producto_id-${index}`).value = ''; // Limpiar la selección para evitar duplicados
    } else {
        selectedProducts.add(selectedProduct); // Agregar el nuevo producto al conjunto
    }
}

function calculateTotal() {
    let total = 0;

    document.querySelectorAll('.detail-group').forEach((group, index) => {
        const cantidad = parseFloat(group.querySelector(`[name="detalles[${index}][cantidad]"]`).value) || 0;
        const precio = parseFloat(group.querySelector(`[name="detalles[${index}][precio_compra]"]`).value) || 0;
        const descuento = parseFloat(group.querySelector(`[name="detalles[${index}][descuento]"]`).value) || 0;

        // Calcular el subtotal considerando el descuento
        const subtotal = (cantidad * (precio - descuento)).toFixed(0);

        // Actualizar el subtotal en la columna correspondiente
        group.querySelector(`#total-${index}`).textContent = `$${subtotal}`;

        // Sumar al total general
        total += parseFloat(subtotal);
    });

    // Actualizar el campo de total general
    document.getElementById('total').value = total.toFixed(0);
}

function removeDetail(element) {
    const detailRow = element.closest('.detail-group');

    // Obtener el ID del producto eliminado y eliminarlo del conjunto de productos seleccionados
    const productSelect = detailRow.querySelector('select');
    if (productSelect && selectedProducts.has(productSelect.value)) {
        selectedProducts.delete(productSelect.value);
    }

    // Eliminar la fila correspondiente
    detailRow.remove();

    // Recalcular el total después de eliminar
    calculateTotal();

    // Reindexar los elementos restantes
    reindexDetails();
}

// Función para actualizar los índices de los elementos dinámicos después de eliminar un detalle
function reindexDetails() {
    const details = document.querySelectorAll('.detail-group');
    details.forEach((group, index) => {
        group.querySelectorAll('input, select').forEach((input) => {
            if (input.name) {
                const updatedName = input.name.replace(/\[\d+\]/, `[${index}]`);
                input.setAttribute('name', updatedName);
            }
            if (input.id) {
                const updatedId = input.id.replace(/-\d+/, `-${index}`);
                input.setAttribute('id', updatedId);
            }
        });

        // Actualizar el ID del campo de subtotal
        const subtotalElement = group.querySelector(`[id^="total-"]`);
        if (subtotalElement) {
            subtotalElement.setAttribute('id', `total-${index}`);
        }
    });
}

function checkProductoEnBodegaGeneral(index) {
    const productoId = document.getElementById(`producto_id-${index}`).value;

    fetch(`/api/check-producto-bodega-general/${productoId}`)
        .then(response => response.json())
        .then(data => {
            const infoElement = document.getElementById(`info-${index}`);
            if (infoElement) {
    if (data.exists) {
        infoElement.innerHTML = `En Bodega General: ${data.cantidad}`;
        infoElement.style.color = 'green';
    } else {
        infoElement.innerHTML = 'No disponible en Bodega General';
        infoElement.style.color = 'red';
    }
} else {
    console.error(`El elemento con id info-${index} no existe en el DOM.`);
}

        });
}
</script>
@stop
