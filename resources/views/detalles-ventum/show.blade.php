@extends('adminlte::page')

@section('template_title')
    {{ $detallesVentum->name ?? __('Show') . " " . __('Detalles Ventum') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Detalles Ventum</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('detalles-venta.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Venta Id:</strong>
                                    {{ $detallesVentum->venta_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Producto Id:</strong>
                                    {{ $detallesVentum->producto_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Iva Id:</strong>
                                    {{ $detallesVentum->iva_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cantidad:</strong>
                                    {{ $detallesVentum->cantidad }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Precio Unitario:</strong>
                                    {{ $detallesVentum->precio_unitario }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
