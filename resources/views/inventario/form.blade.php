<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="producto_id" class="form-label">{{ __('Producto Id') }}</label>
            <input type="text" name="producto_id" class="form-control @error('producto_id') is-invalid @enderror" value="{{ old('producto_id', $inventario?->producto_id) }}" id="producto_id" placeholder="Producto Id">
            {!! $errors->first('producto_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="cantidad" class="form-label">{{ __('Cantidad') }}</label>
            <input type="text" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" value="{{ old('cantidad', $inventario?->cantidad) }}" id="cantidad" placeholder="Cantidad">
            {!! $errors->first('cantidad', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tipo_movimiento" class="form-label">{{ __('Tipo Movimiento') }}</label>
            <input type="text" name="tipo_movimiento" class="form-control @error('tipo_movimiento') is-invalid @enderror" value="{{ old('tipo_movimiento', $inventario?->tipo_movimiento) }}" id="tipo_movimiento" placeholder="Tipo Movimiento">
            {!! $errors->first('tipo_movimiento', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="fecha" class="form-label">{{ __('Fecha') }}</label>
            <input type="text" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', $inventario?->fecha) }}" id="fecha" placeholder="Fecha">
            {!! $errors->first('fecha', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>