@extends('adminlte::page')

@section('title', 'Editar Pago')

@section('content')
<div class="container">
    <h1>Editar Pago</h1>
    
    @if ($mensaje)
        <div class="alert alert-warning">
            {{ $mensaje }}
        </div>
    @endif

    <form action="{{ route('pagos.update', $pago->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Método de Pago -->
        <div class="form-group">
            <label for="metodo_pago_id">Método de Pago</label>
            <select name="metodo_pago_id" id="metodo_pago_id" class="form-control" required>
                <option value="1" {{ $pago->metodo_pago_id == 1 ? 'selected' : '' }}>Efectivo</option>
                <option value="2" {{ $pago->metodo_pago_id == 2 ? 'selected' : '' }}>Transferencia</option>
                <!-- Agregar más métodos según tu lógica -->
            </select>
        </div>

        <!-- Fecha de Pago -->
        <div class="form-group">
            <label for="fecha_pago">Fecha de Pago</label>
            <input 
                type="date" 
                name="fecha_pago" 
                id="fecha_pago" 
                class="form-control" 
                value="{{ old('fecha_pago', $pago->fecha_pago ? $pago->fecha_pago->format('Y-m-d') : '') }}" 
                required>
        </div>

        <!-- Monto -->
        <div class="form-group">
            <label for="monto">Monto</label>
            <input 
                type="number" 
                name="monto" 
                id="monto" 
                class="form-control" 
                value="{{ old('monto', $pago->monto) }}" 
                min="0" 
                required>
        </div>

        <!-- Número de Transferencia (si aplica) -->
        @if ($pago->metodo_pago_id == 2)
            <div class="form-group">
                <label for="numero_transferencia">Número de Transferencia</label>
                <input 
                    type="text" 
                    name="numero_transferencia" 
                    id="numero_transferencia" 
                    class="form-control" 
                    value="{{ old('numero_transferencia', $pago->numero_transferencia) }}">
            </div>
        @endif

        <!-- Descripción (obligatoria si no hay factura asociada) -->
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea 
                name="descripcion" 
                id="descripcion" 
                class="form-control" 
                rows="3" 
                {{ is_null($pago->factura_id) ? 'required' : '' }}>{{ old('descripcion', $pago->descripcion) }}</textarea>
        </div>

        <div class="form-group text-right">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
            <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
        </div>
    </form>
</div>
@stop

@section('css')
<style>
    .form-group {
        margin-bottom: 1.5rem;
    }
</style>
@stop

