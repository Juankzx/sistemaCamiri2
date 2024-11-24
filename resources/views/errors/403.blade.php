@extends('adminlte::page')

@section('title', '403 - Acceso No Autorizado')

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="text-center">
            <h1 class="display-1 text-danger font-weight-bold">403</h1>
            <h2 class="text-muted">Acceso No Autorizado</h2>
            <p class="lead">
                <i class="fas fa-exclamation-triangle text-danger"></i> Oops! No tienes permiso para acceder a esta página.
            </p>
            <p class="mb-4">
                Lo sentimos, pero no tienes los permisos necesarios para ver esta sección. Si necesitas acceso, contacta con el administrador.
            </p>
            <a href="{{ url('/home') }}" class="btn btn-primary">
                <i class="fas fa-home"></i> Volver al Dashboard
            </a>
        </div>
    </div>
@endsection
