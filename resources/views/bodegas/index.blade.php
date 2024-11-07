@extends('adminlte::page')

@section('title', 'Bodegas')

@section('content')
<div class="container">
    <h1>Lista de Bodegas</h1>
    <a href="{{ route('bodegas.create') }}" class="btn btn-primary">Agregar Bodega</a>

    <!-- Campo de búsqueda en vivo -->
    <div class="form-group my-3">
        <input type="text" id="searchName" class="form-control" placeholder="Buscar por nombre de bodega...">
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
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
                            <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $bodega->id }}" data-nombre="{{ $bodega->nombre }}"><i class="fa fa-fw fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación e información de registros -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <p class="small text-muted">
                    Mostrando {{ $bodegas->firstItem() }} a {{ $bodegas->lastItem() }} de {{ $bodegas->total() }} registros
                </p>
            </div>
            <div>
                {{ $bodegas->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <!-- Formulario oculto para eliminación -->
    <form id="deleteForm" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmación de eliminación con SweetAlert
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Evitar la acción predeterminada del botón

                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');

                console.log('Delete button clicked for ID:', id); // Verificar si el evento se dispara

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas desactivar la bodega "${nombre}"? Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, desactivar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const deleteForm = document.getElementById('deleteForm');
                        deleteForm.action = `/bodegas/${id}`;
                        deleteForm.submit();
                    }
                });
            });
        });

        // Búsqueda en vivo con Fuse.js
        const warehouses = @json($bodegas->items());
        const options = { keys: ['nombre'], threshold: 0.3 };
        const fuse = new Fuse(warehouses, options);

        document.getElementById('searchName').addEventListener('input', function(e) {
            const searchText = e.target.value.trim();
            filterResults(searchText);
        });

        function filterResults(searchText) {
            let filteredWarehouses = warehouses;
            if (searchText !== '') {
                const result = fuse.search(searchText);
                filteredWarehouses = result.map(r => r.item);
            }
            displayWarehouses(filteredWarehouses);
        }

        function displayWarehouses(filteredWarehouses) {
            const tableBody = document.querySelector('#warehouseTableBody');
            tableBody.innerHTML = '';

            if (filteredWarehouses.length > 0) {
                filteredWarehouses.forEach((bodega, index) => {
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${bodega.nombre}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="/bodegas/${bodega.id}"><i class="fa fa-fw fa-eye"></i></a>    
                                <a href="/bodegas/${bodega.id}/edit" class="btn btn-sm btn-success"><i class="fa fa-fw fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${bodega.id}" data-nombre="${bodega.nombre}"><i class="fa fa-fw fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="3" class="text-center">No se encontraron bodegas.</td></tr>';
            }
        }

        displayWarehouses(warehouses);
    });
</script>
@endsection
