@extends('adminlte::page')

@section('title', 'Editar Factura')

@section('content_header')
    <h1>Editar Factura N° {{ $factura->numero_factura }}</h1>
@stop

@section('content')
<div class="container">
    <!-- Mostrar errores de validación -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Editar Factura</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('facturas.update', $factura->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="numero_factura">Número de Factura</label>
                    <input type="text" name="numero_factura" id="numero_factura" class="form-control" value="{{ old('numero_factura', $factura->numero_factura) }}" required>
                </div>
                
                <div class="form-group">
                <label for="fecha_emision">Fecha</label>
                <input type="date" name="fecha_emision" id="fecha_emision" class="form-control" value="{{ $factura->fecha_emision ? \Carbon\Carbon::parse($factura->fecha_emision)->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d') }}">
                </div>

                <div class="form-group">
                <label for="monto_total">Monto Total</label>
                <input type="number" name="monto_total" id="monto_total" class="form-control" 
                    value="{{ $factura->monto_total }}" 
                    {{ $puedeEditarTotal ? '' : 'readonly' }}>
                @if(!$puedeEditarTotal)
                    <small class="text-muted">No se puede editar el monto total porque la factura está asociada a una guía de despacho.</small>
                @endif
            </div>


                <div class="text-right">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Cambios</button>
                    <a href="{{ route('facturas.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card {
        margin-top: 20px;
    }
    .card-header {
        background-color: #007bff;
        color: white;
    }
</style>
@stop
