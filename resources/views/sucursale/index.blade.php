@extends('adminlte::page')

@section('template_title')
    Sucursales
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Sucursales') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('sucursales.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                                  {{ __('+ Agregar') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <!-- Campo de búsqueda en vivo -->
                        <div class="form-group my-3">
                            <input type="text" id="searchName" class="form-control" placeholder="Buscar por nombre de sucursal...">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>N°</th>
                                        <th>Nombre</th>
                                        <th>Dirección</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="branchTableBody">
                                    @foreach ($sucursales as $sucursale)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $sucursale->nombre }}</td>
                                            <td>{{ $sucursale->direccion }}</td>
                                            <td>
                                                <a class="btn btn-sm btn-primary" href="{{ route('sucursales.show', $sucursale->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                                <a class="btn btn-sm btn-success" href="{{ route('sucursales.edit', $sucursale->id) }}"><i class="fa fa-fw fa-edit"></i></a>
                                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $sucursale->id }}" data-nombre="{{ $sucursale->nombre }}"><i class="fa fa-fw fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $sucursales->withQueryString()->links() !!}
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
        // Cargamos las sucursales desde la variable PHP en un array de objetos JS
        const branches = @json($sucursales->toArray());

        // Configuración de Fuse.js
        const options = {
            keys: ['nombre'], // Solo buscamos en el nombre de las sucursales
            threshold: 0.3 // Sensibilidad de la búsqueda
        };

        const fuse = new Fuse(branches.data, options);

        // Manejador del evento input para búsqueda por nombre
        document.getElementById('searchName').addEventListener('input', function(e) {
            const searchText = e.target.value.trim();
            filterResults(searchText);
        });

        // Función para filtrar los resultados en función del nombre
        function filterResults(searchText) {
            let filteredBranches = branches.data;

            if (searchText !== '') {
                const result = fuse.search(searchText);
                filteredBranches = result.map(r => r.item);
            }

            displayBranches(filteredBranches);
        }

        // Función para mostrar las sucursales filtradas o completas
        function displayBranches(filteredBranches) {
            const tableBody = document.querySelector('#branchTableBody');
            tableBody.innerHTML = '';

            if (filteredBranches.length > 0) {
                filteredBranches.forEach((sucursale, index) => {
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${sucursale.nombre}</td>
                            <td>${sucursale.direccion}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="/sucursales/${sucursale.id}"><i class="fa fa-fw fa-eye"></i></a>
                                <a class="btn btn-sm btn-success" href="/sucursales/${sucursale.id}/edit"><i class="fa fa-fw fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${sucursale.id}" data-nombre="${sucursale.nombre}"><i class="fa fa-fw fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center">No se encontraron sucursales.</td></tr>';
            }
        }

        // Mostrar todas las sucursales inicialmente
        displayBranches(branches.data);

        // SweetAlert para confirmación de eliminación y eliminación real
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                const deleteForm = document.getElementById('deleteForm');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar la sucursal "${nombre}"? Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Asignar la acción dinámica al formulario oculto
                        deleteForm.action = `/sucursales/${id}`;
                        deleteForm.submit();
                    }
                });
            });
        });

        // SweetAlert para mensajes de éxito, error y otros tipos de alerta
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
