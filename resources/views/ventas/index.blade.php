@extends($isVendedor ? 'layouts.app' : 'adminlte::page')

@section('template_title')
    Ventas
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Ventas') }}
                            </span>

                            <!-- Botones alineados -->
                            <div class="float-right">
                                <a href="{{ route('ventas.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                                    {{ __('+ Agregar') }}
                                </a>
                                <a href="{{ route('cajas.index') }}" class="btn btn-secondary btn-sm float-right ml-2" data-placement="left">
                                    <i class="fas fa-cash-register"></i> {{ __('Ir a Cajas') }}
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
                        <!-- Formulario de búsqueda -->
                        <div class="row mb-3 p-2">
                            <div class="col-md-3">
                                <input type="text" id="searchUsuario" class="form-control" placeholder="Buscar por usuario...">
                            </div>
                            <div class="col-md-3">
                                <select id="searchSucursal" class="form-control">
                                    <option value="">Buscar por sucursal</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="searchMetodoPago" class="form-control">
                                    <option value="">Buscar por método de pago</option>
                                    @foreach($metodosPago as $metodo)
                                        <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" id="searchFecha" class="form-control" placeholder="Buscar por fecha...">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>N°</th>
                                        <th>Usuario</th>
                                        <th>Sucursal</th>
                                        <th>Método de Pago</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="ventasTableBody">
                                    @foreach ($ventas as $venta)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $venta->user->name }}</td>
                                            <td>{{ $venta->sucursal->nombre }}</td>
                                            <td>{{ $venta->metodo_pago->nombre }}</td>
                                            <td>{{ $venta->fecha ? \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                            <td>${{ number_format($venta->total, 0) }}</td>
                                            <td>
                                                <!-- Botones de acción para la venta -->
                                                <a class="btn btn-sm btn-primary" href="{{ route('ventas.show', $venta->id) }}" title="Ver Venta"><i class="fa fa-fw fa-eye"></i></a>
                                                <a class="btn btn-sm btn-info" href="{{ route('ventas.print', $venta->id) }}" target="_blank" title="Imprimir Boleta"><i class="fa fa-fw fa-print"></i></a>
                                                
                                                <!-- Solo permitir edición y eliminación si el usuario no es vendedor -->
                                                @if (!auth()->user()->hasRole('vendedor'))
                                                <a class="btn btn-sm btn-success" href="{{ route('ventas.edit', $venta->id) }}" title="Editar Venta"><i class="fa fa-fw fa-edit"></i></a>

                                                <!-- Botón de eliminación con confirmación -->
                                                <form action="{{ route('ventas.destroy', $venta->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta venta?');" title="Eliminar Venta"><i class="fa fa-fw fa-trash"></i></button>
                                                </form>
                                                @endif
                                                
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
       <!-- Sección de Paginación -->
       <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <p class="small text-muted">
                    Mostrando {{ $ventas->firstItem() }} a {{ $ventas->lastItem() }} de {{ $ventas->total() }} registros
                </p>
            </div>
            <div>
                {{ $ventas->links('pagination::bootstrap-4') }} <!-- Estilo Bootstrap 4 para la paginación -->
            </div>
        </div>
                    </div>
                </div>
            </div>

              

        </div>
    </div>

    <!-- Modales -->
    @include('modals.confirmacion')

@endsection

@section('js')

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ventas = @json($ventas->items());

        // Funciones de filtro
        document.getElementById('searchUsuario').addEventListener('input', filterVentas);
        document.getElementById('searchSucursal').addEventListener('change', filterVentas);
        document.getElementById('searchMetodoPago').addEventListener('change', filterVentas);
        document.getElementById('searchFecha').addEventListener('change', filterVentas);

        function filterVentas() {
            const searchUsuario = document.getElementById('searchUsuario').value.trim().toLowerCase();
            const searchSucursal = document.getElementById('searchSucursal').value;
            const searchMetodoPago = document.getElementById('searchMetodoPago').value;
            const searchFecha = document.getElementById('searchFecha').value;

            let filteredVentas = ventas;

            // Filtrar por nombre de usuario
            if (searchUsuario !== '') {
                filteredVentas = filteredVentas.filter(venta => venta.user.name.toLowerCase().includes(searchUsuario));
            }

            // Filtrar por sucursal
            if (searchSucursal !== '') {
                filteredVentas = filteredVentas.filter(venta => venta.sucursal.id == searchSucursal);
            }

            // Filtrar por método de pago
            if (searchMetodoPago !== '') {
                filteredVentas = filteredVentas.filter(venta => venta.metodo_pago.id == searchMetodoPago);
            }

            // Filtrar por fecha
            if (searchFecha !== '') {
                filteredVentas = filteredVentas.filter(venta => venta.fecha.startsWith(searchFecha));
            }

            displayVentas(filteredVentas);
        }

        function displayVentas(filteredVentas) {
            const tableBody = document.querySelector('#ventasTableBody');
            tableBody.innerHTML = '';

            if (filteredVentas.length > 0) {
                filteredVentas.forEach(venta => {
                    const row = `
                        <tr>
                            <td>${venta.id}</td>
                            <td>${venta.user.name}</td>
                            <td>${venta.sucursal.nombre}</td>
                            <td>${venta.metodo_pago.nombre}</td>
                            <td>${new Date(venta.fecha).toLocaleString()}</td>
                            <td>$${venta.total.toLocaleString()}</td>
                            <td>
                                <a href="/ventas/${venta.id}" class="btn btn-sm btn-primary">Ver</a>
                                <a href="/ventas/${venta.id}/edit" class="btn btn-sm btn-success">Editar</a>
                                <form action="/ventas/${venta.id}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar esta venta?');">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron ventas.</td></tr>';
            }
        }
    });
</script>
@endsection
