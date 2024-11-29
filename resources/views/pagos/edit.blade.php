@extends('adminlte::page')

@section('title', 'Editar Pago')

@section('content')
<div class="container">
    <h1>Editar Pago</h1>
    
    @if (session('mensaje'))
        <div class="alert alert-warning">
            {{ session('mensaje') }}
        </div>
    @endif

    <form action="{{ route('pagos.update', $pago->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Método de Pago -->
        <div class="form-group">
            <label for="metodo_pago_id">Método de Pago</label>
            <select name="metodo_pago_id" id="metodo_pago_id" class="form-control" required>
                <option value="" disabled {{ is_null($pago->metodo_pago_id) ? 'selected' : '' }}>Seleccione un método</option>
                @foreach ($metodoPago as $metodo)
                    <option value="{{ $metodo->id }}" {{ $pago->metodo_pago_id == $metodo->id ? 'selected' : '' }}>
                        {{ $metodo->nombre }}
                    </option>
                @endforeach
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
                value="{{ old('fecha_pago', $pago->fecha_pago ? \Carbon\Carbon::parse($pago->fecha_pago)->format('Y-m-d') : '') }}" 
                required>
        </div>

        <!-- Monto -->
        <div class="form-group">
            <label for="formatted_monto">Monto</label>
            <!-- Mostrar el monto formateado al usuario -->
            <input 
                type="text" 
                id="formatted_monto" 
                class="form-control" 
                value="{{ $formattedMonto }}" 
                readonly>
            <!-- Campo oculto para enviar el valor numérico real -->
            <input 
                type="hidden" 
                name="monto" 
                id="monto" 
                value="{{ $pago->monto }}">
        </div>

        <!-- Número de Transferencia (si aplica) -->
        <div class="form-group" id="transferencia-group" style="display: {{ $pago->metodo_pago_id == 2 ? 'block' : 'none' }}">
            <label for="numero_transferencia">Número de Transferencia</label>
            <input 
                type="text" 
                name="numero_transferencia" 
                id="numero_transferencia" 
                class="form-control" 
                value="{{ old('numero_transferencia', $pago->numero_transferencia) }}">
        </div>

        <!-- Descripción -->
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea 
                name="descripcion" 
                id="descripcion" 
                class="form-control" 
                rows="3" 
                {{ is_null($pago->factura_id) ? 'required' : '' }}>{{ old('descripcion', $pago->descripcion) }}</textarea>
        </div>

        <!-- Botones -->
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

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const metodoPagoSelect = document.getElementById('metodo_pago_id');
        const transferenciaGroup = document.getElementById('transferencia-group');
        const numeroTransferencia = document.getElementById('numero_transferencia');

        metodoPagoSelect.addEventListener('change', function () {
            if (this.value == 2) { // Transferencia
                transferenciaGroup.style.display = 'block';
                numeroTransferencia.required = true;
            } else {
                transferenciaGroup.style.display = 'none';
                numeroTransferencia.required = false;
            }
        });

        // Inicialización al cargar la página
        if (metodoPagoSelect.value == 2) {
            transferenciaGroup.style.display = 'block';
            numeroTransferencia.required = true;
        } else {
            transferenciaGroup.style.display = 'none';
            numeroTransferencia.required = false;
        }
    });
</script>
@stop

@section('css')
<style>
    .form-group {
        margin-bottom: 1.5rem;
    }
</style>
@stop
