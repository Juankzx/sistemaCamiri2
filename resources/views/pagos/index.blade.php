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
                <td>{{ $pago->metodoPago ? $pago->metodoPago->nombre : 'N/A' }}</td>
                <td>${{ number_format($pago->monto, 0) }}</td>
                <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                <td>{{ $pago->numero_transferencia ? $pago->numero_transferencia : 'N/A' }}</td>
                <td class="text-center">
                    <span class="badge {{ $pago->estado_pago == 'pendiente' ? 'bg-danger' : 'bg-success' }}">
                        {{ ucfirst($pago->estado_pago) }}
                    </span>
                </td>
                <td>
                    <!-- Botón para Ver Detalle -->
                    <a class="btn btn-sm btn-primary" href="{{ route('pagos.show', $pago->id) }}">
                        <i class="fa fa-fw fa-eye"></i>
                    </a>
                    
                    <!-- Condicional para Deshabilitar el Botón de Editar si el estado es "pagado" -->
                    @if ($pago->estado_pago == 'pendiente')
                        <a href="{{ route('pagos.edit', $pago) }}" class="btn btn-sm btn-success">
                            <i class="fa fa-fw fa-edit"></i>
                        </a>
                    @else
                        <button type="button" class="btn btn-sm btn-secondary" onclick="showEditAlert()">
                            <i class="fa fa-fw fa-edit"></i>
                        </button>
                    @endif

                    <!-- Botón para Eliminar -->
                    <form action="{{ route('pagos.destroy', $pago) }}" method="POST" style="display: inline-block;">
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

    <!-- Sección de Paginación -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <p class="small text-muted">
                Mostrando {{ $pagos->firstItem() }} a {{ $pagos->lastItem() }} de {{ $pagos->total() }} registros
            </p>
        </div>
        <div>
            {{ $pagos->links('pagination::bootstrap-4') }} <!-- Estilo Bootstrap 4 para la paginación -->
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showEditAlert() {
        Swal.fire({
            icon: 'error',
            title: 'Acción no permitida',
            text: 'No se puede editar un pago que ya está en estado "pagado".',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#d33'
        });
    }
</script>
@endsection

@section('css')
<style>
    .bg-danger {
        background-color: #dc3545 !important; /* Rojo */
    }
    .bg-success {
        background-color: #28a745 !important; /* Verde */
    }
    .btn-secondary {
        background-color: #6c757d;
        cursor: not-allowed;
    }
</style>
@endsection
