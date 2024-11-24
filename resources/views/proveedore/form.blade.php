<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $proveedore?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="rut" class="form-label">{{ __('RUT') }}</label>
            <input type="text" name="rut" class="form-control @error('rut') is-invalid @enderror" value="{{ old('rut', $proveedore?->rut) }}" id="rut" placeholder="11.111.111-1">
            {!! $errors->first('rut', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="razon_social" class="form-label">{{ __('Razón Social') }}</label>
            <input type="text" name="razon_social" class="form-control @error('razon_social') is-invalid @enderror" value="{{ old('razon_social', $proveedore?->razon_social) }}" id="razon_social" placeholder="Razón Social">
            {!! $errors->first('razon_social', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="direccion" class="form-label">{{ __('Dirección') }}</label>
            <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $proveedore?->direccion) }}" id="direccion" placeholder="Dirección">
            {!! $errors->first('direccion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
    <label for="telefono" class="form-label">{{ __('Teléfono') }}</label>
    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
           value="{{ old('telefono', $proveedore?->telefono) }}" id="telefono" 
           placeholder="Teléfono" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
    {!! $errors->first('telefono', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
</div>

        <div class="form-group mb-2 mb20">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $proveedore?->email) }}" id="email" placeholder="Correo Electrónico">
            {!! $errors->first('email', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
    </div>
    
    

    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
        <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">{{ __('Volver') }}</a>
    </div>
</div>
