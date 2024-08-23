@extends('adminlte::page')

@section('content')
    <div class="container">
        <h1>Unidades de Medida</h1>
        <a href="{{ route('unidades.create') }}" class="btn btn-primary">Crear Nueva Unidad</a>

        <!-- Campo de búsqueda en vivo -->
        <div class="form-group my-3">
            <input type="text" id="searchName" class="form-control" placeholder="Buscar por nombre de unidad...">
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Abreviatura</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="unitTableBody">
                @foreach ($unidades as $unidad)
                    <tr>
                        <td>{{ $unidad->id }}</td>
                        <td>{{ $unidad->nombre }}</td>
                        <td>{{ $unidad->abreviatura }}</td>
                        <td>
                            <a href="{{ route('unidades.show', $unidad) }}" class="btn btn-sm btn-primary"><i class="fa fa-fw fa-eye"></i></a>
                            <a href="{{ route('unidades.edit', $unidad) }}" class="btn btn-sm btn-success"><i class="fa fa-fw fa-edit"></i></a>
                            <form action="{{ route('unidades.destroy', $unidad) }}" method="POST" style="display:inline-block;">
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
        // Cargamos las unidades desde la variable PHP en un array de objetos JS
        const units = @json($unidades); // Simplemente utilizamos @json($unidades)

        // Configuración de Fuse.js
        const options = {
            keys: ['nombre'], // Solo buscamos en el nombre de las unidades
            threshold: 0.3 // Sensibilidad de la búsqueda
        };

        const fuse = new Fuse(units, options);

        // Mostrar todas las unidades inicialmente
        displayUnits(units);

        // Manejador del evento input para búsqueda por nombre
        document.getElementById('searchName').addEventListener('input', function(e) {
            const searchText = e.target.value.trim(); // Elimina espacios en blanco
            filterResults(searchText);
        });

        // Función para filtrar los resultados en función del nombre
        function filterResults(searchText) {
            let filteredUnits = units;

            // Si hay texto en el campo de búsqueda, utilizamos Fuse.js para filtrar
            if (searchText !== '') {
                const result = fuse.search(searchText);
                filteredUnits = result.map(r => r.item);
            }

            displayUnits(filteredUnits);
        }

        // Función para mostrar las unidades filtradas o completas
        function displayUnits(filteredUnits) {
            const tableBody = document.querySelector('#unitTableBody');
            tableBody.innerHTML = '';

            if (filteredUnits.length > 0) {
                filteredUnits.forEach((unidad, index) => {
                    const row = `
                        <tr>
                            <td>${unidad.id}</td>
                            <td>${unidad.nombre}</td>
                            <td>${unidad.abreviatura}</td>
                            <td>
                                <a href="/unidades/${unidad.id}" class="btn btn-sm btn-primary"><i class="fa fa-fw fa-eye"></i></a>
                                <a href="/unidades/${unidad.id}/edit" class="btn btn-sm btn-success"><i class="fa fa-fw fa-edit"></i></a>
                                <form action="/unidades/${unidad.id}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar?');"><i class="fa fa-fw fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center">No se encontraron unidades.</td></tr>';
            }
        }
    });
</script>
@endsection
