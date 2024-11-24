@extends('adminlte::page')

@section('content')
    <div class="container">
        <h1>Dashboard de Vendedor</h1>
        <p>Bienvenido {{ auth()->user()->name }}: Aquí puedes gestionar ventas y cajas.</p>
        <!-- Contenido específico para vendedor -->
    </div>
@endsection
