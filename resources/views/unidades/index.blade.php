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
                            <!-- Botón para eliminar con SweetAlert -->
                            <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $unidad->id }}" data-nombre="{{ $unidad->nombre }}"><i class="fa fa-fw fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Formulario oculto para eliminación -->
    <form id="deleteForm" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargamos las unidades desde la variable PHP en un array de objetos JS
        const units = @json($unidades);

        // Configuración de Fuse.js
        const options = {
            keys: ['nombre'],
            threshold: 0.3
        };

        const fuse = new Fuse(units, options);

        // Mostrar todas las unidades inicialmente
        displayUnits(units);

        // Manejador del evento input para búsqueda por nombre
        document.getElementById('searchName').addEventListener('input', function(e) {
            const searchText = e.target.value.trim();
            filterResults(searchText);
        });

        // Función para filtrar los resultados en función del nombre
        function filterResults(searchText) {
            let filteredUnits = units;

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
                filteredUnits.forEach((unidad) => {
                    const row = `
                        <tr>
                            <td>${unidad.id}</td>
                            <td>${unidad.nombre}</td>
                            <td>${unidad.abreviatura}</td>
                            <td>
                                <a href="/unidades/${unidad.id}" class="btn btn-sm btn-primary"><i class="fa fa-fw fa-eye"></i></a>
                                <a href="/unidades/${unidad.id}/edit" class="btn btn-sm btn-success"><i class="fa fa-fw fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${unidad.id}" data-nombre="${unidad.nombre}"><i class="fa fa-fw fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center">No se encontraron unidades.</td></tr>';
            }

            // Reasignar el evento de eliminación con SweetAlert para los botones recién generados
            addDeleteEvent();
        }

        // SweetAlert para la confirmación de eliminación
        function addDeleteEvent() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: `¿Deseas eliminar la unidad de medida "${nombre}"? Esta acción no se puede deshacer.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Si se confirma, enviar el formulario de eliminación con la URL correcta
                            const form = document.getElementById('deleteForm');
                            form.action = `/unidades/${id}`;
                            form.submit(); // Enviar el formulario de eliminación
                        }
                    });
                });
            });
        }

        // Añadir los eventos a los botones de eliminación existentes al cargar la página
        addDeleteEvent();

        // SweetAlert para mostrar mensajes de éxito, error y otros tipos de alerta
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error en la operación',
                html: '{!! implode("<br>", $errors->all()) !!}',
                showConfirmButton: true
            });
        @endif
    });
</script>
@endsection
