@extends('adminlte::page')

@section('content')
<div class="container">
    <h1>Pagos</h1>
    <a href="{{ route('pagos.create') }}" class="btn btn-primary">Registrar Pago</a>
    <table class="table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Factura</th>
                <th>Método de Pago</th>
                <th>Monto</th>
                <th>Fecha de Pago</th>
                <th>Número de Transferencia</th>
                <th>Estado Pago</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pagos as $pago)
            <tr>
                <td>{{ $pago->id }}</td>
                <td>{{ $pago->factura->numero_factura }}</td>
                <td>{{ $pago->metodoPago->nombre }}</td>
                <td>${{ $pago->monto }}</td>
                <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                <td>{{ $pago->numero_transferencia }}</td>
                <td class="text-center">
                            <span class="badge {{ $pago->estado_pago == 'pendiente' ? 'bg-danger' : ($pago->estado_pago == 'pagado' ? 'bg-success' : 'bg-success') }}">
                                {{ $pago->estado_pago }}
                            </span>
                        </td>
                <td>
                    <a href="{{ route('pagos.edit', $pago) }}" class="btn btn-secondary">Editar</a>
                    <form action="{{ route('pagos.destroy', $pago) }}" method="POST" style="display: inline-block;">
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