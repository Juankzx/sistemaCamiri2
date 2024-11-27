@extends($isVendedor ? 'layouts.app' : 'adminlte::page')

@section('title', 'Gestión de Cajas')

@section('content_header')
    <h1>Gestión de Cajas</h1>
@stop

@section('content')
<div class="container-fluid">
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header">
    <h3 class="card-title">Listado de Cajas</h3>

    @if ($cajaAbierta)
        <!-- Botón de cerrar caja en la cabecera con total de ventas -->
        <button 
            class="btn btn-danger btn-sm float-right" 
            {{-- Cambia entre data-toggle y data-bs-toggle según el rol --}}
            @if(auth()->user()->hasRole('vendedor'))
                data-bs-toggle="modal" 
                data-bs-target="#cerrarCajaModal"
            @else
                data-toggle="modal" 
                data-target="#cerrarCajaModal"
            @endif
            data-id="{{ $cajaAbierta->id }}" 
            data-ventas="{{ $montoVentas }}" 
            title="Cerrar Caja">
            <i class="fas fa-lock"></i> Cerrar Caja
        </button>
    @else
        <!-- Botones para abrir caja y redirigir a ventas -->
        <div class="btn-group float-right">
            <button 
                class="btn btn-primary btn-sm float-right" 
                {{-- Cambia entre data-toggle y data-bs-toggle según el rol --}}
                @if(auth()->user()->hasRole('vendedor'))
                    data-bs-toggle="modal" 
                    data-bs-target="#abrirCajaModal"
                @else
                    data-toggle="modal" 
                    data-target="#abrirCajaModal"
                @endif>
                <i class="fas fa-cash-register"></i> Abrir Caja
            </button>
            @if(auth()->user()->hasRole('vendedor'))
                <a href="{{ route('ventas.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-shopping-cart"></i> Ventas
                </a>
            @endif
        </div>
    @endif

