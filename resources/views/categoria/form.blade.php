<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $categoria?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="descripcion" class="form-label">{{ __('Descripcion') }}</label>
            <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $categoria?->descripcion) }}" id="descripcion" placeholder="Descripcion">
            {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <!-- Campo Sin Stock -->
        <div class="form-check mb-3">
                                <input type="checkbox" name="sin_stock" id="sin_stock" value="1"
                                    class="form-check-input @error('sin_stock') is-invalid @enderror"
                                    {{ old('sin_stock') ? 'checked' : '' }}>
                                <label for="sin_stock" class="form-check-label">
                                    {{ __('Categoría Sin Stock') }}
                                </label>
                                @error('sin_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
    </div>

    <!-- Botones de Guardar y Volver -->
    <div class="col-md-12 mt20 mt-2 d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">{{ __('Volver') }}</a>
    </div>
</div>
