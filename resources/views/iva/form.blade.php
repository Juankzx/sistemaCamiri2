<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="porcentaje" class="form-label">{{ __('Porcentaje') }}</label>
            <input type="text" name="porcentaje" class="form-control @error('porcentaje') is-invalid @enderror" value="{{ old('porcentaje', $iva?->porcentaje) }}" id="porcentaje" placeholder="Porcentaje">
            {!! $errors->first('porcentaje', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>