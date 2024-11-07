@extends('adminlte::page')

@section('content')
    <div class="container">
        <h1>Unidades de Medida</h1>
        <a href="{{ route('unidades.create') }}" class="btn btn-primary mb-3">+ Agregar</a>

        <!-- Campo de búsqueda en vivo -->
        <div class="form-group my-3">
            <input type="text" id="searchName" class="form-control" placeholder="Buscar por nombre de unidad...">
        </div>

        <!-- Tabla de unidades con estilo Bootstrap -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
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

        <!-- Información de la paginación y número de registros -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <p class="small text-muted">
                    Mostrando {{ $unidades->firstItem() }} a {{ $unidades->lastItem() }} de {{ $unidades->total() }} registros
                </p>
            </div>
            <div>
                {{ $unidades->links('pagination::bootstrap-4') }}
            </div>
        </div>
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
        const units = @json($unidades->items());

        const options = {
            keys: ['nombre'],
            threshold: 0.3
        };

        const fuse = new Fuse(units, options);

        displayUnits(units);

        document.getElementById('searchName').addEventListener('input', function(e) {
            const searchText = e.target.value.trim();
            filterResults(searchText);
        });

        function filterResults(searchText) {
            let filteredUnits = units;

            if (searchText !== '') {
                const result = fuse.search(searchText);
                filteredUnits = result.map(r => r.item);
            }

            displayUnits(filteredUnits);
        }

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

            addDeleteEvent();
        }

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
                            const form = document.getElementById('deleteForm');
                            form.action = `/unidades/${id}`;
                            form.submit();
                        }
                    });
                });
            });
        }

        addDeleteEvent();

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
