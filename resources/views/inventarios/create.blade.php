@extends('adminlte::page')

@section('template_title')
    {{ __('Crear') }} Inventario
@endsection

@if(session('error'))
    <script>
        window.onload = function() {
            alert('{{ session('error') }}');
        };
    </script>
@endif

@section('content')
<div class="container">
    <h1>Agregar Inventario</h1>
    <form action="{{ route('inventarios.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="producto_id">Producto</label>
            <select class="form-control" id="producto_id" name="producto_id" required>
                <option value="" disabled selected>Seleccione un Producto</option>    
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}">
                        {{ $producto->nombre }} - {{ $producto->categoria->nombre ?? 'Sin categoría' }} - {{ $producto->codigo_barra }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="bodega_id">Bodega</label>
            <select class="form-control" id="bodega_id" name="bodega_id" required>
                <option value="" disabled selected>Seleccione una Bodega</option>    
                @foreach($bodegas as $bodega)
                    <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="cantidad">Cantidad</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection

@section('css')
<style>
    .form-group {
        margin-bottom: 15px;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
</style>
@endsection
