@extends('adminlte::page')

@section('title', 'Editar Guía de Despacho')

@section('content_header')
    <h1>Editar Guía de Despacho</h1>
@stop

@section('content')
<div class="container">
    <form action="{{ route('guias-despacho.update', $guiaDespacho->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h4>Editar Guía de Despacho N° {{ $guiaDespacho->id }}</h4>
    </div>
    <div class="card-body">
        <!-- Bloquear edición si el estado es entregado -->
        @if($guiaDespacho->estado === 'entregada')
            <div class="alert alert-danger">
                Esta guía de despacho ya ha sido entregada y no se puede editar.
            </div>
        @else
            <!-- Campos editables -->
            <div class="form-group">
                <label for="fecha_entrega">Fecha de Entrega</label>
                <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega"
                    value="{{ $guiaDespacho->fecha_entrega }}" required>
            </div>


            <h5>Detalles de los Productos</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad Solicitada</th>
                        <th>Cantidad Entregada</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guiaDespacho->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre }}</td>
                            <td>{{ $detalle->cantidad_solicitada }}</td>
                            <td>
                                <input type="number" class="form-control" name="detalles[{{ $detalle->id }}][cantidad_entregada]"
                                    value="{{ $detalle->cantidad_entregada }}" min="0" max="{{ $detalle->cantidad_solicitada }}" required>
                            </td>
                            <td>${{ number_format($detalle->precio_compra, 0) }}</td>
                            <td>${{ number_format($detalle->cantidad_entregada * $detalle->precio_compra, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card-footer">
        <a href="{{ route('guias-despacho.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        @if($guiaDespacho->estado !== 'entregada')
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        @endif
    </div>
</div>

    </form>
</div>
@stop

@section('css')
<style>
    .card {
        transition: all 0.3s ease-in-out;
    }
</style>
@stop
