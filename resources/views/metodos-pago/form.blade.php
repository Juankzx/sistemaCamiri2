<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $metodosPago?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2 d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
        <!-- Botón Volver -->
        <a href="{{ route('metodos-pagos.index') }}" class="btn btn-secondary">{{ __('Volver') }}</a>
    </div>
</div>
