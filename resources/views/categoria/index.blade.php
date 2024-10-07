@extends('adminlte::page')

@section('template_title')
    Categorias
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            {{ __('Categorias') }}
                        </span>
                        <div class="float-right">
                            <a href="{{ route('categorias.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                              {{ __('+ Agregar') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body bg-white">
                    <!-- Campo de búsqueda en vivo -->
                    <div class="form-group my-3">
                        <input type="text" id="searchName" class="form-control" placeholder="Buscar por nombre de categoría...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead">
                                <tr>
                                    <th>No</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="categoryTableBody">
                                @foreach ($categorias as $categoria)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $categoria->nombre }}</td>
                                        <td>{{ $categoria->descripcion }}</td>
                                        <td>{{ $categoria->estado ? 'Activo' : 'Inactivo' }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="{{ route('categorias.show', $categoria->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                            <a class="btn btn-sm btn-success" href="{{ route('categorias.edit', $categoria->id) }}"><i class="fa fa-fw fa-edit"></i></a>
                                            <!-- Botón de eliminación con SweetAlert -->
                                            <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $categoria->id }}" data-nombre="{{ $categoria->nombre }}"><i class="fa fa-fw fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {!! $categorias->withQueryString()->links() !!}
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
    // Cargamos las categorías desde la variable PHP en un array de objetos JS
    const categories = @json($categorias->toArray()); // Convertimos la colección de categorías a un array

    // Configuración de Fuse.js para la búsqueda
    const options = {
        keys: ['nombre'], // Solo buscamos en el nombre de la categoría
        threshold: 0.3 // Sensibilidad de la búsqueda
    };

    const fuse = new Fuse(categories.data, options);

    // Manejador del evento input para búsqueda por nombre
    document.getElementById('searchName').addEventListener('input', function(e) {
        const searchText = e.target.value.trim();
        filterResults(searchText);
    });

    // Función para filtrar los resultados en función del nombre
    function filterResults(searchText) {
        let filteredCategories = categories.data;

        // Si hay texto en el campo de búsqueda, utilizamos Fuse.js para filtrar
        if (searchText !== '') {
            const result = fuse.search(searchText);
            filteredCategories = result.map(r => r.item);
        }

        displayCategories(filteredCategories);
    }

    // Función para mostrar las categorías filtradas o completas
    function displayCategories(filteredCategories) {
        const tableBody = document.querySelector('#categoryTableBody');
        tableBody.innerHTML = '';

        if (filteredCategories.length > 0) {
            filteredCategories.forEach((categoria, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${categoria.nombre}</td>
                        <td>${categoria.descripcion}</td>
                        <td>${categoria.estado ? 'Activo' : 'Inactivo'}</td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="/categorias/${categoria.id}"><i class="fa fa-fw fa-eye"></i></a>
                            <a class="btn btn-sm btn-success" href="/categorias/${categoria.id}/edit"><i class="fa fa-fw fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${categoria.id}" data-nombre="${categoria.nombre}"><i class="fa fa-fw fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        } else {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No se encontraron categorías.</td></tr>';
        }

        bindDeleteButtons(); // Vuelve a asignar el evento a los botones
    }

    // Función para manejar la eliminación con SweetAlert
    function bindDeleteButtons() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar la categoría "${nombre}"? Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const deleteForm = document.getElementById('deleteForm');
                        deleteForm.action = `/categorias/${id}`;
                        deleteForm.submit();
                    }
                });
            });
        });
    }

    // Mostrar todas las categorías inicialmente y enlazar los botones de eliminar
    displayCategories(categories.data);

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
