@extends('adminlte::page')

@section('title', 'Detalles del Producto')

@section('content_header')
    <h1>Detalles del Producto</h1>
@stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>{{ $producto->nombre }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="codigo_barra"><strong>Código de Barra:</strong></label>
                        <p>{{ $producto->codigo_barra }}</p>
                    </div>
                    <div class="form-group">
                        <label for="unidad_medida"><strong>Unidad de Medida:</strong></label>
                        <p>{{ $producto->unidadmedida->nombre }} - {{ $producto->unidadmedida->abreviatura }}</p>
                    </div>
                    <div class="form-group">
                        <label for="categoria"><strong>Categoría:</strong></label>
                        <p>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</p>
                    </div>
                    <div class="form-group">
                        <label for="proveedor"><strong>Proveedor:</strong></label>
                        <p>{{ $producto->proveedor->nombre ?? 'Sin proveedor' }}</p>
                    </div>
                    <div class="form-group">
                        <label for="preciocompra"><strong>Precio de Compra:</strong></label>
                        <p>${{ number_format($producto->preciocompra, 0, ',', '.') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="precioventa"><strong>Precio de Venta:</strong></label>
                        <p>${{ number_format($producto->precioventa, 0, ',', '.') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="estado"><strong>Estado:</strong></label>
                        <p>{{ $producto->estado ? 'Activo' : 'Inactivo' }}</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver</a>
                    <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h4>Imagen del Producto</h4>
                </div>
                <div class="card-body text-center">
                    @if($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="img-fluid">
                    @else
                        <img src="{{ asset('default_image_path.jpg') }}" alt="Imagen no disponible" class="img-fluid">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card-body p {
        font-size: 1rem;
        color: #555;
    }
    .card-body label {
        font-weight: bold;
        color: #333;
    }
    .img-fluid {
        max-width: 100%;
        height: auto;
    }
</style>
@endsection
