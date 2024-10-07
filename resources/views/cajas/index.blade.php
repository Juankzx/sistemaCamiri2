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
                    
                    @if ($cajaAbierta)
                        <!-- Botón de cerrar caja en la cabecera con total de ventas -->
                        <button class="btn btn-danger btn-sm float-right" data-toggle="modal" data-target="#cerrarCajaModal" 
                                data-id="{{ $cajaAbierta->id }}" 
                                data-ventas="{{ $montoVentas }}" 
                                title="Cerrar Caja">
                            <i class="fas fa-lock"></i>
                        </button>
                    @else
                        <!-- Botón de abrir caja en la cabecera -->
                        <button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#abrirCajaModal">
                            <i class="fas fa-cash-register"></i>
                        </button>
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
                                        <!-- Botón de Ver -->
                                        <a href="{{ route('cajas.show', $caja->id) }}" class="btn btn-xs btn-info" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (!$caja->estado)
                                            <!-- Botón para Imprimir Boleta solo si la caja está cerrada -->
                                            <a href="{{ route('cajas.imprimir_boleta', $caja->id) }}" class="btn btn-xs btn-success" title="Imprimir Boleta" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
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
            <!-- Paginación personalizada con Bootstrap -->
            <div class="d-flex justify-content-center">
                {{ $cajas->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para Abrir Caja -->
<div class="modal fade" id="abrirCajaModal" tabindex="-1" aria-labelledby="abrirCajaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="abrirCajaModalLabel"><i class="fas fa-cash-register"></i> Abrir Caja</h5>
                <button type="button" class="btn-close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('cajas.abrir') }}" id="abrirCajaForm">
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cerrarCajaModalLabel"><i class="fas fa-lock"></i> Cerrar Caja</h5>
                <button type="button" class="btn-close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('cajas.cerrar', ['id' => $cajaAbierta ? $cajaAbierta->id : 0]) }}" id="cerrarCajaForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="monto_cierre">Monto de Cierre:</label>
                        <input type="number" class="form-control" name="monto_cierre" id="monto_cierre" readonly required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Cerrar Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $('#cerrarCajaModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const ventas = button.data('ventas');
        const modal = $(this);
        modal.find('.modal-body #monto_cierre').val(ventas);
    });
</script>
@stop
