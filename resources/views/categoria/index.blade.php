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
                                <a href="{{ route('categorias.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

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
                                                <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('categorias.show', $categoria->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('categorias.edit', $categoria->id) }}"><i class="fa fa-fw fa-edit"></i></a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i></button>
                                                </form>
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
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargamos las categorías desde la variable PHP en un array de objetos JS
        const categories = @json($categorias->toArray()); // Convertimos la colección de categorías a un array

        // Configuración de Fuse.js
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
                                <form action="/categorias/${categoria.id}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete?');"><i class="fa fa-fw fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No se encontraron categorías.</td></tr>';
            }
        }

        // Mostrar todas las categorías inicialmente
        displayCategories(categories.data);
    });
</script>
@endsection
