@extends('adminlte::page')

@section('content')
    <div class="container">
        <h1>Lista de Bodegas</h1>
        <a href="{{ route('bodegas.create') }}" class="btn btn-primary">Agregar Bodega</a>

        <!-- Campo de búsqueda en vivo -->
        <div class="form-group my-3">
            <input type="text" id="searchName" class="form-control" placeholder="Buscar por nombre de bodega...">
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="warehouseTableBody">
                @foreach($bodegas as $bodega)
                    <tr>
                        <td>{{ $bodega->id }}</td>
                        <td>{{ $bodega->nombre }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="{{ route('bodegas.show', $bodega->id) }}"><i class="fa fa-fw fa-eye"></i></a>    
                            <a href="{{ route('bodegas.edit', $bodega->id) }}" class="btn btn-sm btn-success"><i class="fa fa-fw fa-edit"></i></a>
                            <form action="{{ route('bodegas.destroy', $bodega->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargamos las bodegas desde la variable PHP en un array de objetos JS
        const warehouses = @json($bodegas->toArray()); // Cambiamos a toArray()

        // Configuración de Fuse.js
        const options = {
            keys: ['nombre'], // Solo se busca en el nombre
            threshold: 0.3 // Sensibilidad de la búsqueda
        };

        const fuse = new Fuse(warehouses, options);

        // Manejador del evento input para búsqueda por nombre
        document.getElementById('searchName').addEventListener('input', function(e) {
            const searchText = e.target.value.trim(); // Elimina espacios en blanco
            filterResults(searchText);
        });

        // Función para filtrar los resultados en función del nombre
        function filterResults(searchText) {
            let filteredWarehouses = warehouses;

            // Si hay texto en el campo de búsqueda, utilizamos Fuse.js para filtrar
            if (searchText !== '') {
                const result = fuse.search(searchText);
                filteredWarehouses = result.map(r => r.item);
            }

            displayWarehouses(filteredWarehouses);
        }

        // Función para mostrar las bodegas filtradas o completas
        function displayWarehouses(filteredWarehouses) {
            const tableBody = document.querySelector('#warehouseTableBody');
            tableBody.innerHTML = '';

            if (filteredWarehouses.length > 0) {
                filteredWarehouses.forEach((bodega) => {
                    const row = `
                        <tr>
                            <td>${bodega.id}</td>
                            <td>${bodega.nombre}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="/bodegas/${bodega.id}"><i class="fa fa-fw fa-eye"></i></a>    
                                <a href="/bodegas/${bodega.id}/edit" class="btn btn-sm btn-success"><i class="fa fa-fw fa-edit"></i></a>
                                <form action="/bodegas/${bodega.id}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="3" class="text-center">No se encontraron bodegas.</td></tr>';
            }
        }

        // Mostrar todas las bodegas inicialmente
        displayWarehouses(warehouses);
    });
</script>
@endsection
