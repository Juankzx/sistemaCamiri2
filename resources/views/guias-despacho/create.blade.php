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
            <input type="text" class="form-control" id="numero_guia" name="numero_guia" placeholder="Ingresa el N° de Guía" required>
        </div>

        <!-- Fecha de Entrega -->
        <div class="form-group">
            <label for="fecha_entrega">Fecha de Entrega</label>
            <input type="datetime-local" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
        </div>

        <!-- Selección de Orden de Compra -->
        <div class="form-group">
            <label for="orden_compra_id">Orden de Compra (opcional)</label>
            <select class="form-control select2" id="orden_compra_id" name="orden_compra_id">
                <option value="" selected>Ninguna (Crear Guía sin Orden de Compra)</option>
                @foreach($ordenCompra as $orden)
                    <option value="{{ $orden->id }}">
                        N°: {{ $orden->numero_orden }} - Proveedor: {{ $orden->proveedor->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Buscador de productos cuando no hay Orden de Compra -->
        <div id="productSearch" class="form-group" style="display: none;">
            <label for="buscarProducto">Buscar Producto</label>
            <input type="text" class="form-control mb-2" id="buscarProducto" placeholder="Escriba para buscar un producto..." oninput="filtrarProductos()">
            <ul id="listaProductos" class="list-group mb-3" style="max-height: 200px; overflow-y: auto;"></ul>
        </div>

        <!-- Detalles de la Guía de Despacho -->
        <div class="card mt-3">
            <div class="card-header bg-primary text-white">
                <h4>Detalles de la Guía</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="detalles-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th id="cantidadSolicitadaColumn">Cantidad Solicitada</th>
                            <th>Cantidad Entregada</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
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

@section('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CL', {
        style: 'currency',
        currency: 'CLP',
        minimumFractionDigits: 0,
    }).format(value);
};


document.addEventListener('DOMContentLoaded', function () {
    const fechaEntregaInput = document.getElementById('fecha_entrega');

    if (fechaEntregaInput) {
        // Obtén la fecha y hora actual en formato ISO
        const now = new Date();
        const isoString = now.toISOString().slice(0, 16); // yyyy-MM-ddTHH:mm

        // Asigna la fecha y hora al campo
        fechaEntregaInput.value = isoString;
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const ordenCompraSelect = document.getElementById('orden_compra_id');
    const productSearchDiv = document.getElementById('productSearch');
    const productInput = document.getElementById('buscarProducto');
    const listaProductos = document.getElementById('listaProductos');
    const tbody = document.querySelector('#detalles-table tbody');

    // Productos cargados desde el backend
    const productos = [
        @foreach($productos as $producto)
            {
                id: "{{ $producto->id }}",
                nombre: "{{ $producto->nombre }}",
                codigo_barra: "{{ $producto->codigo_barra }}"
            },
        @endforeach
    ];

    // Mostrar buscador si la orden de compra es nula
    toggleProductSearch(ordenCompraSelect.value);

    ordenCompraSelect.addEventListener('change', function () {
        toggleProductSearch(this.value);
        if (this.value !== "") {
            loadOrderDetails(this.value);
        } else {
            tbody.innerHTML = ""; // Limpiar detalles si no hay orden seleccionada
        }
    });

    // Mostrar u ocultar el buscador de productos
    function toggleProductSearch(ordenCompraId) {
        if (ordenCompraId === "") {
            productSearchDiv.style.display = 'block';
        } else {
            productSearchDiv.style.display = 'none';
        }
    }

    // Buscar productos en vivo
    productInput.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        listaProductos.innerHTML = ""; // Limpiar la lista

        const productosFiltrados = productos.filter(producto =>
            producto.nombre.toLowerCase().includes(query) ||
            producto.codigo_barra.toLowerCase().includes(query)
        );

        productosFiltrados.forEach(producto => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action';
            li.textContent = `${producto.nombre} (${producto.codigo_barra})`;
            li.onclick = () => agregarProducto(producto);
            listaProductos.appendChild(li);
        });
    });

    // Cargar detalles de la orden de compra seleccionada
    async function loadOrderDetails(ordenCompraId) {
        tbody.innerHTML = ""; // Limpiar la tabla antes de cargar

        try {
            const response = await fetch('/api/ordenes-compra/' + ordenCompraId);
            const data = await response.json();

            data.detalles.forEach((detalle, index) => {
                const row = generateRow(
                    detalle.producto.nombre,
                    detalle.cantidad,
                    detalle.precio_compra,
                    index,
                    true,
                    detalle.producto.id
                );
                tbody.insertAdjacentHTML('beforeend', row);
            });
            updateTotal();
        } catch (error) {
            console.error("Error al cargar detalles de la orden de compra:", error);
        }
    }

    // Agregar producto desde el buscador a la tabla de detalles
    function agregarProducto(producto) {
        if (!producto || !producto.id) {
            console.error("El producto no tiene un ID válido:", producto);
            return;
        }

        const index = tbody.children.length; // Índice basado en el número de filas actuales
        const row = generateRow(producto.nombre, "", "", index, false, producto.id); // Pasar el ID del producto
        tbody.insertAdjacentHTML('beforeend', row);

        // Limpiar el buscador
        productInput.value = "";
        listaProductos.innerHTML = "";
        updateTotal();
    }

   // Generar fila en la tabla de detalles
function generateRow(nombreProducto = "", cantidad = "", precioCompra = "", index, readonly = false, productoId = null) {
    return `
        <tr class="product-row">
            <td>
                <!-- Campo oculto para producto_id -->
                <input type="hidden" name="detalles[${index}][producto_id]" value="${productoId || ''}" required>
                <input type="text" class="form-control" value="${nombreProducto}" readonly>
            </td>
            ${
                readonly
                    ? `<td><input type="number" class="form-control" name="detalles[${index}][cantidad]" value="${cantidad}" readonly></td>`
                    : `<td><input type="text" class="form-control" name="detalles[${index}][cantidad]" value="N/A" readonly></td>`
            }
            <td>
                <input type="number" class="form-control" name="detalles[${index}][cantidad_entregada]" min="1" placeholder="Cantidad" value="" oninput="validateCantidad(${index}); updateSubtotal(${index})" required>
            </td>
            <td>
                <input type="number" class="form-control" name="detalles[${index}][precio_compra]" min="0.01" step="0.01" placeholder="Precio" value="${precioCompra}" oninput="updateSubtotal(${index})" required>
            </td>
            <td>
                <input type="number" class="form-control subtotal" name="detalles[${index}][subtotal]" value="0" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
    `;
}


    // Validar que la cantidad entregada no exceda la solicitada
    window.validateCantidad = function (index) {
        const cantidadEntregada = document.querySelector(`[name="detalles[${index}][cantidad_entregada]"]`);
        const cantidadSolicitada = document.querySelector(`[name="detalles[${index}][cantidad]"]`);

        if (cantidadSolicitada && parseFloat(cantidadEntregada.value) > parseFloat(cantidadSolicitada.value)) {
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad excedida',
                text: 'La cantidad entregada no puede ser mayor a la solicitada.',
            });
            cantidadEntregada.value = cantidadSolicitada.value; // Restablecer el valor
        }
    };

    // Actualizar subtotal de cada fila
    window.updateSubtotal = function (index) {
        const cantidad = parseFloat(document.querySelector(`[name="detalles[${index}][cantidad_entregada]"]`).value) || 0;
        const precioCompra = parseFloat(document.querySelector(`[name="detalles[${index}][precio_compra]"]`).value) || 0;
        const subtotalField = document.querySelector(`[name="detalles[${index}][subtotal]"]`);

        subtotalField.value = (cantidad * precioCompra).toFixed(0);
        updateTotal();
    };

    // Actualizar el total general
    function updateTotal() {
    const subtotales = document.querySelectorAll('.subtotal');
    let total = 0;

    // Suma los subtotales
    subtotales.forEach(subtotal => {
        const value = parseFloat(subtotal.value || subtotal.textContent.replace(/[^0-9.-]+/g, "")) || 0;
        total += value;
    });

    // Actualiza el total general con formato de moneda
    document.getElementById('total').innerText = formatCurrency(total);
}

    // Eliminar fila de la tabla
    window.removeRow = function (button) {
        button.closest('tr').remove();
        updateTotal();
    };
});
</script>
@endsection
