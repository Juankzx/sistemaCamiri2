@extends('adminlte::page')

@section('template_title')
    {{ __('Agregar ') }} Producto
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Agregar Producto') }}</span>
                    </div>
                    <div class="card-body bg-white">
                    <form method="POST" action="{{ route('productos.store') }}" role="form" enctype="multipart/form-data">
    @csrf
    <div class="row padding-1 p-1">
        <div class="col-md-12">

            <!-- Código de Barras -->
            <div class="form-group mb-2 mb20">
                <label for="codigo_barra" class="form-label">{{ __('Código de Barras') }}</label>
                <input type="text" name="codigo_barra" class="form-control @error('codigo_barra') is-invalid @enderror" 
                       value="{{ old('codigo_barra', $producto ? $producto->codigo_barra : '') }}" id="codigo_barra" 
                       placeholder="Código de Barras">
                @error('codigo_barra')
                    <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <!-- Nombre -->
            <div class="form-group mb-2 mb20">
                <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                       value="{{ old('nombre', $producto?->nombre) }}" id="nombre" placeholder="Nombre">
                {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>

            <!-- Unidad de Medida -->
            <div class="form-group mb-2 mb20">
                <label for="unidadmedida_id" class="form-label">{{ __('Unidad de Medida') }}</label>
                <select name="unidadmedida_id" class="form-control select2 @error('unidadmedida_id') is-invalid @enderror text-center" 
                        id="unidadmedida_id" style="text-align: center;">
                    <option value="" selected disabled>{{ __('Seleccionar...') }}</option>
                    @foreach($unidadMedida as $unidad)
                        <option value="{{ $unidad->id }}" 
                                {{ old('unidadmedida_id', $producto?->unidadmedida_id) == $unidad->id ? 'selected' : '' }}>
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('unidadmedida_id')
                    <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <!-- Imagen -->
            <div class="form-group mb-2 mb20">
                <label for="imagen" class="form-label">{{ __('Imagen') }}</label>
                <input type="file" name="imagen" class="form-control @error('imagen') is-invalid @enderror" id="imagen">
                {!! $errors->first('imagen', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>

            <!-- Precio de Compra -->
            <div class="form-group mb-2 mb20">
                <label for="precio_compra" class="form-label">{{ __('Precio de Compra') }}</label>
                <input type="text" name="preciocompra" class="form-control @error('preciocompra') is-invalid @enderror" 
                       value="{{ old('preciocompra', $producto->preciocompra ?? '') }}" id="precio_compra" 
                       placeholder="Precio de Compra">
                {!! $errors->first('preciocompra', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>

            <!-- Precio de Venta -->
            <div class="form-group mb-2 mb20">
                <label for="precio_venta" class="form-label">{{ __('Precio de Venta') }}</label>
                <input type="text" name="precioventa" class="form-control @error('precioventa') is-invalid @enderror" 
                       value="{{ old('precioventa', $producto->precioventa ?? '') }}" id="precio_venta" 
                       placeholder="Precio de Venta">
                {!! $errors->first('precioventa', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>

            <!-- Categoría -->
            <div class="form-group mb-2 mb20">
                <label for="categoria_id" class="form-label">{{ __('Categoría') }}</label>
                <select name="categoria_id" class="form-control select2 @error('categoria_id') is-invalid @enderror text-center" 
                        id="categoria_id" style="text-align: center;">
                    <option value="" selected disabled>{{ __('Seleccionar...') }}</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" 
                                {{ old('categoria_id', $producto?->categoria_id) == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('categoria_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>

            <!-- Proveedor -->
            <div class="form-group mb-2 mb20">
                <label for="proveedor_id" class="form-label">{{ __('Proveedor') }}</label>
                <select name="proveedor_id" class="form-control select2 @error('proveedor_id') is-invalid @enderror text-center" 
                        id="proveedor_id" style="text-align: center;">
                    <option value="" selected disabled>{{ __('Seleccionar...') }}</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}" 
                                {{ old('proveedor_id', $producto?->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                            {{ $proveedor->nombre }} - {{ $proveedor->rut }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('proveedor_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>

            <!-- Botones -->
            <div class="col-md-12 mt20 mt-2">
                <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary ml-2">{{ __('Volver') }}</a>
            </div>
        </div>
    </div>
</form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Seleccionar...",
            allowClear: true,
            width: '100%' // Ajusta el ancho
        });
    });
</script>
<script>
    // Mostrar mensajes de error usando SweetAlert
    @if ($errors->any())
        let errorMessages = '';
        @foreach ($errors->all() as $error)
            errorMessages += '{{ $error }}<br>';
        @endforeach

        Swal.fire({
            icon: 'error',
            title: 'Errores de validación',
            html: errorMessages, // Mostrar todos los mensajes de error en HTML
            showConfirmButton: true
        });
    @endif

    // Mostrar mensajes de éxito cuando se crea un producto
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Producto creado exitosamente!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    // Mostrar mensajes de error general (no solo validación)
    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: '{{ session('error') }}',
            showConfirmButton: true
        });
    @endif
</script>
@endsection
