@extends('adminlte::page')

@section('title', 'Crear Venta')

@section('content_header')
    <h1>Crear Venta</h1>
@stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('ventas.store') }}" method="POST" id="ventaForm">
                @csrf

                <!-- Información básica de la venta -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Datos de la Venta</h3>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        <div class="form-group">
                            <label for="sucursal_id">Sucursal:</label>
                            <select class="form-control" id="sucursal_id" name="sucursal_id" required>
                                <option value="" disabled selected>Seleccione una sucursal</option>
                                @foreach ($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="metodo_pago_id">Método de Pago:</label>
                            <select class="form-control" id="metodo_pago_id" name="metodo_pago_id" required>
                                <option value="" disabled selected>Seleccione un método de pago</option>
                                @foreach ($metodosPago as $metodo)
                                    <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="hidden" class="form-control" id="fecha" name="fecha" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Búsqueda y selección de productos -->
                <div class="card mt-3">
                    <div class="card-header bg-secondary text-white">
                        <h4>Buscar Productos</h4>
                    </div>
                    <div class="card-body">
                        <input type="text" id="productSearch" class="form-control mb-3" placeholder="Buscar producto por código o nombre...">
                        <div id="productGrid" class="d-flex flex-wrap">
                            <!-- Productos cargados aquí dinámicamente -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <!-- Carrito de compras -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4>Carrito de Compras</h4>
                </div>
                <div class="card-body" id="cart">
                    <!-- Los productos añadidos al carrito se mostrarán aquí -->
                </div>
            </div>

            <!-- Detalles del total de la venta -->
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h4>Total de la Venta</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Total Neto:</label>
                        <input type="text" id="totalNeto" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>IVA (19%):</label>
                        <input type="text" id="iva" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Total con IVA:</label>
                        <input type="text" id="totalConIva" class="form-control" readonly>
                    </div>
                    <button type="button" class="btn btn-primary btn-block" onclick="submitForm()">Finalizar Venta</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let products = @json($productos);
let cart = [];

function displayProducts(products) {
    const grid = document.getElementById('productGrid');
    grid.innerHTML = '';
    products.forEach(product => {
        const productCard = `
            <div class="card m-2" style="width: 10rem;">
                <div class="card-body">
                    <h5 class="card-title">${product.nombre}</h5>
                    <p class="card-text">Precio: $${product.precioventa.toFixed(2)}</p>
                    <p class="card-text">Cantidad: ${product.inventarios[0].cantidad}</p>
                    <button type="button" onclick="addProductToCart(${product.id}, '${product.nombre}', ${product.precioventa}, ${product.inventarios[0].cantidad}, ${product.inventarios[0].id})" class="btn btn-primary">Agregar</button>
                </div>
            </div>
        `;
        grid.innerHTML += productCard;
    });
}

function addProductToCart(productId, productName, productPrice, productQuantity, inventoryId) {
    const existingItem = cart.find(item => item.id === productId);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({ id: productId, name: productName, price: productPrice, quantity: 1, inventory: productQuantity, inventory_id: inventoryId });
    }
    updateCartDisplay();
}

function updateCartDisplay() {
    const cartElement = document.getElementById('cart');
    cartElement.innerHTML = '';
    cart.forEach((item, index) => {
        const itemElement = `
            <div class="cart-item d-flex align-items-center justify-content-between mb-2">
                <input type="hidden" name="detalles[${index}][producto_id]" value="${item.id}">
                <input type="hidden" name="detalles[${index}][precio_unitario]" value="${item.price}">
                <input type="hidden" name="detalles[${index}][cantidad]" value="${item.quantity}">
                <input type="hidden" name="detalles[${index}][inventario_id]" value="${item.inventory_id}">
                <div>${item.name}</div>
                <div class="input-group input-group-sm quantity-adjuster">
                    <div class="input-group-prepend">
                        <button class="btn btn-decrement btn-outline-secondary" type="button" onclick="adjustQuantity(${item.id}, -1)">-</button>
                    </div>
                    <input type="text" class="form-control text-center" value="${item.quantity}" min="1" onchange="inputQuantity(${item.id}, this.value)">
                    <div class="input-group-append">
                        <button class="btn btn-increment btn-outline-secondary" type="button" onclick="adjustQuantity(${item.id}, 1)">+</button>
                    </div>
                </div>
                <div>$${(item.quantity * item.price).toFixed(2)}</div>
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
    item.quantity = Math.max(1, item.quantity + change);
    updateCartDisplay();
}

function inputQuantity(productId, value) {
    const item = cart.find(item => item.id === productId);
    if (!item) return;
    item.quantity = Math.max(1, parseInt(value) || 1);
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

    document.getElementById('totalNeto').value = `$${totalNeto.toFixed(2)}`;
    document.getElementById('iva').value = `$${iva.toFixed(2)}`;
    document.getElementById('totalConIva').value = `$${totalConIva.toFixed(2)}`;
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
    const form = document.getElementById('ventaForm');
    cart.forEach((item, index) => {
        const productIdInput = document.createElement('input');
        productIdInput.type = 'hidden';
        productIdInput.name = `detalles[${index}][producto_id]`;
        productIdInput.value = item.id;
        form.appendChild(productIdInput);

        const precioUnitarioInput = document.createElement('input');
        precioUnitarioInput.type = 'hidden';
        precioUnitarioInput.name = `detalles[${index}][precio_unitario]`;
        precioUnitarioInput.value = item.price;
        form.appendChild(precioUnitarioInput);

        const cantidadInput = document.createElement('input');
        cantidadInput.type = 'hidden';
        cantidadInput.name = `detalles[${index}][cantidad]`;
        cantidadInput.value = item.quantity;
        form.appendChild(cantidadInput);

        const inventarioInput = document.createElement('input');
        inventarioInput.type = 'hidden';
        inventarioInput.name = `detalles[${index}][inventario_id]`;
        inventarioInput.value = item.inventory_id;
        form.appendChild(inventarioInput);
    });
    form.submit();
}

displayProducts(products); // Mostrar todos los productos al cargar la página
</script>

@stop

@section('css')
<style>
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
</style>
@endsection