</div>

                

                <!-- Campos de búsqueda en vivo -->
                <div class="row mb-3 p-2">
                    <div class="col-md-4">
                        <!-- Select para buscar sucursal -->
                        <select id="searchSucursal" class="form-control">
                            <option value="">Buscar por sucursal</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->nombre }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="searchEstado" class="form-control">
                            <option value="">Buscar por estado</option>
                            <option value="Abierta">Abierta</option>
                            <option value="Cerrada">Cerrada</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="date" id="searchFecha" class="form-control" placeholder="Buscar por fecha...">
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sucursal</th>
                                <th>Usuario</th>
                                <th>Fecha Apertura</th>
                                <th>Fecha Cierre</th>
                                <th>Monto Apertura</th>
                                <th>Monto Cierre</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="cajasTableBody">
                            @forelse ($cajas as $caja)
                                <tr>
                                    <td>{{ $caja->id }}</td>
                                    <td>{{ $caja->sucursal->nombre }}</td>
                                    <td>{{ $caja->user->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                    <td>$ {{ number_format($caja->monto_apertura, 0) }}</td>
                                    <td>$ {{ $caja->monto_cierre ? number_format($caja->monto_cierre, 0) : 'N/A' }}</td>
                                    <td>{{ $caja->estado ? 'Abierta' : 'Cerrada' }}</td>
                                    <td>
                                        <!-- Botón de Ver -->
                                        <a href="{{ route('cajas.show', $caja->id) }}" class="btn btn-xs btn-info" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (!$caja->estado)
                                            <!-- Botón para Imprimir Boleta solo si la caja está cerrada -->
                                            <a href="{{ route('cajas.imprimir_boleta', $caja->id) }}" class="btn btn-xs btn-success" title="Imprimir Boleta" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No hay cajas registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Sección de Paginación -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <p class="small text-muted">
                    Mostrando {{ $cajas->firstItem() }} a {{ $cajas->lastItem() }} de {{ $cajas->total() }} registros
                </p>
            </div>
            <div>
                {{ $cajas->links('pagination::bootstrap-4') }} <!-- Estilo Bootstrap 4 para la paginación -->
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Modal para Abrir Caja -->
<div class="modal fade" id="abrirCajaModal" tabindex="-1" aria-labelledby="abrirCajaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="abrirCajaModalLabel"><i class="fas fa-cash-register"></i> Abrir Caja</h5>
                <button type="button" class="btn-close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('cajas.abrir') }}" id="abrirCajaForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sucursal_id" class="form-label">Sucursal:</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-store-alt"></i></span>
                            <select class="form-control" name="sucursal_id" required>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="monto_apertura" class="form-label">Monto de Apertura:</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                            <input type="number" class="form-control" name="monto_apertura" required value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-md" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-check-circle"></i> Abrir Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Cerrar Caja -->
<div class="modal fade" id="cerrarCajaModal" tabindex="-1" aria-labelledby="cerrarCajaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cerrarCajaModalLabel"><i class="fas fa-lock"></i> Cerrar Caja</h5>
                <button type="button" class="btn-close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('cajas.cerrar', ['id' => $cajaAbierta ? $cajaAbierta->id : 0]) }}" id="cerrarCajaForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="monto_cierre" class="form-label">Monto de Cierre:</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                            <input type="number" class="form-control" name="monto_cierre" id="monto_cierre" readonly required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-md" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cancelar</button>
                    <button type="submit" class="btn btn-danger btn-md"><i class="fas fa-check-circle"></i> Cerrar Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Mostrar alerta de SweetAlert si existe un mensaje de error
    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: '¡Atención!',
            text: '{{ session('error') }}',
            confirmButtonText: 'Aceptar',
            timer: 5000
        });
    @endif

    // Modal para cerrar caja
    $('#cerrarCajaModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const ventas = button.data('ventas');
        const modal = $(this);
        modal.find('.modal-body #monto_cierre').val(ventas);
    });

    // Filtros en la tabla de cajas
    document.getElementById('searchSucursal').addEventListener('change', filterCajas);
    document.getElementById('searchEstado').addEventListener('change', filterCajas);
    document.getElementById('searchFecha').addEventListener('change', filterCajas);

    function filterCajas() {
        const searchSucursal = document.getElementById('searchSucursal').value.toLowerCase();
        const searchEstado = document.getElementById('searchEstado').value;
        const searchFecha = document.getElementById('searchFecha').value;

        document.querySelectorAll('#cajasTableBody tr').forEach(function(row) {
            const sucursal = row.children[1].textContent.toLowerCase();
            const estado = row.children[7].textContent.toLowerCase();
            const fecha = row.children[3].textContent.split(' ')[0];  // Solo la parte de fecha (d/m/Y)

            const sucursalMatch = searchSucursal === '' || sucursal.includes(searchSucursal);
            const estadoMatch = searchEstado === '' || estado.includes(searchEstado.toLowerCase());
            const fechaMatch = searchFecha === '' || fecha.split('/').reverse().join('-') === searchFecha;

            row.style.display = sucursalMatch && estadoMatch && fechaMatch ? '' : 'none';
        });
    }
</script>
@stop

@section('css')
<style>
    .modal-header {
        border-bottom: 1px solid #f4f4f4;
    }

    .modal-content {
        border-radius: 10px;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
    }

    .modal-body {
        background-color: #f9f9f9;
        padding: 20px 30px;
    }

    .modal-footer {
        background-color: #f4f4f4;
        padding: 15px 20px;
        border-top: 1px solid #ddd;
    }

    .form-label {
        font-weight: bold;
        color: #495057;
    }

    .input-group-text {
        background-color: #e9ecef;
        border-right: none;
    }

    .form-control {
        border-left: none;
        transition: border 0.3s ease;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn-md {
        padding: 6px 12px;
    }

    .btn-close {
        background-color: transparent;
        border: none;
    }

    .btn-close:hover {
        color: white;
    }

    .btn-close span {
        font-size: 1.2rem;
    }
</style>
@endsection
