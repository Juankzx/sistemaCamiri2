@extends('adminlte::page')

@section('template_title')
    {{ $proveedore->nombre ?? __('Detalles del Proveedor') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white position-relative">
                        <h5 class="card-title mb-0 text-center">{{ __('Detalles del Proveedor') }}</h5>
                        <a class="btn btn-outline-light btn-sm position-absolute" href="{{ route('proveedores.index') }}"
                            style="top: 10px; right: 10px; opacity: 0.8;">
                            <i class="fas fa-arrow-left"></i> {{ __('Volver') }}
                        </a>
                    </div>

                    <div class="card-body bg-light">
                        <table class="table table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <th class="bg-primary text-white" style="width: 30%;">{{ __('Nombre') }}</th>
                                    <td>{{ $proveedore->nombre }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-primary text-white">{{ __('Razón Social') }}</th>
                                    <td>{{ $proveedore->razon_social ?? __('No especificada') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-primary text-white">{{ __('Dirección') }}</th>
                                    <td>{{ $proveedore->direccion ?? __('No especificada') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-primary text-white">{{ __('Teléfono') }}</th>
                                    <td>{{ $proveedore->telefono ?? __('No especificado') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-primary text-white">{{ __('Email') }}</th>
                                    <td>{{ $proveedore->email ?? __('No especificado') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-primary text-white text-center">
                        <small>{{ __('Última actualización:') }} {{ $proveedore->updated_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
