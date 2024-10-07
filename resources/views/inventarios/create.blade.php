@extends('adminlte::page')

@section('template_title')
    {{ __('Crear') }} Inventario
@endsection

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
                    @php
                        $isAgregado = in_array($producto->id, $productosInventariados); // Verificar si el producto ya está en inventario
                    @endphp
                    <option value="{{ $producto->id }}" 
                            style="{{ $isAgregado ? 'color: green; font-weight: bold;' : '' }}"
                            class="{{ $isAgregado ? 'bg-light' : '' }}">
                        {{ $producto->nombre }} - {{ $producto->categoria->nombre ?? 'Sin categoría' }} - {{ $producto->codigo_barra }} 
                        @if($isAgregado)
                            (Agregado)
                        @else
                            (No Agregado)
                        @endif
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
            <input type="number" class="form-control" id="cantidad" name="cantidad" value="{{ old('cantidad', 1) }}" required>
        </div>
        <div class="form-group">
            <label for="stock_minimo">Stock Mínimo</label>
            <input type="number" name="stock_minimo" class="form-control" value="{{ old('stock_minimo', 0) }}" required>
        </div>
        <div class="form-group">
            <label for="stock_critico">Stock Crítico</label>
            <input type="number" name="stock_critico" class="form-control" value="{{ old('stock_critico', 0) }}" required>
        </div>
        
        <!-- Botones de acción -->
        <div class="form-group d-flex justify-content-between">
            <!-- Botón Guardar -->
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Guardar
            </button>
            
            <!-- Botón Volver -->
            <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Volver
            </a>
        </div>
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
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    .option-added {
        background-color: #d4edda !important; /* Verde claro */
        color: #155724 !important;
        font-weight: bold;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
        });
    @endif

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
});
</script>
@stop
