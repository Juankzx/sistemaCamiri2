@extends('adminlte::page')

@section('title', 'Órdenes de Compra')

@section('content_header')
    <h1>Órdenes de Compra</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-12">
            <a href="{{ route('ordenes-compras.create') }}" class="btn btn-primary mb-2">
                <i class="fas fa-plus"></i> Crear Orden de Compra
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Número de Orden</th>
                        <th>Proveedor</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th style="width: 30%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ordenes as $orden)
                    <tr>
                        <td>{{ $orden->numero_orden }}</td>
                        <td>{{ $orden->proveedor->nombre }} - {{ $orden->proveedor->rut }}</td>
                        <td class="text-center">
                            <span class="badge {{ $orden->estado == 'solicitado' ? 'bg-danger' : ($orden->estado == 'entregado' ? 'bg-success' : 'bg-warning') }}">
                                {{ $orden->estado }}
                            </span>
                        </td>
                        <td>${{ $orden->total }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="{{ route('ordenes-compras.show', $orden->id) }}">
                                <i class="fa fa-fw fa-eye"></i>
                            </a>
                            <a href="{{ route('ordenes-compras.edit', $orden) }}" class="btn btn-sm btn-info">
                                <i class="fa fa-fw fa-edit"></i>
                            </a>
                            @if($orden->estado == 'solicitado')
                            <a href="{{ route('ordenes-compras.entregar', $orden->id) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i>
                            </a>
                            @endif
                            <form action="{{ route('ordenes-compras.destroy', $orden) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fa fa-fw fa-trash"></i>
                                    
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .bg-danger {
        background-color: #dc3545 !important; /* Rojo */
    }
    .bg-warning {
        background-color: #ffc107 !important; /* Amarillo */
    }
    .bg-success {
        background-color: #28a745 !important; /* Verde */
    }
</style>
@endsection
