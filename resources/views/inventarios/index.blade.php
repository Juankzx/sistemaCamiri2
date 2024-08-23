@extends('adminlte::page')

@section('title', 'Inventarios')

@section('content_header')
    <h1>Inventario</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-12">
        <a href="{{ route('inventarios.create') }}" class="btn btn-primary mb-2">Agregar Inventario +</a>
    </div>
</div>

<div class="container">
    <!-- Campo de búsqueda en vivo -->
    <div class="form-group">
        <label for="search">Buscar:</label>
        <input type="text" id="inventorySearch" class="form-control" placeholder="Nombre de producto, bodega o sucursal">
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Bodega</th>
                <th>Sucursal</th>
                <th>Cantidad</th>
                <th>+/-</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="inventoryTableBody">
            @foreach($inventarios as $inventario)
            <tr>
                <td>{{ $inventario->producto->nombre }}</td>
                <td>{{ $inventario->bodega ? $inventario->bodega->nombre : 'N/A' }}</td>
                <td>{{ $inventario->sucursal ? $inventario->sucursal->nombre : 'N/A' }}</td>
                <td>{{ $inventario->cantidad }}</td>
                <td>
                    <button class="btn btn-info" data-toggle="modal" data-target="#incrementarModal" data-id="{{ $inventario->id }}"><i class="fa fa-fw fa-plus"></i></button>
                    <button class="btn btn-warning" data-toggle="modal" data-target="#decrementarModal" data-id="{{ $inventario->id }}"><i class="fa fa-fw fa-minus"></i></button>
                </td>
                <td>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#transferModal" data-id="{{ $inventario->id }}"><i class="fas fa-exchange-alt"></i></button> 
                    <a href="{{ route('inventarios.edit', $inventario->id) }}" class="btn btn-success"><i class="fa fa-fw fa-edit"></i></a>
                    <form action="{{ route('inventarios.destroy', $inventario->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fa fa-fw fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $inventarios->withQueryString()->links() }}
</div>
@include('modals.incrementar')
@include('modals.decrementar')
@include('modals.transferirsucursal')
@stop

@section('css')
    <style>
        .container { padding-top: 20px; }
    </style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargamos los inventarios desde la variable PHP en un array de objetos JS
        const inventories = @json($inventarios->items());

        // Configuración de Fuse.js
        const options = {
            keys: [
                'producto.nombre', // Nombre del producto
                'bodega.nombre',   // Nombre de la bodega
                'sucursal.nombre'  // Nombre de la sucursal
            ],
            threshold: 0.3 // Sensibilidad de la búsqueda (0 = coincidencia exacta, 1 = coincidencia amplia)
        };

        // Inicializamos Fuse con los inventarios y las opciones
        const fuse = new Fuse(inventories, options);

        // Manejador del evento input para la búsqueda en vivo
        document.getElementById('inventorySearch').addEventListener('input', function(e) {
            const searchText = e.target.value.trim(); // Elimina espacios en blanco al inicio y al final

            // Si el campo de búsqueda está vacío, mostrar todos los inventarios
            if (searchText === '') {
                displayInventories(inventories);
            } else {
                // Si hay un término de búsqueda, filtrar los inventarios
                const result = fuse.search(searchText);
                displayInventories(result.map(r => r.item));
            }
        });

        // Función para mostrar los inventarios (ya sean todos o filtrados)
        function displayInventories(filteredInventories) {
            const tableBody = document.querySelector('#inventoryTableBody');
            tableBody.innerHTML = '';

            if (filteredInventories.length > 0) {
                filteredInventories.forEach((inventario, index) => {
                    const row = `
                        <tr>
                            <td>${inventario.producto.nombre}</td>
                            <td>${inventario.bodega ? inventario.bodega.nombre : 'N/A'}</td>
                            <td>${inventario.sucursal ? inventario.sucursal.nombre : 'N/A'}</td>
                            <td>${inventario.cantidad}</td>
                            <td>
                                <button class="btn btn-info" data-toggle="modal" data-target="#incrementarModal" data-id="${inventario.id}"><i class="fa fa-fw fa-plus"></i></button>
                                <button class="btn btn-warning" data-toggle="modal" data-target="#decrementarModal" data-id="${inventario.id}"><i class="fa fa-fw fa-minus"></i></button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#transferModal" data-id="${inventario.id}"><i class="fas fa-exchange-alt"></i></button> 
                                <a href="/inventarios/${inventario.id}/edit" class="btn btn-success"><i class="fa fa-fw fa-edit"></i></a>
                                <form action="/inventarios/${inventario.id}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"><i class="fa fa-fw fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron inventarios.</td></tr>';
            }
        }

        // Mostrar todos los inventarios inicialmente
        displayInventories(inventories);
    });
</script>
@stop
