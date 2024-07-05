@extends('adminlte::page')

@section('content')
<div class="container">
    <h1>Facturas</h1>
    <a href="{{ route('facturas.create') }}" class="btn btn-primary">Crear Factura</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Número de Factura</th>
                <th>Fecha de Factura</th>
                <th>Total Factura</th>
                <th>Estado de Pago</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($facturas as $factura)
            <tr>
                <td>{{ $factura->id }}</td>
                <td>{{ $factura->numero_factura }}</td>
                <td>{{ \Carbon\Carbon::parse($factura->fecha_factura)->format('d/m/Y') }}</td>

                <td>${{ $factura->total_factura }}</td>
                        <td class="text-center">
                            <span class="badge {{ $factura->estado_pago == 'pendiente' ? 'bg-danger' : 'bg-success' }}">
                                {{ $factura->estado_pago }}
                            </span>
                        </td>
                <td>
                    <a href="{{ route('facturas.edit', $factura) }}" class="btn btn-secondary">Editar</a>
                    <form action="{{ route('facturas.destroy', $factura) }}" method="POST" style="display: inline-block;">
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
    .bg-danger { background-color: #dc3545; color: white; }
    .bg-success { background-color: #28a745; color: white; }
</style>
@endsection