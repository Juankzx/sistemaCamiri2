@extends($isVendedor ? 'layouts.app' : 'adminlte::page')

@section('title', 'Crear Venta')

@section('content_header')
    <h1 class="text-center">Registrar Nueva Venta</h1>
@stop

@section('content')
<div class="container">
    <!-- Mostrar mensaje si no hay caja abierta -->
    @if(isset($mensaje) && !empty($mensaje))
        <div class="alert alert-warning text-center">
            <strong>{{ $mensaje }}</strong>
        </div>
    @endif
    <div class="row">
        <!-- Información básica de la venta -->
        <div class="col-md-8 mb-3">
            <form action="{{ route('ventas.store') }}" method="POST" id="ventaForm">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Detalles de la Venta</h5>
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        
                        <!-- Mostrar la sucursal activa de la caja abierta -->
                        <div class="form-group mb-3">
                            <label for="sucursal_id" class="form-label">Sucursal Activa</label>
                            <input type="text" class="form-control" value="{{ $sucursalActiva->nombre }}" readonly>
                            <input type="hidden" name="sucursal_id" value="{{ $sucursalActiva->id }}">
                        </div>

                        <div class="form-group mb-3">
                            <label for="metodo_pago_id" class="form-label">Método de Pago</label>
                            <select class="form-control" id="metodo_pago_id" name="metodo_pago_id" required>
                                <option value="" disabled selected>Seleccione un método de pago</option>
                                @foreach ($metodosPago as $metodo)
                                    <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" class="form-control" id="fecha" name="fecha" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                </div>
                <!-- Búsqueda y selección de productos -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="mb-3">Buscar y Agregar Productos</h5>
                        <input type="text" id="productSearch" class="form-control mb-3" placeholder="Buscar producto por código o nombre...">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="productTable">
                                    <!-- Productos cargados aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Carrito de compras y detalles -->
        <div class="col-md-4">
            <!-- Carrito de compras -->
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Carrito de Compras</h5>
                    <div id="cart"></div>
                </div>
            </div>

            <!-- Detalles del total de la venta -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="mb-3">Resumen del Pedido</h5>
                    <div class="form-group mb-2">
                        <label>Total Bruto:</label>
                        <input type="text" id="totalNeto" class="form-control bg-light" readonly>
                    </div>
                    <div class="form-group mb-2">
                        <label>IVA (19%):</label>
                        <input type="text" id="iva" class="form-control bg-light" readonly>
                    </div>
                    <div class="form-group mb-4">
                        <label>Total Neto:</label>
                        <input type="text" id="totalConIva" class="form-control bg-light" readonly>
                    </div>
                    <button type="button" class="btn btn-primary w-100" onclick="submitForm()"><i class="fas fa-shopping-cart"></i> Finalizar Venta</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales -->
@include('modals.confirmacion')

@endsection

@section('css')
<style>
    /* Ajuste general del contenedor */
    .container {
        margin-top: 30px;
    }

    /* Titulares */
    .h1, h5 {
        color: #343a40; /* Color de títulos */
    }

    .card {
        border: none; /* Sin bordes */
        border-radius: 12px; /* Bordes suaves */
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Sombra ligera */
    }

    /* Card Headers */
    .card-body {
        padding: 1rem; /* Ajuste de padding */
    }

    /* Modificar el estilo del botón de cierre */
    .btn-close {
        background: none;
        border: none;
        font-size: 1.2rem;
    }

    .form-control, .form-label {
        border-radius: 5px; /* Inputs más suaves */
    }

    /* Encabezado de la tabla */
    .table th {
        text-align: center;
    }

    /* Estilo de la tabla */
    .table td {
        text-align: center;
    }

    /* Botones */
    .btn {
        border-radius: 50px; /* Redondeado */
    }
    
    .modal-content {
        border-radius: 12px;
    }
</style>
@endsection


