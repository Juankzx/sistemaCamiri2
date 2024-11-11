@extends('adminlte::page')

@section('title', 'Agregar Inventario')

@section('content')
<div class="container">
    <h1>Agregar Inventario</h1>
    <form id="inventarioForm" action="{{ route('inventarios.storeMultiple') }}" method="POST">
        @csrf

        <!-- Buscador de productos en vivo -->
        <div class="form-group">
            <label for="buscarProducto">Buscar Producto</label>
            <input type="text" class="form-control mb-2" id="buscarProducto" placeholder="Escriba para buscar un producto..." oninput="filtrarProductos()">
            <ul id="listaProductos" class="list-group mb-3" style="background-color: #f5f5f5; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto;">
                <!-- Lista de productos para seleccionar -->
            </ul>
        </div>

        <!-- Detalles del Inventario -->
        <div class="card mt-3">
            <div class="card-header bg-primary text-white">
                <h4>Detalles del Inventario</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="detalles-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Bodega</th>
                            <th>Cantidad</th>
                            <th>Stock Mínimo</th>
                            <th>Stock Crítico</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los detalles se cargarán aquí vía JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botón de Guardar -->
        <button type="submit" class="btn btn-primary mt-3">Guardar Inventario</button>
    </form>
</div>
@endsection

@section('css')
<style>
    .card {
        margin-top: 20px;
    }
    .card-header {
        background-color: #007bff;
        color: white;
    }
    .form-control {
        border-radius: 5px;
        height: 38px;
    }
</style>
@endsection

@section('js')
<script>
    // Cargar productos disponibles
    const productos = [
        @foreach($productos as $producto)
            {
                id: "{{ $producto->id }}",
                nombre: "{{ $producto->nombre }}",
                codigo_barra: "{{ $producto->codigo_barra }}"
            },
        @endforeach
    ];

    // Filtrar productos disponibles según la búsqueda
    function filtrarProductos() {
        const query = document.getElementById('buscarProducto').value.toLowerCase();
        const lista = document.getElementById('listaProductos');
        lista.innerHTML = '';
        
        const productosFiltrados = productos.filter(p => p.nombre.toLowerCase().includes(query) || p.codigo_barra.includes(query));
        
        productosFiltrados.forEach(producto => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action';
            li.innerHTML = `${producto.nombre} - ${producto.codigo_barra}`;
            li.onclick = () => agregarProducto(producto);
            lista.appendChild(li);
        });
    }

    // Agregar producto a la tabla de detalles
    function agregarProducto(producto) {
        const tbody = document.querySelector('#detalles-table tbody');
        const productoExiste = Array.from(tbody.querySelectorAll('input[name^="detalles"]')).some(input => input.value == producto.id);

        if (productoExiste) {
            Swal.fire({
                icon: 'warning',
                title: 'Producto duplicado',
                text: 'El producto ya está en la lista de inventario.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        const index = tbody.children.length;
        const row = `
            <tr class="product-row">
                <td>
                    <input type="hidden" name="detalles[${index}][producto_id]" value="${producto.id}" required>
                    <input type="text" class="form-control" name="detalles[${index}][producto_nombre]" value="${producto.nombre}" readonly required>
                </td>
                <td>
                    <input type="text" class="form-control" name="detalles[${index}][bodega]" value="Bodega General" required>
                </td>
                <td>
                    <input type="number" class="form-control" name="detalles[${index}][cantidad]" min="1" placeholder="Cantidad" value="1" required>
                </td>
                <td>
                    <input type="number" class="form-control" name="detalles[${index}][stock_minimo]" min="0" placeholder="Stock Mínimo" value="0" required>
                </td>
                <td>
                    <input type="number" class="form-control" name="detalles[${index}][stock_critico]" min="0" placeholder="Stock Crítico" value="0" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
        document.getElementById('buscarProducto').value = '';
        document.getElementById('listaProductos').innerHTML = '';
    }

    // Eliminar fila de la tabla
    function removeRow(button) {
        button.closest('tr').remove();
    }
</script>
@endsection
