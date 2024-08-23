@extends('adminlte::page')

@section('template_title')
    Métodos de Pago
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Métodos de Pago') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('metodos-pagos.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
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
                            <input type="text" id="searchName" class="form-control" placeholder="Buscar por nombre de método de pago...">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Nombre</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentMethodTableBody">
                                    @foreach ($metodosPagos as $metodosPago)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $metodosPago->nombre }}</td>
                                            <td>
                                                <form action="{{ route('metodos-pagos.destroy', $metodosPago->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('metodos-pagos.show', $metodosPago->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('metodos-pagos.edit', $metodosPago->id) }}"><i class="fa fa-fw fa-edit"></i></a>
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
                {!! $metodosPagos->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargamos los métodos de pago desde la variable PHP en un array de objetos JS
        const paymentMethods = @json($metodosPagos->toArray()); // Convertimos la colección a un array

        // Configuración de Fuse.js
        const options = {
            keys: ['nombre'], // Solo buscamos en el nombre de los métodos de pago
            threshold: 0.3 // Sensibilidad de la búsqueda
        };

        const fuse = new Fuse(paymentMethods.data, options);

        // Manejador del evento input para búsqueda por nombre
        document.getElementById('searchName').addEventListener('input', function(e) {
            const searchText = e.target.value.trim(); // Elimina espacios en blanco
            filterResults(searchText);
        });

        // Función para filtrar los resultados en función del nombre
        function filterResults(searchText) {
            let filteredMethods = paymentMethods.data;

            // Si hay texto en el campo de búsqueda, utilizamos Fuse.js para filtrar
            if (searchText !== '') {
                const result = fuse.search(searchText);
                filteredMethods = result.map(r => r.item);
            }

            displayPaymentMethods(filteredMethods);
        }

        // Función para mostrar los métodos de pago filtrados o completos
        function displayPaymentMethods(filteredMethods) {
            const tableBody = document.querySelector('#paymentMethodTableBody');
            tableBody.innerHTML = '';

            if (filteredMethods.length > 0) {
                filteredMethods.forEach((metodosPago, index) => {
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${metodosPago.nombre}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="/metodos-pagos/${metodosPago.id}"><i class="fa fa-fw fa-eye"></i></a>
                                <a class="btn btn-sm btn-success" href="/metodos-pagos/${metodosPago.id}/edit"><i class="fa fa-fw fa-edit"></i></a>
                                <form action="/metodos-pagos/${metodosPago.id}" method="POST" style="display:inline-block;">
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
                tableBody.innerHTML = '<tr><td colspan="3" class="text-center">No se encontraron métodos de pago.</td></tr>';
            }
        }

        // Mostrar todos los métodos de pago inicialmente
        displayPaymentMethods(paymentMethods.data);
    });
</script>
@endsection