@section('js')
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Incluir en el head o la sección de js del archivo create -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Cargar productos desde el backend
    let products = @json($productos);
    let cart = [];

    // Función para mostrar productos
    function displayProducts(products) {
        const table = document.getElementById('productTable');
        table.innerHTML = '';
        products.forEach(product => {
            const productRow = `
                <tr>
                    <td>${product.nombre}</td>
                    <td>$${product.precioventa.toFixed(0)}</td>
                    <td>${product.inventarios.length > 0 ? product.inventarios[0].cantidad : 'N/A'}</td>
                    <td>
                        <button type="button" 
                                onclick="addProductToCart(${product.id}, '${product.nombre}', ${product.precioventa}, ${product.inventarios.length > 0 ? product.inventarios[0].cantidad : 0}, ${product.inventarios.length > 0 ? product.inventarios[0].id : 0}, '${product.unidad_medida}')" 
                                class="btn btn-primary">Agregar</button>
                    </td>
                </tr>
            `;
            table.innerHTML += productRow;
        });
    }

    // Ejecutar displayProducts() automáticamente al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Productos cargados:", products); // Depuración
        if (typeof products !== 'undefined' && products.length > 0) {
            displayProducts(products); // Llama a displayProducts al cargar la página
        } else {
            console.error("No se encontraron productos.");
        }
    });


function addProductToCart(productId, productName, productPrice, productQuantity, inventoryId, unitType) {
    const existingItem = cart.find(item => item.id === productId);
    const initialQuantity = unitType === 'kg' ? 0.1 : 1; // Iniciar con 0.1 si es kg
    if (existingItem) {
        existingItem.quantity += initialQuantity; // Incrementar según la unidad
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: productPrice,
            quantity: initialQuantity, // Cantidad inicial
            inventory: productQuantity,
            inventory_id: inventoryId,
            unit: unitType // Almacena la unidad de medida
        });
    }
    updateCartDisplay();
}


function updateCartDisplay() {
    const cartElement = document.getElementById('cart');
    cartElement.innerHTML = '';
    cart.forEach((item, index) => {
        const quantityValue = item.unit === 'kg' ? item.quantity.toFixed(2) : item.quantity; // Mostrar decimales si es kg
        const itemElement = `
            <div class="cart-item d-flex align-items-center justify-content-between mb-2">
                <input type="hidden" name="detalles[${index}][producto_id]" value="${item.id}">
                <input type="hidden" name="detalles[${index}][precio_unitario]" value="${item.price}">
                <input type="hidden" name="detalles[${index}][cantidad]" value="${item.quantity}">
                <input type="hidden" name="detalles[${index}][inventario_id]" value="${item.inventory_id}">
                <div>${item.name}</div>
                <div class="input-group input-group-sm quantity-adjuster">
                    <div class="input-group-prepend">
                        <button class="btn btn-decrement btn-outline-secondary" type="button" onclick="adjustQuantity(${item.id}, ${item.unit === 'kg' ? -0.1 : -1})">-</button>
                    </div>
                    <input type="text" class="form-control text-center" value="${quantityValue}" min="${item.unit === 'kg' ? 0.1 : 1}" onchange="inputQuantity(${item.id}, this.value)">
                    <div class="input-group-append">
                        <button class="btn btn-increment btn-outline-secondary" type="button" onclick="adjustQuantity(${item.id}, ${item.unit === 'kg' ? 0.1 : 1})">+</button>
                    </div>
                </div>
                <div>$${(item.quantity * item.price).toFixed(0)}</div>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">x</button>
            </div>
        `;
        cartElement.innerHTML += itemElement;
    });
    updateTotals();
}


function adjustQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (!item) return;

    const nuevaCantidad = item.unit === 'kg' ? Math.max(0.1, item.quantity + change) : Math.max(1, item.quantity + change);

    if (nuevaCantidad > item.inventory) {
        Swal.fire({
            icon: 'warning',
            title: 'Inventario Insuficiente',
            text: `No hay suficiente inventario para el producto ${item.name}. Disponible: ${item.inventory}, Requerido: ${nuevaCantidad}`,
            confirmButtonText: 'Aceptar'
        });
    } else {
        item.quantity = nuevaCantidad;
        updateCartDisplay();
    }
}
// Alerta para mostrar si se inicia caja con éxito
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Caja Iniciada',
            text: '{{ session('success') }}',
            confirmButtonText: 'Aceptar'
        });
    });
    @endif

