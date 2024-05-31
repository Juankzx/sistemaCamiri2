@extends('layouts.app')

@section('template_title')
    {{ $pagosProveedor->name ?? __('Show') . " " . __('Pagos Proveedor') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Pagos Proveedor</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('pagos-proveedors.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Pedido Id:</strong>
                                    {{ $pagosProveedor->pedido_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Monto:</strong>
                                    {{ $pagosProveedor->monto }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Pago:</strong>
                                    {{ $pagosProveedor->fecha_pago }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Referencia Pago:</strong>
                                    {{ $pagosProveedor->referencia_pago }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Numero Factura:</strong>
                                    {{ $pagosProveedor->numero_factura }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado:</strong>
                                    {{ $pagosProveedor->estado }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
