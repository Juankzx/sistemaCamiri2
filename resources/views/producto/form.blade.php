<form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row padding-1 p-1">
        <div class="col-md-12">
            
            <div class="form-group mb-2 mb20">
                <label for="codigo_barra" class="form-label">{{ __('Código de Barras') }}</label>
                <input type="text" name="codigo_barra" class="form-control @error('codigo_barra') is-invalid @enderror" value="{{ old('codigo_barra', $producto ? $producto->codigo_barra : '') }}" id="codigo_barra" placeholder="Código de Barras">
                @error('codigo_barra')
                    <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                @enderror
            </div>    

            <div class="form-group mb-2 mb20">
                <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $producto?->nombre) }}" id="nombre" placeholder="Nombre">
                {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>    

            <div class="form-group mb-2 mb20">
                <label for="unidadmedida_id" class="form-label">{{ __('Unidad de Medida') }}</label>
                <select name="unidadmedida_id" class="form-control @error('unidadmedida_id') is-invalid @enderror" id="unidadmedida_id">
                    <option value="">{{ __('Seleccionar unidad de medida') }}</option>
                    @foreach($unidadMedida as $unidad)
                        <option value="{{ $unidad->id }}" {{ old('unidadmedida_id', $producto?->unidadmedida_id) == $unidad->id ? 'selected' : '' }}>
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('unidadmedida_id')
                    <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                @enderror
            </div>

            <div class="form-group mb-2 mb20">
                <label for="imagen" class="form-label">{{ __('Imagen') }}</label>
                <input type="file" name="imagen" class="form-control @error('imagen') is-invalid @enderror" id="imagen">
                {!! $errors->first('imagen', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>
            
            <div class="form-group mb-2 mb20">
                <label for="precio_compra" class="form-label">{{ __('Precio de Compra') }}</label>
                <input type="text" name="preciocompra" class="form-control @error('preciocompra') is-invalid @enderror" value="{{ old('preciocompra', $producto->preciocompra ?? '') }}" id="precio_compra" placeholder="Precio de Compra">
                {!! $errors->first('preciocompra', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>

            <div class="form-group mb-2 mb20">
                <label for="precio_venta" class="form-label">{{ __('Precio de Venta') }}</label>
                <input type="text" name="precioventa" class="form-control @error('precioventa') is-invalid @enderror" value="{{ old('precioventa', $producto->precioventa ?? '') }}" id="precio_venta" placeholder="Precio de Venta">
                {!! $errors->first('precioventa', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>

            <div class="form-group mb-2 mb20">
                <label for="categoria_id" class="form-label">{{ __('Categoria') }}</label>
                <select name="categoria_id" class="form-control @error('categoria_id') is-invalid @enderror" id="categoria_id">
                    <option value="">{{ __('Seleccionar categoría') }}</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ old('categoria_id', $producto?->categoria_id) == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('categoria_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>

            <div class="form-group mb-2 mb20">
                <label for="proveedor_id" class="form-label">{{ __('Proveedor') }}</label>
                <select name="proveedor_id" class="form-control @error('proveedor_id') is-invalid @enderror" id="proveedor_id">
                    <option value="">{{ __('Seleccionar proveedor') }}</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $producto?->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                            {{ $proveedor->nombre }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('proveedor_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>
        <!-- Botones de acción -->
        <div class="col-md-12 mt20 mt-2">
            <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
            <!-- Botón Volver -->
            <a href="{{ route('productos.index') }}" class="btn btn-secondary ml-2">{{ __('Volver') }}</a>
        </div>
    </div>
</form>
