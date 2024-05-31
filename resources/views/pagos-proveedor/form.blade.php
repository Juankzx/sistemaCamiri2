<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="pedido_id" class="form-label">{{ __('Pedido Id') }}</label>
            <input type="text" name="pedido_id" class="form-control @error('pedido_id') is-invalid @enderror" value="{{ old('pedido_id', $pagosProveedor?->pedido_id) }}" id="pedido_id" placeholder="Pedido Id">
            {!! $errors->first('pedido_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="monto" class="form-label">{{ __('Monto') }}</label>
            <input type="text" name="monto" class="form-control @error('monto') is-invalid @enderror" value="{{ old('monto', $pagosProveedor?->monto) }}" id="monto" placeholder="Monto">
            {!! $errors->first('monto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="fecha_pago" class="form-label">{{ __('Fecha Pago') }}</label>
            <input type="text" name="fecha_pago" class="form-control @error('fecha_pago') is-invalid @enderror" value="{{ old('fecha_pago', $pagosProveedor?->fecha_pago) }}" id="fecha_pago" placeholder="Fecha Pago">
            {!! $errors->first('fecha_pago', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="referencia_pago" class="form-label">{{ __('Referencia Pago') }}</label>
            <input type="text" name="referencia_pago" class="form-control @error('referencia_pago') is-invalid @enderror" value="{{ old('referencia_pago', $pagosProveedor?->referencia_pago) }}" id="referencia_pago" placeholder="Referencia Pago">
            {!! $errors->first('referencia_pago', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="numero_factura" class="form-label">{{ __('Numero Factura') }}</label>
            <input type="text" name="numero_factura" class="form-control @error('numero_factura') is-invalid @enderror" value="{{ old('numero_factura', $pagosProveedor?->numero_factura) }}" id="numero_factura" placeholder="Numero Factura">
            {!! $errors->first('numero_factura', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="estado" class="form-label">{{ __('Estado') }}</label>
            <input type="text" name="estado" class="form-control @error('estado') is-invalid @enderror" value="{{ old('estado', $pagosProveedor?->estado) }}" id="estado" placeholder="Estado">
            {!! $errors->first('estado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>