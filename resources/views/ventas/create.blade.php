@extends($isVendedor ? 'layouts.app' : 'adminlte::page')

@section('title', 'Crear Venta')

@section('content_header')
    <h1 class="text-center">Registrar Nueva Venta</h1>
@stop

@section('content')
<div class="container">
    @if(isset($mensaje) && !empty($mensaje))
        <div class="alert alert-warning text-center">
            <strong>{{ $mensaje }}</strong>
        </div>
    @endif
    <div class="row">
        <div class="col-md-8 mb-3">
            <form action="{{ route('ventas.store') }}" method="POST" id="ventaForm">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Detalles de la Venta</h5>
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        
                        <div class="form-group mb-3">
                            <label for="sucursal_id">Sucursal Activa</label>
                            <input type="text" class="form-control" value="{{ $sucursalActiva->nombre }}" readonly>
                            <input type="hidden" name="sucursal_id" value="{{ $sucursalActiva->id }}">
                        </div>

                        <div class="form-group mb-3">
                            <label for="metodo_pago_id">Método de Pago</label>
                            <select class="form-control" id="metodo_pago_id" name="metodo_pago_id" required>
                                <option value="" disabled selected>Seleccione un método de pago</option>
                                @foreach ($metodosPago as $metodo)
                                    <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-2" id="montoRecibidoGroup" style="display: none;">
                            <label for="monto_recibido">Monto Recibido</label>
                            <input type="number" class="form-control" id="monto_recibido" name="monto_recibido" placeholder="Ingrese el monto recibido" min="0">
                        </div>
                        <div class="form-group mb-2" id="vueltoGroup" style="display: none;">
                            <label for="vuelto">Vuelto</label>
                            <input type="text" class="form-control bg-light" id="vuelto" name="vuelto" readonly>
                        </div>
                        <input type="hidden" id="fecha" name="fecha" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                </div>

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

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Carrito de Compras</h5>
                    <div id="cart"></div>
                </div>
            </div>

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
                    <button type="button" class="btn btn-primary w-100 mb-3" onclick="submitForm()">
                        <i class="fas fa-shopping-cart"></i> Finalizar Venta
                    </button>
                    <!-- Botón Volver -->
                    <button type="button" class="btn btn-secondary w-100" onclick="window.history.back()">
                        <i class="fas fa-arrow-left"></i> Volver
                    </button>
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
    document.addEventListener('DOMContentLoaded', function () {
    const metodoPagoSelect = document.getElementById('metodo_pago_id');
    const montoRecibidoGroup = document.getElementById('montoRecibidoGroup');
    const montoRecibidoInput = document.getElementById('monto_recibido');
    const vueltoGroup = document.getElementById('vueltoGroup');
    const vueltoInput = document.getElementById('vuelto');
    const totalConIvaField = document.getElementById('totalConIva');

    function toggleMontoRecibido() {
        const metodoSeleccionado = metodoPagoSelect.options[metodoPagoSelect.selectedIndex].text;
        const totalConIva = parseFloat(totalConIvaField.value.replace('$', '')) || 0;

        if (metodoSeleccionado === "Efectivo" && totalConIva > 0) {
            montoRecibidoGroup.style.display = 'block';
        } else {
            montoRecibidoGroup.style.display = 'none';
            montoRecibidoInput.value = ''; // Limpiar el valor del monto recibido
            vueltoGroup.style.display = 'none';
            vueltoInput.value = ''; // Limpiar el vuelto
        }
    }

    function calculateVuelto() {
        const totalConIva = parseFloat(totalConIvaField.value.replace('$', '')) || 0;
        const montoRecibido = parseFloat(montoRecibidoInput.value) || 0;

        if (montoRecibido >= totalConIva) {
            const vuelto = montoRecibido - totalConIva;
            vueltoGroup.style.display = 'block';
            vueltoInput.value = `$${vuelto.toFixed(0)}`;
        } else {
            vueltoGroup.style.display = 'none';
            vueltoInput.value = '';
        }
    }

    // Detectar cambios en el método de pago
    metodoPagoSelect.addEventListener('change', toggleMontoRecibido);

    // Detectar cambios en el monto recibido
    montoRecibidoInput.addEventListener('input', calculateVuelto);

    // Actualizar lógica al cambiar el carrito o los totales
    function monitorCartChanges() {
        toggleMontoRecibido(); // Recalcular si se debe mostrar el campo
        calculateVuelto(); // Recalcular el vuelto si el monto recibido ya está ingresado
    }

    // Llamar a monitorCartChanges cada vez que el carrito cambia
    const cartElement = document.getElementById('cart');
    const totalElements = [totalConIvaField]; // Añadir cualquier otro elemento que afecte el total

    // Observar cambios en el carrito
    new MutationObserver(monitorCartChanges).observe(cartElement, { childList: true, subtree: true });

    // Observar cambios en el total
    totalElements.forEach((el) => {
        el.addEventListener('change', monitorCartChanges);
    });

    // Llamar a toggleMontoRecibido al cargar la página
    toggleMontoRecibido();
});

    
    // Cargar productos desde el backend
    let products = @json($productos);
    let cart = [];

    // Función para mostrar productos
    function displayProducts(products) {
    const table = document.getElementById('productTable');
    table.innerHTML = '';
    products.forEach(product => {
        const unidad = product.unidad_medida ? product.unidad_medida.abreviatura : 'N/A';
        const productRow = `
            <tr>
                <td>${product.nombre} - ${unidad}</td>
                <td>$${product.precioventa.toFixed(0)}</td>
                <td>${product.inventarios.length > 0 ? product.inventarios[0].cantidad : 'N/A'}</td>
                <td>
                    <button type="button" 
                            onclick="addProductToCart(${product.id}, '${product.nombre}', ${product.precioventa}, ${product.inventarios.length > 0 ? product.inventarios[0].cantidad : 0}, ${product.inventarios.length > 0 ? product.inventarios[0].id : 0}, '${unidad}')" 
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
    const initialQuantity = unitType === 'KG' ? 0.1 : 1; // Iniciar con 0.1 si es kg
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
        const quantityValue = item.unit === 'KG' ? item.quantity.toFixed(2) : item.quantity; // Mostrar decimales si es KG
        const readonly = item.unit === 'UND' ? 'readonly' : ''; // Definir readonly para productos con unidad UND
        
        const itemElement = `
            <div class="cart-item d-flex align-items-center justify-content-between mb-2">
                <input type="hidden" name="detalles[${index}][producto_id]" value="${item.id}">
                <input type="hidden" name="detalles[${index}][precio_unitario]" value="${item.price}">
                <input type="hidden" name="detalles[${index}][cantidad]" value="${item.quantity}">
                <input type="hidden" name="detalles[${index}][inventario_id]" value="${item.inventory_id}">
                <div>${item.name}</div>
                <div class="input-group input-group-sm quantity-adjuster">
                    <div class="input-group-prepend">
                        <button class="btn btn-decrement btn-outline-secondary" type="button" onclick="adjustQuantity(${item.id}, ${item.unit === 'KG' ? -0.1 : -1})">-</button>
                    </div>
                    <input 
                        type="text" 
                        class="form-control text-center" 
                        value="${quantityValue}" 
                        min="${item.unit === 'KG' ? 0.1 : 1}" 
                        ${readonly} 
                        onchange="inputQuantity(${item.id}, this.value)">
                    <div class="input-group-append">
                        <button class="btn btn-increment btn-outline-secondary" type="button" onclick="adjustQuantity(${item.id}, ${item.unit === 'KG' ? 0.1 : 1})">+</button>
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

    // Verifica si la unidad es KG o UND para ajustar la cantidad
    const nuevaCantidad = item.unit === 'KG' ? Math.max(0.1, item.quantity + change) : Math.max(1, item.quantity + change);

    // Verifica si la nueva cantidad es mayor al inventario disponible
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

    if (item.unit === 'UND') {
        // No permitir edición manual para productos en unidades
        Swal.fire({
            icon: 'error',
            title: 'Edición No Permitida',
            text: 'No puedes editar manualmente la cantidad de productos por unidad. Usa los botones + y -.',
        });
        updateCartDisplay(); // Restablecer el valor original
        return;
    }

    // Permitir edición manual sólo para productos con unidad KG
    let parsedValue = parseFloat(value);
    parsedValue = isNaN(parsedValue) ? 0.1 : parsedValue; // Si no es válido, asignar valor mínimo
    item.quantity = Math.max(0.1, parsedValue);
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
    const montoRecibidoInput = form.querySelector('input[name="monto_recibido"]'); // Campo de monto recibido

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

    // Inicializar monto_recibido
    let montoRecibido = null;

    // Si el método de pago es efectivo, validar el monto recibido
    if (metodoPago === '1') { // Supongamos que '1' es el ID de "Efectivo"
        if (!montoRecibidoInput || montoRecibidoInput.value.trim() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Monto Recibido Requerido',
                text: 'Por favor, ingrese el monto recibido para calcular el vuelto.',
                confirmButtonText: 'Aceptar'
            });
            return; // Salir si no hay monto recibido
        }
        montoRecibido = parseFloat(montoRecibidoInput.value);

        // Validar que el monto recibido sea mayor o igual al total
        const totalVenta = parseFloat(document.getElementById('totalConIva').value.replace('$', '').replace(',', '')); // Asegúrate de que tu totalConIva esté correctamente capturado
        if (isNaN(montoRecibido) || montoRecibido < totalVenta) {
            Swal.fire({
                icon: 'error',
                title: 'Monto Insuficiente',
                text: 'El monto recibido no puede ser menor al total de la venta.',
                confirmButtonText: 'Aceptar'
            });
            return; // Salir si el monto recibido es menor al total
        }
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
        monto_recibido: montoRecibido, // Incluir el monto recibido si aplica
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
    if (response.status === 200 && response.data.success) {
        const ventaId = response.data.venta_id;

        // Calcular el vuelto solo si el método de pago es "Efectivo"
        const totalVenta = parseFloat(document.getElementById('totalConIva').value.replace('$', '').replace(',', '')) || 0;
        let montoRecibido = totalVenta; // Asumimos que el monto recibido es igual al total para métodos distintos de efectivo
        let vuelto = 0;

        // Si el método de pago es efectivo, asignamos el monto recibido real y calculamos el vuelto
        const metodoPago = document.getElementById('metodo_pago_id').value;
        if (metodoPago === "1") { // Supongamos que "1" es el ID de "Efectivo"
            montoRecibido = parseFloat(document.getElementById('monto_recibido').value) || 0;
            vuelto = montoRecibido - totalVenta;
        }

        // Mostrar los datos en el modal
        document.getElementById('modalMontoRecibido').textContent = `$${montoRecibido.toFixed(0)}`;
        document.getElementById('modalVuelto').textContent = `$${vuelto.toFixed(0)}`;
        document.getElementById('modalTotalVenta').textContent = `$${totalVenta.toFixed(0)}`;

        // Mostrar el modal de confirmación
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
        // Determinar si el error tiene una respuesta del backend
    if (error.response && error.response.data) 
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