@extends('layouts.app')

@section('template_title')
    {{ $caja->name ?? __('Show') . " " . __('Caja') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Caja</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('cajas.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Sucursal Id:</strong>
                                    {{ $caja->sucursal_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>User Id:</strong>
                                    {{ $caja->user_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Apertura:</strong>
                                    {{ $caja->fecha_apertura }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Cierre:</strong>
                                    {{ $caja->fecha_cierre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Monto Apertura:</strong>
                                    {{ $caja->monto_apertura }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Monto Cierre:</strong>
                                    {{ $caja->monto_cierre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado:</strong>
                                    {{ $caja->estado }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
