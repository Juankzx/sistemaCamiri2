@extends('adminlte::page')

@section('template_title')
    {{ $inventario->name ?? __('Show') . " " . __('Inventario') }}
@endsection

@section('content')
<div class="container">
    <h1>Detalles del Inventario</h1>
    <div><strong>Producto:</strong> {{ $inventario->producto->nombre }}</div>
    <div><strong>Bodega:</strong> {{ $inventario->bodega ? $inventario->bodega->nombre : 'N/A' }}</div>
    <div><strong>Sucursal:</strong> {{ $inventario->sucursal ? $inventario->sucursal->nombre : 'N/A' }}</div>
    <div><strong>Cantidad:</strong> {{ $inventario->cantidad }}</div>
</div>
@endsection