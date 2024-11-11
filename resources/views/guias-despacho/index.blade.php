@extends('adminlte::page')

@section('title', 'Guias de Despacho')

@section('content')
<div class="container">
    <h1>Guías de Despacho</h1>
    <a href="{{ route('guias-despacho.create') }}" class="btn btn-primary">+ Recepcionar Guía de Despacho</a>
    <table class="table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Número de Guía</th>
                <th>Fecha de Entrega</th>
                <th>Orden de Compra</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($guias as $guia)
            <tr>
                <td>{{ $guia->id }}</td>
                <td>{{ $guia->numero_guia }}</td>
                <td>{{ \Carbon\Carbon::parse($guia->fecha_entrega)->format('d/m/Y') }}</td>
                <td>{{ $guia->ordenCompra ? $guia->ordenCompra->numero_orden : 'Sin orden de compra' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $guia->estado == 'emitida' ? 'bg-danger' : ($guia->estado == 'en_transito' ? 'bg-warning' : 'bg-success') }}">
                                {{ $guia->estado }}
                            </span>
                        </td>
                <td>
                    <a href="{{ route('guias-despacho.edit', $guia) }}" class="btn btn-sm btn-success"><i class="fa fa-fw fa-edit"></i></a>
                    <a class="btn btn-sm btn-primary" href="{{ route('guias-despacho.show', $guia->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                    <form action="{{ route('guias-despacho.destroy', $guia) }}" method="POST" style="display: inline-block;">
                        
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
     <!-- Sección de Paginación -->
     <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <p class="small text-muted">
                    Mostrando {{ $guias->firstItem() }} a {{ $guias->lastItem() }} de {{ $guias->total() }} registros
                </p>
            </div>
            <div>
                {{ $guias->links('pagination::bootstrap-4') }} <!-- Estilo Bootstrap 4 para la paginación -->
            </div>
        </div>
</div>
@endsection
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
