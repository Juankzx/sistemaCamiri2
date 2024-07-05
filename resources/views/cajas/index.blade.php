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
                        <tbody>
                            @forelse ($cajas as $caja)
                                <tr>
                                    <td>{{ $caja->id }}</td>
                                    <td>{{ $caja->sucursal->nombre }}</td>
                                    <td>{{ $caja->user->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                    <td>{{ number_format($caja->monto_apertura, 0) }}</td>
                                    <td>{{ $caja->monto_cierre ? number_format($caja->monto_cierre, 0) : 'N/A' }}</td>
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
    <script>
        $('#cerrarCajaModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var monto = button.data('monto');
        var modal = $(this);
        modal.find('form').attr('action', '/cajas/cerrar/' + id);
        modal.find('#monto_cierre').val(monto);
    });
    </script>
@stop
