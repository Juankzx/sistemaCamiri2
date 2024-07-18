<div class="row padding-1 p-1">
    <div class="col-md-12">
        
    <div class="form-group mb-2 mb20">
            <label for="producto_id" class="form-label">{{ __('Producto') }}</label>
            <select name="producto_id" class="form-control @error('producto_id') is-invalid @enderror" id="producto_id">
                <option value="" disabled {{ old('producto_id', $inventario?->producto_id) ? '' : 'selected' }}>Seleccionar producto</option>
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}" {{ old('producto_id', $inventario?->producto_id) == $producto->id ? 'selected' : '' }}>
                        {{ $producto->nombre }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('producto_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="sucursal_id" class="form-label">{{ __('Sucursal') }}</label>
            <select name="sucursal_id" class="form-control @error('sucursal_id') is-invalid @enderror" id="sucursal_id">
                <option value="" disabled {{ old('sucursal_id', $inventario?->sucursal_id) ? '' : 'selected' }}>Seleccionar sucursal</option>
                @foreach($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}" {{ old('sucursal_id', $inventario?->sucursal_id) == $sucursal->id ? 'selected' : '' }}>
                        {{ $sucursal->nombre }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('sucursal_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="bodega_id" class="form-label">{{ __('Bodega') }}</label>
            <select name="bodega_id" class="form-control @error('bodega_id') is-invalid @enderror" id="bodega_id">
                <option value="" disabled {{ old('bodega_id', $inventario?->bodega_id) ? '' : 'selected' }}>Seleccionar bodega</option>
                @foreach($bodegas as $bodega)
                    <option value="{{ $bodega->id }}" {{ old('bodega_id', $inventario?->bodega_id) == $bodega->id ? 'selected' : '' }}>
                        {{ $bodega->nombre }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('bodega_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="cantidad" class="form-label">{{ __('Cantidad') }}</label>
            <input type="text" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" value="{{ old('cantidad', $inventario?->cantidad) }}" id="cantidad" placeholder="Cantidad">
            {!! $errors->first('cantidad', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>