@extends('adminlte::page')

@section('content')
    <div class="container">
        <h1>Dashboard de Bodeguero</h1>
        <p>Bienvenido {{ auth()->user()->name }}: Aquí puedes gestionar inventario y compras.</p>
        <!-- Contenido específico para bodeguero -->
    </div>
@endsection
