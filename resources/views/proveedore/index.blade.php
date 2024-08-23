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

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

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
                                    @foreach ($proveedores as $proveedore)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $proveedore->nombre }}</td>
                                            <td>{{ $proveedore->rut }}</td>
                                            <td>{{ $proveedore->direccion }}</td>
                                            <td>{{ $proveedore->telefono }}</td>
                                            <td>{{ $proveedore->email }}</td>
                                            <td>
                                                <form action="{{ route('proveedores.destroy', $proveedore->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('proveedores.show', $proveedore->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('proveedores.edit', $proveedore->id) }}"><i class="fa fa-fw fa-edit"></i></a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('¿Estás seguro de eliminar?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $proveedores->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargamos los proveedores desde la variable PHP en un array de objetos JS
        const providers = @json($proveedores->toArray()); // Convertimos la colección de proveedores a un array

        // Configuración de Fuse.js
        const options = {
            keys: ['nombre'], // Solo buscamos en el nombre de los proveedores
            threshold: 0.3 // Sensibilidad de la búsqueda
        };

        const fuse = new Fuse(providers.data, options);

        // Manejador del evento input para búsqueda por nombre
        document.getElementById('searchName').addEventListener('input', function(e) {
            const searchText = e.target.value.trim(); // Elimina espacios en blanco
            filterResults(searchText);
        });

        // Función para filtrar los resultados en función del nombre
        function filterResults(searchText) {
            let filteredProviders = providers.data;

            // Si hay texto en el campo de búsqueda, utilizamos Fuse.js para filtrar
            if (searchText !== '') {
                const result = fuse.search(searchText);
                filteredProviders = result.map(r => r.item);
            }

            displayProviders(filteredProviders);
        }

        // Función para mostrar los proveedores filtrados o completos
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
                                <form action="/proveedores/${proveedore.id}" method="POST" style="display:inline-block;">
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
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron proveedores.</td></tr>';
            }
        }

        // Mostrar todos los proveedores inicialmente
        displayProviders(providers.data);
    });
</script>
@endsection