function inputQuantity(productId, value) {
    const item = cart.find(item => item.id === productId);
    if (!item) return;

    let parsedValue = item.unit === 'kg' ? parseFloat(value) : parseInt(value);
    parsedValue = isNaN(parsedValue) ? (item.unit === 'kg' ? 0.1 : 1) : parsedValue; // Si no es válido, asignar valor mínimo

    item.quantity = Math.max(item.unit === 'kg' ? 0.1 : 1, parsedValue);
    updateCartDisplay();
}


function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCartDisplay();
}

function updateTotals() {
    const totalConIva = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    const totalNeto = totalConIva / 1.19; // Calculando el total neto
    const iva = totalConIva - totalNeto; // Calculando el IVA

    document.getElementById('totalNeto').value = `$${totalNeto.toFixed(0)}`;
    document.getElementById('iva').value = `$${iva.toFixed(0)}`;
    document.getElementById('totalConIva').value = `$${totalConIva.toFixed(0)}`;
}

document.getElementById('productSearch').addEventListener('input', function(e) {
    const searchText = e.target.value.toLowerCase();
    const filteredProducts = products.filter(p => p.nombre.toLowerCase().includes(searchText) || p.codigo_barra.includes(searchText));
    displayProducts(filteredProducts);
});

document.getElementById('sucursal_id').addEventListener('change', function() {
    const sucursalId = this.value;
    fetch(`/productos/sucursal/${sucursalId}`)
        .then(response => response.json())
        .then(data => {
            products = data;
            displayProducts(products);
        });
});

document.getElementById('productSearch').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
    }
});

function submitForm() {
    console.log("Función submitForm ejecutada"); // Agrega este log para depuración
    const form = document.getElementById('ventaForm');

    // Obtener y validar los campos antes de enviar la solicitud
    const metodoPago = form.querySelector('select[name="metodo_pago_id"]').value;
    const sucursalId = form.querySelector('input[name="sucursal_id"]').value;

    // Verificar si se ha seleccionado el método de pago
    if (!metodoPago) {
        Swal.fire({
            icon: 'warning',
            title: 'Método de Pago Requerido',
            text: 'Por favor, seleccione un método de pago antes de continuar.',
            confirmButtonText: 'Aceptar'
        });
        return; // Salir de la función si no se selecciona un método de pago
    }

    // Verificar si el carrito tiene productos agregados
    if (cart.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Carrito Vacío',
            text: 'Por favor, agregue al menos un producto al carrito antes de finalizar la venta.',
            confirmButtonText: 'Aceptar'
        });
        return; // Salir de la función si no hay productos en el carrito
    }

    // Si todo está correcto, procedemos a construir el objeto de datos de la venta
    const ventaData = {
        user_id: form.querySelector('input[name="user_id"]').value,
        sucursal_id: sucursalId, // Asignar correctamente el valor de sucursal
        metodo_pago_id: metodoPago,
        fecha: form.querySelector('input[name="fecha"]').value,
        detalles: cart.map((item, index) => ({
            producto_id: item.id,
            precio_unitario: item.price,
            cantidad: item.quantity,
            inventario_id: item.inventory_id,
        }))
    };

    console.log("Datos enviados:", ventaData); // Muestra los datos que se enviarán para depuración
    
    // Realizar la solicitud de envío con Axios
    axios.post(form.action, ventaData, {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log("Respuesta recibida:", response); // Verificar la respuesta del backend
        if (response.data.success) {
            const ventaId = response.data.venta_id;

            // Mostrar el modal de confirmación si la venta se realizó correctamente
            const modalElement = document.getElementById('printModal');
            if (modalElement) {
                const printModal = new bootstrap.Modal(modalElement);
                printModal.show();

                document.getElementById('yesPrintButton').onclick = function() {
                    window.open(`/ventas/${ventaId}/print`, '_blank');
                    printModal.hide();
                    window.location.href = '/ventas';
                };

                document.getElementById('noPrintButton').onclick = function() {
                    window.location.href = '/ventas';
                };
            }
        } else {
            // Mostrar mensaje de error si la respuesta no indica éxito
            Swal.fire({
                icon: 'error',
                title: 'Error en la Venta',
                text: response.data.error || 'Hubo un problema al procesar la venta.',
                confirmButtonText: 'Aceptar'
            });
        }
    })
    .catch(error => {
        console.error('Error en el proceso de venta:', error); // Mostrar el error en la consola para depuración
        // Mostrar mensaje de error con SweetAlert
        Swal.fire({
            icon: 'error',
            title: 'Error en la Venta',
            text: 'Ocurrió un error inesperado. Por favor, intente de nuevo.',
            confirmButtonText: 'Aceptar'
        });
    });
}


