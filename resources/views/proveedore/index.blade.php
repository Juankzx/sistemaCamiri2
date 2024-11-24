@extends('adminlte::page')

@section('template_title')
    Proveedores
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Proveedores') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('proveedores.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                                  {{ __('+ Agregar') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <!-- Campo de búsqueda en vivo -->
                        <div class="form-group my-3">
                            <input type="text" id="searchName" class="form-control" placeholder="Buscar por nombre de proveedor...">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>N°</th>
                                        <th>Nombre</th>
                                        <th>Rut</th>
                                        <th>Dirección</th>
                                        <th>Teléfono</th>
                                        <th>Email</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="providerTableBody">
                                    @foreach ($proveedores as $index => $proveedore)
                                        <tr>
                                            <td>{{ $proveedores->firstItem() + $index }}</td>
                                            <td>{{ $proveedore->nombre }}</td>
                                            <td>{{ $proveedore->rut }}</td>
                                            <td>{{ $proveedore->direccion ?? 'N/A' }}</td>
                                            <td>{{ $proveedore->telefono ?? 'N/A' }}</td>
                                            <td>{{ $proveedore->email ?? 'N/A' }}</td>
                                            <td>
                                                <a class="btn btn-sm btn-primary" href="{{ route('proveedores.show', $proveedore->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                                <a class="btn btn-sm btn-success" href="{{ route('proveedores.edit', $proveedore->id) }}"><i class="fa fa-fw fa-edit"></i></a>
                                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $proveedore->id }}" data-nombre="{{ $proveedore->nombre }}"><i class="fa fa-fw fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- Paginación e información de registros -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="small text-muted">
                                Mostrando {{ $proveedores->firstItem() }} a {{ $proveedores->lastItem() }} de {{ $proveedores->total() }} registros
                            </p>
                        </div>
                        <div>
                            {{ $proveedores->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario oculto para eliminar el proveedor -->
    <form id="deleteForm" action="" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const providers = @json($proveedores->toArray());

        const options = {
            keys: ['nombre'],
            threshold: 0.3
        };

        const fuse = new Fuse(providers.data, options);

        document.getElementById('searchName').addEventListener('input', function(e) {
            const searchText = e.target.value.trim();
            filterResults(searchText);
        });

        function filterResults(searchText) {
            let filteredProviders = providers.data;
            if (searchText !== '') {
                const result = fuse.search(searchText);
                filteredProviders = result.map(r => r.item);
            }
            displayProviders(filteredProviders);
        }

        function displayProviders(filteredProviders) {
            const tableBody = document.querySelector('#providerTableBody');
            tableBody.innerHTML = '';

            if (filteredProviders.length > 0) {
                filteredProviders.forEach((proveedore, index) => {
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${proveedore.nombre}</td>
                            <td>${proveedore.rut}</td>
                            <td>${proveedore.direccion}</td>
                            <td>${proveedore.telefono}</td>
                            <td>${proveedore.email}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="/proveedores/${proveedore.id}"><i class="fa fa-fw fa-eye"></i></a>
                                <a class="btn btn-sm btn-success" href="/proveedores/${proveedore.id}/edit"><i class="fa fa-fw fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${proveedore.id}" data-nombre="${proveedore.nombre}"><i class="fa fa-fw fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron proveedores.</td></tr>';
            }
        }

        displayProviders(providers.data);

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar el proveedor "${nombre}"? Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const deleteForm = document.getElementById('deleteForm');
                        deleteForm.action = `/proveedores/${id}`;
                        deleteForm.submit();
                    }
                });
            });
        });

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
