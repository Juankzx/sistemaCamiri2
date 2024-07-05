<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="sucursal_id" class="form-label">{{ __('Sucursal Id') }}</label>
            <input type="text" name="sucursal_id" class="form-control @error('sucursal_id') is-invalid @enderror" value="{{ old('sucursal_id', $caja?->sucursal_id) }}" id="sucursal_id" placeholder="Sucursal Id">
            {!! $errors->first('sucursal_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="user_id" class="form-label">{{ __('User Id') }}</label>
            <input type="text" name="user_id" class="form-control @error('user_id') is-invalid @enderror" value="{{ old('user_id', $caja?->user_id) }}" id="user_id" placeholder="User Id">
            {!! $errors->first('user_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="fecha_apertura" class="form-label">{{ __('Fecha Apertura') }}</label>
            <input type="text" name="fecha_apertura" class="form-control @error('fecha_apertura') is-invalid @enderror" value="{{ old('fecha_apertura', $caja?->fecha_apertura) }}" id="fecha_apertura" placeholder="Fecha Apertura">
            {!! $errors->first('fecha_apertura', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="fecha_cierre" class="form-label">{{ __('Fecha Cierre') }}</label>
            <input type="text" name="fecha_cierre" class="form-control @error('fecha_cierre') is-invalid @enderror" value="{{ old('fecha_cierre', $caja?->fecha_cierre) }}" id="fecha_cierre" placeholder="Fecha Cierre">
            {!! $errors->first('fecha_cierre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="monto_apertura" class="form-label">{{ __('Monto Apertura') }}</label>
            <input type="text" name="monto_apertura" class="form-control @error('monto_apertura') is-invalid @enderror" value="{{ old('monto_apertura', $caja?->monto_apertura) }}" id="monto_apertura" placeholder="Monto Apertura">
            {!! $errors->first('monto_apertura', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="monto_cierre" class="form-label">{{ __('Monto Cierre') }}</label>
            <input type="text" name="monto_cierre" class="form-control @error('monto_cierre') is-invalid @enderror" value="{{ old('monto_cierre', $caja?->monto_cierre) }}" id="monto_cierre" placeholder="Monto Cierre">
            {!! $errors->first('monto_cierre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="estado" class="form-label">{{ __('Estado') }}</label>
            <input type="text" name="estado" class="form-control @error('estado') is-invalid @enderror" value="{{ old('estado', $caja?->estado) }}" id="estado" placeholder="Estado">
            {!! $errors->first('estado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>