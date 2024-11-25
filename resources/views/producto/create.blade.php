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
                                            <option value="" selected>{{ __('Seleccionar...') }}</option>
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
        <option value="" selected>{{ __('Seleccione una categoría') }}</option>
        @foreach($categorias as $categoria)
            <option value="{{ $categoria->id }}" data-sin-stock="{{ $categoria->sin_stock }}"
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
                                            <option value="" selected>{{ __('Seleccionar...') }}</option>
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
        // Inicializa Select2
        $('.select2').select2({
            placeholder: "Seleccione..", // Texto de ayuda
            allowClear: true, // Permite limpiar la selección
            width: '100%' // Ajusta el ancho para que sea consistente con otros inputs
        });

        // Ocultar o mostrar el campo Proveedor según la categoría seleccionada
        $('#categoria_id').change(function() {
            let selectedOption = $(this).find(':selected');
            let sinStock = selectedOption.data('sin-stock');
            if (sinStock == 1) {
                $('#proveedor_id').closest('.form-group').hide();
            } else {
                $('#proveedor_id').closest('.form-group').show();
            }
        });

        // Ejecutar al cargar la página
        $('#categoria_id').trigger('change');
    });
</script>
@endsection
<style>
    .select2-container {
        width: 100% !important; /* Asegura que el ancho sea igual al de los inputs */
    }

    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px); /* Ajusta la altura para que coincida con los inputs */
        padding: 0.375rem 0.75rem; /* Margen interno similar a los inputs */
        border: 1px solid #ced4da; /* Borde igual a los inputs */
        border-radius: 0.25rem; /* Redondeo igual a los inputs */
        font-size: 1rem; /* Tamaño de fuente consistente */
        line-height: 1.5; /* Altura de línea consistente */
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        padding: 0; /* Asegura que el texto no tenga padding adicional */
        line-height: calc(2.25rem); /* Centra verticalmente el texto */
    }

    .select2-container .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem); /* Alinea el ícono con la altura */
    }

    .select2-container .select2-selection--single .select2-selection__arrow b {
        margin-top: 0.75rem; /* Centra el ícono verticalmente */
    }

    .form-group {
        margin-bottom: 1rem; /* Asegura un margen uniforme entre los campos */
    }
</style>