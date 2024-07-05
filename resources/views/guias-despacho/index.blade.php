@extends('adminlte::page')

@section('content')
<div class="container">
    <h1>Guías de Despacho</h1>
    <a href="{{ route('guias-despacho.create') }}" class="btn btn-primary">Crear Guía de Despacho</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
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
                <td>{{ $guia->ordenCompra->numero_orden }}</td>
                        <td class="text-center">
                            <span class="badge {{ $guia->estado == 'emitida' ? 'bg-danger' : ($guia->estado == 'en_transito' ? 'bg-warning' : 'bg-success') }}">
                                {{ $guia->estado }}
                            </span>
                        </td>
                <td>
                    <a href="{{ route('guias-despacho.edit', $guia) }}" class="btn btn-secondary">Editar</a>
                    <form action="{{ route('guias-despacho.destroy', $guia) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
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
