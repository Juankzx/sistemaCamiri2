@extends('adminlte::page')

@section('title', 'Facturas')

@section('content')
<div class="container">
    <h1>Facturas</h1>
    <a href="{{ route('facturas.create') }}" class="btn btn-primary">+ Recepcionar Factura</a>
    <table class="table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Número de Factura</th>
                <th>Fecha de Emisión</th>
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
                <td>{{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y') }}</td>
                <td>${{ number_format($factura->monto_total, 0) }}</td>
                <td class="text-center">
                    <span class="badge {{ $factura->estado_pago == 'pendiente' ? 'bg-danger' : 'bg-success' }}">
                        {{ ucfirst($factura->estado_pago) }}
                    </span>
                </td>
                <td>
                    <a class="btn btn-sm btn-primary" href="{{ route('facturas.show', $factura->id) }}">
                        <i class="fa fa-fw fa-eye"></i>
                    </a>

                    @if ($factura->estado_pago == 'pendiente')
                        <a href="{{ route('facturas.edit', $factura) }}" class="btn btn-sm btn-success">
                            <i class="fa fa-fw fa-edit"></i>
                        </a>
                    @else
                        <button type="button" class="btn btn-sm btn-secondary" onclick="showAlert()">
                            <i class="fa fa-fw fa-edit"></i>
                        </button>
                    @endif

                    <form action="{{ route('facturas.destroy', $factura) }}" method="POST" style="display: inline-block;">
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
                Mostrando {{ $facturas->firstItem() }} a {{ $facturas->lastItem() }} de {{ $facturas->total() }} registros
            </p>
        </div>
        <div>
            {{ $facturas->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function showAlert() {
        Swal.fire({
            icon: 'error',
            title: 'Acción no permitida',
            text: 'No se puede editar una factura que ya ha sido pagada.',
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
