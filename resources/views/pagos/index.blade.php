@extends('adminlte::page')

@section('content')
<div class="container">
    <h1>Pagos</h1>
    <a href="{{ route('pagos.create') }}" class="btn btn-primary">Registrar Pago</a>

    <!-- Buscador y Filtro -->
    <div class="row my-4">
        <div class="col-md-6">
            <input 
                type="text" 
                id="searchInput" 
                class="form-control" 
                placeholder="Buscar por factura, método de pago, monto o descripción..."
            >
        </div>
        <div class="col-md-6">
            <select id="filterEstado" class="form-control">
                <option value="">Todos los Estados</option>
                <option value="pagado">Pagado</option>
                <option value="pendiente">Pendiente</option>
            </select>
        </div>
    </div>

    <table class="table" id="pagosTable">
        <thead>
            <tr>
                <th>N°</th>
                
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
                    
                    <!-- Botón para Completar Pago si está pendiente -->
                    @if ($pago->estado_pago == 'pendiente')
                        <a href="{{ route('pagos.edit', $pago->id) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-fw fa-check"></i>
                        </a>
                    @else
                        <button type="button" class="btn btn-sm btn-secondary" onclick="showEditAlert()">
                            <i class="fa fa-fw fa-check"></i> 
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

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const filterEstado = document.getElementById('filterEstado');
        const table = document.getElementById('pagosTable');
        const rows = Array.from(table.getElementsByTagName('tbody')[0].getElementsByTagName('tr'));

        function filterTable() {
            const searchText = searchInput.value.toLowerCase();
            const estadoFiltro = filterEstado.value;

            rows.forEach(row => {
                const cells = row.getElementsByTagName('td');
                const factura = cells[1].textContent.toLowerCase();
                const metodo = cells[2].textContent.toLowerCase();
                const monto = cells[3].textContent.toLowerCase();
                const estado = cells[6].textContent.toLowerCase();
                const descripcion = cells[7]?.textContent?.toLowerCase() || '';

                // Verificar si la fila coincide con el texto de búsqueda y el estado
                const matchesSearch = factura.includes(searchText) || metodo.includes(searchText) || monto.includes(searchText) || descripcion.includes(searchText);
                const matchesEstado = estadoFiltro === '' || estado.includes(estadoFiltro);

                if (matchesSearch && matchesEstado) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Agregar eventos a los campos de búsqueda y filtro
        searchInput.addEventListener('input', filterTable);
        filterEstado.addEventListener('change', filterTable);
    });
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
