@extends('adminlte::page')

@section('title', 'Configuración del Sistema')

@section('content_header')
    <h1>Configuración del Sistema</h1>
@stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Detalles de Configuración</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nombre del Sistema:</label>
                        <p>{{ $configuracion->nombre_sistema }}</p>
                    </div>
                    <div class="form-group">
                        <label>Logo del Sistema:</label>
                        <p><img src="{{ asset($configuracion->logo) }}" alt="Logo" width="100"></p>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('configuracion.edit', $configuracion->id) }}" class="btn btn-warning">Editar Configuración</a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
