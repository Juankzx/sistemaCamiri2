@extends('adminlte::page')

@section('template_title')
    {{ __('Abrir') }} Caja
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Abrir') }} Caja</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('cajas.store') }}"  role="form" enctype="multipart/form-data">
                        @csrf
                            <div class="form-group">
                                <label for="monto_apertura">Monto Apertura</label>
                                <input type="text" name="monto_apertura" id="monto_apertura" class="form-control">
                            </div>
                            <div class="form-group">
                            <label for="sucursal_id" class="form-label">{{ __('Sucursal') }}</label>
                                <select name="sucursal_id" class="form-control @error('sucursal_id') is-invalid @enderror" id="sucursal_id">
                                <option value="">{{ __('Seleccionar sucursal') }}</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}" {{ old('sucursal_id', $producto->sucursal_id ?? '') == $sucursal->id ? 'selected' : '' }}>
                                            {{ $sucursal->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            {!! $errors->first('sucursal_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
