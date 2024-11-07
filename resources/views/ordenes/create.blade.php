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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero_orden">Número de Orden</label>
                            <input type="text" class="form-control" id="numero_orden" name="numero_orden" value="{{ $nuevoNumeroOrden }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
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
            <input type="text" class="form-control mb-2" id="buscarProducto" placeholder="Escriba para buscar un producto..." oninput="filtrarProductos()">
            <ul id="listaProductos" class="list-group mb-3" style="background-color: #f5f5f5; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto;">
                <!-- Los productos se mostrarán aquí -->
            </ul>
        </div>

        <!-- Detalles de la Orden de Compra -->
        <div class="card">
            <div class="card-header">
                <h4>Detalles de la Orden de Compra</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="detallesOrdenCompra">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí se insertarán dinámicamente los detalles -->
                        </tbody>
                    </table>
                </div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const productos = [
        @foreach($productos as $producto)
            {
                id: "{{ $producto->id }}",
                nombre: "{{ $producto->nombre }}",
                codigo_barra: "{{ $producto->codigo_barra }}",
                categoria: "{{ $producto->categoria->nombre ?? 'Sin categoría' }}"
            },
        @endforeach
    ];

    let productosAgregados = new Set();  // Para almacenar los productos agregados

    // Cargar todos los productos en la lista inicial
    function cargarProductos() {
        const lista = document.getElementById('listaProductos');
        lista.innerHTML = '';
        
        productos.forEach(producto => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action';
            li.style.cursor = 'pointer';
            li.innerHTML = `${producto.nombre} - ${producto.codigo_barra} - ${producto.categoria}`;
            li.onclick = () => seleccionarProducto(producto);
            lista.appendChild(li);
        });
    }

    // Filtrar productos en vivo mientras se escribe en el campo de búsqueda
    function filtrarProductos() {
        const query = document.getElementById('buscarProducto').value.toLowerCase();
        const lista = document.getElementById('listaProductos');
        lista.innerHTML = '';

        const productosFiltrados = productos.filter(p => p.nombre.toLowerCase().includes(query));
        
        productosFiltrados.forEach(producto => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action';
            li.style.cursor = 'pointer';
            li.innerHTML = `${producto.nombre} - ${producto.codigo_barra} - ${producto.categoria}`;
            li.onclick = () => seleccionarProducto(producto);
            lista.appendChild(li);
        });
    }

    // Llamar a cargarProductos() al cargar la página
    document.addEventListener('DOMContentLoaded', cargarProductos);

    // Función para seleccionar un producto de la lista y agregarlo a la tabla
    function seleccionarProducto(producto) {
        if (productosAgregados.has(producto.id)) {
            Swal.fire({
                icon: 'warning',
                title: 'Producto duplicado',
                text: 'Este producto ya ha sido agregado.',
            });
            return;
        }

        const container = document.querySelector('#detallesOrdenCompra tbody');
        const index = container.children.length;

        const html = `
            <tr class="detail-group">
                <td>
                    <input type="hidden" name="detalles[${index}][producto_id]" value="${producto.id}">
                    ${producto.nombre} - ${producto.codigo_barra} - ${producto.categoria}
                </td>
                <td>
                    <input type="number" class="form-control" name="detalles[${index}][cantidad]" required placeholder="Cantidad">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeDetail(this)">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;

        container.insertAdjacentHTML('beforeend', html);
        productosAgregados.add(producto.id);
        document.getElementById('buscarProducto').value = '';
        filtrarProductos();
    }

    // Función para eliminar un detalle de la tabla
    function removeDetail(element) {
        const detailRow = element.closest('.detail-group');
        const productoId = detailRow.querySelector('input[type="hidden"]').value;
        productosAgregados.delete(productoId);
        detailRow.remove();
    }
</script>

@endsection
