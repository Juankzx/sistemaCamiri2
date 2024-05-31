@extends('layouts.app')

@section('template_title')
    {{ $inventario->name ?? __('Show') . " " . __('Inventario') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Inventario</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('inventarios.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Producto Id:</strong>
                                    {{ $inventario->producto_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cantidad:</strong>
                                    {{ $inventario->cantidad }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipo Movimiento:</strong>
                                    {{ $inventario->tipo_movimiento }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha:</strong>
                                    {{ $inventario->fecha }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
