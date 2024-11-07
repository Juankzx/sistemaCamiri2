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

    <!-- Campos de búsqueda en vivo -->
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" id="searchProveedor" class="form-control" placeholder="Buscar por nombre de proveedor...">
        </div>
        <div class="col-md-4">
            <select id="searchEstado" class="form-control">
                <option value="">Buscar por estado</option>
                <option value="solicitado">Solicitado</option>
                <option value="entregado">Entregado</option>
                <option value="pendiente">Pendiente</option>
                <option value="en_transito">En tránsito</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="date" id="searchFecha" class="form-control" placeholder="Buscar por fecha...">
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Número de Orden</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        
                        <th style="width: 30%;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="ordenesTableBody">
                    @foreach ($ordenes as $orden)
                    <tr>
                        <td>{{ $orden->numero_orden }}</td>
                        <td>{{ $orden->proveedor->nombre }} - {{ $orden->proveedor->rut }}</td>
                        <td>{{ $orden->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $orden->estado == 'solicitado' ? 'bg-danger' : ($orden->estado == 'entregado' ? 'bg-success' : ($orden->estado == 'en_transito' ? 'bg-warning' : 'bg-secondary')) }}">
                                {{ $orden->estado }}
                            </span>
                        </td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="{{ route('ordenes-compras.show', $orden->id) }}">
                                <i class="fa fa-fw fa-eye"></i>
                            </a>
                            <a href="{{ route('ordenes-compras.edit', $orden) }}" class="btn btn-sm btn-info">
                                <i class="fa fa-fw fa-edit"></i>
                            </a>
                            <form action="{{ route('ordenes-compras.destroy', $orden) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Está seguro de que desea eliminar esta orden de compra? Esta acción no se puede deshacer.');">
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

        <!-- Sección de Paginación -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            @if ($ordenes->count() > 0)
                <div>
                    <p class="small text-muted">
                        Mostrando {{ $ordenes->firstItem() }} a {{ $ordenes->lastItem() }} de {{ $ordenes->total() }} registros
                    </p>
                </div>
                <div>
                    {{ $ordenes->links('pagination::bootstrap-4') }} <!-- Estilo Bootstrap 4 para la paginación -->
                </div>
            @else
                <div class="col-12 text-center">
                    <p>No se encontraron órdenes.</p>
                </div>
            @endif
        </div>

    </div>
</div>
@stop
