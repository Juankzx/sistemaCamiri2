@extends('adminlte::page')

@section('template_title')
    {{ __('Actualizar Producto') }}
@endsection

@section('content')
<div class="container">
    <h1>Editar Producto</h1>
    <form action="{{ route('productos.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="codigo_barra">Código de Barra</label>
            <input type="text" name="codigo_barra" class="form-control" value="{{ $producto->codigo_barra }}" required>
        </div>
        
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="{{ $producto->nombre }}" required>
        </div>

        <!-- Campo Unidad de Medida -->
        <div class="form-group">
            <label for="unidadmedida_id" class="form-label">{{ __('Unidad de Medida') }}</label>
            <select name="unidadmedida_id" class="form-control @error('unidadmedida_id') is-invalid @enderror" id="unidadmedida_id" required>
                <option value="">{{ __('Seleccionar unidad de medida') }}</option>
                @foreach($unidadMedida as $unidad)
                    <option value="{{ $unidad->id }}" {{ old('unidadmedida_id', $producto->unidadmedida_id) == $unidad->id ? 'selected' : '' }}>
                        {{ $unidad->nombre }}
                    </option>
                @endforeach
            </select>
            @error('unidadmedida_id')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>

        <div class="form-group">
            <label for="imagen">Imagen</label>
            <input type="file" name="imagen" class="form-control-file">
            @if($producto->imagen)
                <img src="{{ Storage::url($producto->imagen) }}" alt="{{ $producto->nombre }}" width="100" class="mt-2">
            @endif
        </div>
        
        <div class="form-group">
            <label for="preciocompra">Precio de Compra</label>
            <input type="text" name="preciocompra" class="form-control" value="{{ $producto->preciocompra }}" required>
        </div>
        
        <div class="form-group">
            <label for="precioventa">Precio de Venta</label>
            <input type="text" name="precioventa" class="form-control" value="{{ $producto->precioventa }}" required>
        </div>
        
        <div class="form-group">
            <label for="categoria_id">Categoría</label>
            <select name="categoria_id" class="form-control" required>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="proveedor_id">Proveedor</label>
            <select name="proveedor_id" class="form-control" required>
                @foreach($proveedores as $proveedor)
                    <option value="{{ $proveedor->id }}" {{ $producto->proveedor_id == $proveedor->id ? 'selected' : '' }}>{{ $proveedor->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex justify-content-start">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <!-- Botón Volver -->
            <a href="{{ route('productos.index') }}" class="btn btn-secondary ml-2">Volver</a>
        </div>
    </form>
</div>
@endsection
