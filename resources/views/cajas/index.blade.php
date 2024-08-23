@extends('adminlte::page')

@section('title', 'Gestión de Cajas')

@section('content_header')
    <h1>Gestión de Cajas</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Cajas</h3>
                    @if (!$cajaAbierta)
                        <button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#abrirCajaModal">Abrir Nueva Caja</button>
                    @endif
                </div>

                <!-- Campos de búsqueda en vivo -->
                <div class="row mb-3 p-2">
                    <div class="col-md-4">
                        <input type="text" id="searchSucursal" class="form-control" placeholder="Buscar por sucursal...">
                    </div>
                    <div class="col-md-4">
                        <select id="searchEstado" class="form-control">
                            <option value="">Buscar por estado</option>
                            <option value="Abierta">Abierta</option>
                            <option value="Cerrada">Cerrada</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="date" id="searchFecha" class="form-control" placeholder="Buscar por fecha...">
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sucursal</th>
                                <th>Usuario</th>
                                <th>Fecha Apertura</th>
                                <th>Fecha Cierre</th>
                                <th>Monto Apertura</th>
                                <th>Monto Cierre</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="cajasTableBody">
                            @forelse ($cajas as $caja)
                                <tr>
                                    <td>{{ $caja->id }}</td>
                                    <td>{{ $caja->sucursal->nombre }}</td>
                                    <td>{{ $caja->user->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                    <td>$ {{ number_format($caja->monto_apertura, 0) }}</td>
                                    <td>$ {{ $caja->monto_cierre ? number_format($caja->monto_cierre, 0) : 'N/A' }}</td>
                                    <td>{{ $caja->estado ? 'Abierta' : 'Cerrada' }}</td>
                                    <td>
                                        <a href="{{ route('cajas.show', $caja->id) }}" class="btn btn-xs btn-primary">Ver</a>
                                        @if ($caja->estado)
                                            <button class="btn btn-xs btn-danger" data-toggle="modal" data-target="#cerrarCajaModal" data-id="{{ $caja->id }}" data-monto="{{ $montoVentas }}">Cerrar</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No hay cajas registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $cajas->links() }}
        </div>
    </div>
</div>

<!-- Modal para Abrir Caja -->
<div class="modal fade" id="abrirCajaModal" tabindex="-1" aria-labelledby="abrirCajaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="abrirCajaModalLabel">Abrir Caja</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('cajas.abrir') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sucursal_id">Sucursal:</label>
                        <select class="form-control" name="sucursal_id" required>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="monto_apertura">Monto de Apertura:</label>
                        <input type="number" class="form-control" name="monto_apertura" required value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Abrir Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Cerrar Caja -->
<div class="modal fade" id="cerrarCajaModal" tabindex="-1" aria-labelledby="cerrarCajaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cerrarCajaModalLabel">Cerrar Caja</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('cajas.cerrar', ['id' => $cajaAbierta ? $cajaAbierta->id : 0]) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="monto_cierre">Monto de Cierre:</label>
                        <input type="number" class="form-control" name="monto_cierre" id="monto_cierre" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cerrar Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .container-fluid {
            padding-top: 20px;
        }
    </style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cajas = @json($cajas); // Obtenemos las cajas desde PHP

        // Configuración de Fuse.js
        const options = {
            keys: ['sucursal.nombre', 'estado', 'fecha_apertura'], // Claves para búsqueda
            threshold: 0.3 // Sensibilidad
        };

        const fuse = new Fuse(cajas.data, options);

        // Mostrar todas las cajas inicialmente
        displayCajas(cajas.data);

        // Manejador para búsqueda por sucursal
        document.getElementById('searchSucursal').addEventListener('input', function(e) {
            filterCajas();
        });

        // Manejador para búsqueda por estado
        document.getElementById('searchEstado').addEventListener('change', function(e) {
            filterCajas();
        });

        // Manejador para búsqueda por fecha
        document.getElementById('searchFecha').addEventListener('change', function(e) {
            filterCajas();
        });

        // Función para filtrar las cajas
        function filterCajas() {
    const searchSucursal = document.getElementById('searchSucursal').value.trim();
    const searchEstado = document.getElementById('searchEstado').value;
    const searchFecha = document.getElementById('searchFecha').value;

    let filteredCajas = cajas.data;

    // Filtro por sucursal
    if (searchSucursal !== '') {
        const result = fuse.search(searchSucursal);
        filteredCajas = result.map(r => r.item);
    }

    // Filtro por estado
    if (searchEstado !== '') {
        filteredCajas = filteredCajas.filter(caja => {
            // Verificamos si el estado es booleano y lo convertimos a 'Abierta' o 'Cerrada'
            const estadoActual = caja.estado ? 'Abierta' : 'Cerrada';
            return estadoActual === searchEstado;
        });
    }

    // Filtro por fecha de apertura
    if (searchFecha !== '') {
        filteredCajas = filteredCajas.filter(caja => caja.fecha_apertura.startsWith(searchFecha));
    }

    displayCajas(filteredCajas);
}


        // Función para mostrar las cajas
        function displayCajas(filteredCajas) {
            const tableBody = document.querySelector('#cajasTableBody');
            tableBody.innerHTML = '';

            if (filteredCajas.length > 0) {
                filteredCajas.forEach(caja => {
                    const row = `
                        <tr>
                            <td>${caja.id}</td>
                            <td>${caja.sucursal.nombre}</td>
                            <td>${caja.user.name}</td>
                            <td>${new Date(caja.fecha_apertura).toLocaleString()}</td>
                            <td>${caja.fecha_cierre ? new Date(caja.fecha_cierre).toLocaleString() : 'N/A'}</td>
                            <td>$${caja.monto_apertura.toLocaleString()}</td>
                            <td>${caja.monto_cierre ? `$${caja.monto_cierre.toLocaleString()}` : 'N/A'}</td>
                            <td>${caja.estado ? 'Abierta' : 'Cerrada'}</td>
                            <td>
                                <a href="/cajas/${caja.id}" class="btn btn-xs btn-primary">Ver</a>
                                ${caja.estado ? `<button class="btn btn-xs btn-danger" data-toggle="modal" data-target="#cerrarCajaModal" data-id="${caja.id}" data-monto="${caja.monto_apertura}">Cerrar</button>` : ''}
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron cajas.</td></tr>';
            }
        }
    });
</script>
@stop