</script>

@stop

@section('css')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* Ajustar el diseño del contenedor principal */
    .container {
        margin-top: 20px;
    }

    /* Títulos principales */
    h1, h4 {
        color: #333; /* Cambiar color de los títulos */
        font-weight: 600; /* Hacer los títulos más destacados */
    }

    /* Estilo de las tarjetas (cards) */
    .card {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Agregar sombra para destacar */
        border: none; /* Eliminar borde */
        border-radius: 10px; /* Bordes redondeados */
    }

    /* Encabezado de las tarjetas */
    .card-header {
        font-size: 18px; /* Aumentar tamaño del texto */
        font-weight: bold;
        border-bottom: 2px solid #ddd; /* Añadir línea inferior */
    }

    /* Ajustar colores y diseño del encabezado */
    .card-header.bg-primary {
        background-color: #007bff !important;
        color: white;
        border-radius: 10px 10px 0 0; /* Bordes redondeados en la parte superior */
    }

    .card-header.bg-secondary {
        background-color: #6c757d !important;
        color: white;
    }

    .card-header.bg-info {
        background-color: #17a2b8 !important;
        color: white;
    }

    .card-header.bg-success {
        background-color: #28a745 !important;
        color: white;
    }

    /* Botones de acción */
    .btn-primary, .btn-secondary {
        border-radius: 20px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    /* Formularios */
    .form-control {
        border-radius: 5px; /* Bordes redondeados para los inputs */
    }

    /* Estilos específicos para el modal */
    .modal-content {
        border-radius: 10px; /* Bordes redondeados */
        border: none; /* Eliminar bordes */
        box-shadow: 0 5px 15px rgba(0,0,0,0.3); /* Sombra alrededor del modal */
    }

    /* Footer del modal */
    .modal-footer .btn {
        border-radius: 20px; /* Botones redondeados */
    }

    /* Espaciado y diseño del carrito de compras */
    #cart .cart-item {
        border-bottom: 1px solid #ddd;
        padding: 10px 0;
    }

    #cart .cart-item:last-child {
        border-bottom: none; /* Eliminar borde inferior del último elemento */
    }

    /* Tablas de productos */
    .table th, .table td {
        vertical-align: middle; /* Centrar el texto verticalmente */
    }

    .card-body {
        padding: 0.5rem;
    }
    .card-title {
        font-size: 1rem;
    }
    .card-text {
        font-size: 0.9rem;
    }
    .quantity-adjuster {
        width: 120px;
    }
    .cart-item {
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
    }
    /* Estilos adicionales para el modal */
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5); /* Fondo del modal */
    }

    .btn-close {
        color: #000;
    }

    /* Ajustes para que no interfiera con el resto del diseño */
    #printModal .modal-content {
        border-radius: 8px;
    }

    #printModal .modal-footer .btn {
        margin-left: 10px;
    }
    .container {
        display: grid;
        grid-template-columns: 1fr 1fr; /* Dos columnas */
        gap: 20px; /* Espacio entre columnas */
    }

    .col-md-8, .col-md-4 {
        margin-top: 20px; /* Espaciado superior */
    }

    .col-md-8 {
        grid-column: span 2; /* Ocupa las dos columnas */
    }


</style>
@endsection
