@extends('adminlte::page')

@section('title', 'Editar Configuración')

@section('content_header')
    <h1>Editar Configuración</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('configuracion.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre_sistema">Nombre del Sistema:</label>
            <input type="text" name="nombre_sistema" class="form-control" value="{{ $configuracion->nombre_sistema }}" required>
        </div>

        <div class="form-group">
            <label for="logo_sistema">Logo del Sistema:</label>
            <input type="file" name="logo_sistema" class="form-control">
            @if ($configuracion->logo_sistema)
                <img src="{{ asset('storage/' . $configuracion->logo_sistema) }}" alt="Logo actual" width="100">
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
@stop
