@extends('layouts.app')

@section('template_title')
    {{ $pedido->name ?? __('Show') . " " . __('Pedido') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Pedido</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('pedidos.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Proveedor Id:</strong>
                                    {{ $pedido->proveedor_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Pedido:</strong>
                                    {{ $pedido->fecha_pedido }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Total:</strong>
                                    {{ $pedido->total }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado:</strong>
                                    {{ $pedido->estado }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
