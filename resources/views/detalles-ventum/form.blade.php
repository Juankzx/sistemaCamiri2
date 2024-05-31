<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="venta_id" class="form-label">{{ __('Venta Id') }}</label>
            <input type="text" name="venta_id" class="form-control @error('venta_id') is-invalid @enderror" value="{{ old('venta_id', $detallesVentum?->venta_id) }}" id="venta_id" placeholder="Venta Id">
            {!! $errors->first('venta_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="producto_id" class="form-label">{{ __('Producto Id') }}</label>
            <input type="text" name="producto_id" class="form-control @error('producto_id') is-invalid @enderror" value="{{ old('producto_id', $detallesVentum?->producto_id) }}" id="producto_id" placeholder="Producto Id">
            {!! $errors->first('producto_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="iva_id" class="form-label">{{ __('Iva Id') }}</label>
            <input type="text" name="iva_id" class="form-control @error('iva_id') is-invalid @enderror" value="{{ old('iva_id', $detallesVentum?->iva_id) }}" id="iva_id" placeholder="Iva Id">
            {!! $errors->first('iva_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="cantidad" class="form-label">{{ __('Cantidad') }}</label>
            <input type="text" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" value="{{ old('cantidad', $detallesVentum?->cantidad) }}" id="cantidad" placeholder="Cantidad">
            {!! $errors->first('cantidad', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="precio_unitario" class="form-label">{{ __('Precio Unitario') }}</label>
            <input type="text" name="precio_unitario" class="form-control @error('precio_unitario') is-invalid @enderror" value="{{ old('precio_unitario', $detallesVentum?->precio_unitario) }}" id="precio_unitario" placeholder="Precio Unitario">
            {!! $errors->first('precio_unitario', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>