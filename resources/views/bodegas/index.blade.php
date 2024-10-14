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
                            <form action="{{ route('bodegas.destroy', $bodega->id) }}" method="POST" style="display:inline-block;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-btn"><i class="fa fa-fw fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Sección de Paginación -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <p class="small text-muted">
                    Mostrando {{ $bodegas->firstItem() }} a {{ $bodegas->lastItem() }} de {{ $bodegas->total() }} registros
                </p>
            </div>
            <div>
                {{ $bodegas->links('pagination::bootstrap-4') }} <!-- Estilo Bootstrap 4 para la paginación -->
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert para mostrar mensajes de éxito o error
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
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

        // Confirmación de eliminación con SweetAlert
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Prevenir el envío inmediato del formulario
                var form = this.closest('form'); // Obtener el formulario más cercano al botón

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción eliminará la bodega de forma permanente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Enviar el formulario si se confirma la acción
                    }
                });
            });
        });

        // Búsqueda en vivo con Fuse.js
        const warehouses = @json($bodegas->items()); // Usar los elementos actuales de la página
        const options = {
            keys: ['nombre'],
            threshold: 0.3
        };
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
                filteredWarehouses.forEach((bodega) => {
                    const row = `
                        <tr>
                            <td>${bodega.id}</td>
                            <td>${bodega.nombre}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="/bodegas/${bodega.id}"><i class="fa fa-fw fa-eye"></i></a>    
                                <a href="/bodegas/${bodega.id}/edit" class="btn btn-sm btn-success"><i class="fa fa-fw fa-edit"></i></a>
                                <form action="/bodegas/${bodega.id}" method="POST" style="display:inline-block;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm delete-btn"><i class="fa fa-fw fa-trash"></i></button>
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

        displayWarehouses(warehouses);
    });
</script>
@endsection
