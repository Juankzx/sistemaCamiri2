@extends('adminlte::page')

@section('title', 'Orden de Compra #'.$orden->numero_orden)

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Orden de Compra #{{ $orden->numero_orden }}</h1>

    <!-- Detalles del Proveedor -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4>Detalles del Proveedor</h4>
        </div>
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $orden->proveedor->nombre ?? 'No especificado' }}</p>
            <p><strong>RUT:</strong> {{ $orden->proveedor->rut ?? 'No especificado' }}</p>
            <p><strong>Dirección:</strong> {{ $orden->proveedor->direccion ?? 'No especificada' }}</p>
            <p><strong>Teléfono:</strong> {{ $orden->proveedor->telefono ?? 'No especificado' }}</p>
        </div>
    </div>

    <!-- Detalles de la Orden -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h4>Detalles de la Orden</h4>
        </div>
        <div class="card-body">
            <p><strong>Fecha de Creación:</strong> {{ $orden->created_at ? $orden->created_at->format('d/m/Y H:i:s') : 'No disponible' }}</p>
            <p><strong>Estado:</strong> 
                <span class="badge {{ $orden->estado === 'solicitado' ? 'bg-danger' : 'bg-success' }}">
                    {{ ucfirst($orden->estado) }}
                </span>
            </p>
        </div>
    </div>

    <!-- Detalles de los Productos -->
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <h4>Detalles de los Productos</h4>
    </div>
    <div class="card-body">
        @if ($orden->detalles->isNotEmpty())
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalCantidad = 0; // Variable para sumar cantidades
                    @endphp
                    @foreach ($orden->detalles as $detalle)
                        @php
                            $totalCantidad += $detalle->cantidad; // Sumar cantidad
                        @endphp
                        <tr>
                            <td>{{ $detalle->producto->nombre ?? 'No disponible' }}</td>
                            <td>{{ $detalle->cantidad }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total de Productos</th>
                        <th>{{ $totalCantidad }}</th> <!-- Mostrar el total -->
                    </tr>
                </tfoot>
            </table>
        @else
            <p>No hay productos asociados a esta orden de compra.</p>
        @endif
    </div>
</div>


    <!-- Botón de Regresar -->
    <div class="text-right">
        <a href="{{ route('ordenes-compras.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>
@stop

@section('css')
<style>
    .card {
        margin-bottom: 20px;
    }
    .card-header {
        font-size: 1.2rem;
    }
    .badge-warning {
        background-color: #ffc107;
        color: #fff;
    }
    .badge-success {
        background-color: #28a745;
        color: #fff;
    }
</style>
@endsection
