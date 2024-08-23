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
                                                <form action="{{ route('sucursales.destroy', $sucursale->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('sucursales.show', $sucursale->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('sucursales.edit', $sucursale->id) }}"><i class="fa fa-fw fa-edit"></i></a>
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
                {!! $sucursales->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargamos las sucursales desde la variable PHP en un array de objetos JS
        const branches = @json($sucursales->toArray()); // Convertimos la colección a un array

        // Configuración de Fuse.js
        const options = {
            keys: ['nombre'], // Solo buscamos en el nombre de las sucursales
            threshold: 0.3 // Sensibilidad de la búsqueda
        };

        const fuse = new Fuse(branches.data, options);

        // Manejador del evento input para búsqueda por nombre
        document.getElementById('searchName').addEventListener('input', function(e) {
            const searchText = e.target.value.trim(); // Elimina espacios en blanco
            filterResults(searchText);
        });

        // Función para filtrar los resultados en función del nombre
        function filterResults(searchText) {
            let filteredBranches = branches.data;

            // Si hay texto en el campo de búsqueda, utilizamos Fuse.js para filtrar
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
                                <form action="/sucursales/${sucursale.id}" method="POST" style="display:inline-block;">
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
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center">No se encontraron sucursales.</td></tr>';
            }
        }

        // Mostrar todas las sucursales inicialmente
        displayBranches(branches.data);
    });
</script>
@endsection
