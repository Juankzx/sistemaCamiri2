@extends('adminlte::page')

@section('title', 'Crear Orden de Compra')

@section('content_header')
    <h1>Crear Orden de Compra</h1>
@stop

@section('content')
<div class="container">
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
                    <div class="col-md-6">
                        <!-- Número de Orden Actual -->
                        <div class="form-group">
                            <label for="numero_orden_actual">Número de Orden Actual</label>
                            <input type="text" class="form-control" id="numero_orden_actual" value="{{ $nuevoNumeroOrden }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Selección de Proveedor -->
                        <div class="form-group">
                            <label for="proveedor_id">Proveedor</label>
                            <select class="form-control" id="proveedor_id" name="proveedor_id" required>
                                <option value="">Seleccione un proveedor</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buscador de productos -->
        <div class="form-group">
            <label for="buscarProducto">Buscar Producto</label>
            <input type="text" id="buscarProducto" class="form-control mb-2" placeholder="Escriba para buscar un producto..." disabled>
            <ul id="listaProductos" class="list-group mb-3" 
                style="background-color: #f5f5f5; 
                    border-radius: 5px; 
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
                    max-height: 200px; 
                    overflow-y: auto;">
                <!-- Los productos se mostrarán aquí -->
            </ul>
        </div>

        <!-- Detalles de la Orden de Compra -->
        <div class="card">
            <div class="card-header">
                <h4>Detalles de la Orden de Compra</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="detallesOrdenCompra">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los productos seleccionados aparecerán aquí -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botón de Guardado -->
        <div class="text-right">
            <button type="submit" class="btn btn-primary mt-3">
                <i class="fas fa-save"></i> Guardar Todo
            </button>
        </div>
    </form>
</div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const productosAgregados = new Set(); // Para almacenar los productos seleccionados
    const listaProductos = document.getElementById('listaProductos');
    const tbody = document.querySelector('#detallesOrdenCompra tbody');
    const proveedorSelect = document.getElementById('proveedor_id');
    const buscarInput = document.getElementById('buscarProducto');

    // Productos cargados desde el backend
    const productos = @json($productos);

    // Mostrar productos solo si se selecciona un proveedor
    proveedorSelect.addEventListener('change', function () {
        const proveedorId = this.value;
        productosAgregados.clear(); // Limpiar productos seleccionados previos
        tbody.innerHTML = ''; // Limpiar la tabla de detalles

        if (proveedorId) {
            buscarInput.disabled = false; // Habilitar búsqueda
            filtrarProductos(); // Mostrar productos al cambiar proveedor
        } else {
            buscarInput.disabled = true; // Deshabilitar búsqueda
            listaProductos.innerHTML = ''; // Limpiar lista de productos
        }
    });

    // Evento de búsqueda en vivo
    buscarInput.addEventListener('input', filtrarProductos);

    // Función para filtrar productos y mostrarlos en el buscador
    function filtrarProductos() {
        const proveedorId = proveedorSelect.value;
        const query = buscarInput.value.toLowerCase();

        if (!proveedorId) return; // Si no hay proveedor seleccionado, no filtrar

        const productosFiltrados = productos.filter(p => 
            p.proveedor_id == proveedorId && 
            !productosAgregados.has(p.id) && 
            (p.nombre.toLowerCase().includes(query) || p.codigo_barra.includes(query))
        );

        listaProductos.innerHTML = '';

        productosFiltrados.forEach(producto => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action';
            li.textContent = `${producto.nombre} - ${producto.codigo_barra}`;
            li.style.cursor = 'pointer';
            li.onclick = () => agregarProducto(producto);
            listaProductos.appendChild(li);
        });
    }

    // Función para agregar producto a la tabla de detalles
    function agregarProducto(producto) {
        if (productosAgregados.has(producto.id)) return; // Evitar duplicados

        const index = tbody.children.length;
        productosAgregados.add(producto.id); // Agregar producto a la lista de seleccionados

        const row = `
            <tr class="detail-group">
                <td>
                    <input type="hidden" name="detalles[${index}][producto_id]" value="${producto.id}">
                    ${producto.nombre} (${producto.codigo_barra})
                </td>
                <td>
                    <input type="number" class="form-control" name="detalles[${index}][cantidad]" required placeholder="Cantidad">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(${producto.id}, this)">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;

        tbody.insertAdjacentHTML('beforeend', row);
        buscarInput.value = ''; // Limpiar el input de búsqueda
        filtrarProductos(); // Actualizar lista de productos disponibles
    }

    // Función para eliminar producto de la tabla de detalles y regresarlo al buscador
    window.eliminarProducto = function (productoId, boton) {
        productosAgregados.delete(productoId); // Quitar producto de la lista de seleccionados
        boton.closest('tr').remove(); // Remover fila de la tabla
        filtrarProductos(); // Actualizar lista de productos disponibles
    };

    // Inicializar estado de entrada de búsqueda
    buscarInput.disabled = true;
});
</script>

@stop
