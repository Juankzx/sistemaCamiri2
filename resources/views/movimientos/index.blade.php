@extends('adminlte::page')

@section('template_title')
    Movimientos
@endsection

@section('content')
<div class="container-fluid">
    <!-- Campos de búsqueda en vivo -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="form-group">
                <label for="searchName">Buscar por nombre, código de barra, bodega, sucursal o tipo:</label>
                <input type="text" id="searchName" class="form-control" placeholder="Ingrese nombre, código de barra, bodega, sucursal o tipo">
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label for="searchDate">Buscar por fecha:</label>
                <input type="date" id="searchDate" class="form-control">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="card-title">
                            {{ __('Movimientos') }}
                        </span>
                    </div>
                </div>

                <div class="card-body bg-white">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead">
                                <tr>
                                    <th>N°</th>
                                    <th>Producto</th>
                                    <th>Código Barra</th>
                                    <th>Bodega</th>
                                    <th>Sucursal</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody id="movementTableBody">
                                @foreach ($movimientos as $movimiento)
                                    <tr>
                                        <td>{{ $loop->iteration + $movimientos->firstItem() - 1 }}</td>
                                        <td>{{ $movimiento->producto->nombre }}</td>
                                        <td>{{ $movimiento->producto->codigo_barra }}</td>
                                        <td>{{ $movimiento->bodega ? $movimiento->bodega->nombre : 'N/A' }}</td>
                                        <td>{{ $movimiento->sucursal ? $movimiento->sucursal->nombre : 'N/A' }}</td>
                                        <td>{{ ucfirst($movimiento->tipo) }}</td>
                                        <td>{{ $movimiento->cantidad }}</td>
                                        <td>{{ $movimiento->fecha }}</td>
                                        <td>{{ $movimiento->user->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Sección de Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <p class="small text-muted">
                        Mostrando {{ $movimientos->firstItem() }} a {{ $movimientos->lastItem() }} de {{ $movimientos->total() }} registros
                    </p>
                </div>
                <div>
                    {{ $movimientos->links('pagination::bootstrap-4') }} <!-- Estilo Bootstrap 4 para la paginación -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    <style>
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .table th {
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const movements = @json($movimientos->items());
        const options = {
            keys: ['producto.nombre', 'producto.codigo_barra', 'bodega.nombre', 'sucursal.nombre', 'tipo'],
            threshold: 0.3
        };
        const fuse = new Fuse(movements, options);

        document.getElementById('searchName').addEventListener('input', function(e) {
            const searchText = e.target.value.trim();
            filterResults(searchText, document.getElementById('searchDate').value);
        });

        document.getElementById('searchDate').addEventListener('input', function(e) {
            const searchDate = e.target.value;
            filterResults(document.getElementById('searchName').value, searchDate);
        });

        function filterResults(searchText, searchDate) {
            let filteredMovements = movements;
            if (searchText !== '') {
                const result = fuse.search(searchText);
                filteredMovements = result.map(r => r.item);
            }

            if (searchDate !== '') {
                filteredMovements = filteredMovements.filter(movement => movement.fecha.startsWith(searchDate));
            }

            displayMovements(filteredMovements);
        }

        function displayMovements(filteredMovements) {
            const tableBody = document.querySelector('#movementTableBody');
            tableBody.innerHTML = '';
            if (filteredMovements.length > 0) {
                filteredMovements.forEach((movimiento, index) => {
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${movimiento.producto.nombre}</td>
                            <td>${movimiento.producto.codigo_barra}</td>
                            <td>${movimiento.bodega ? movimiento.bodega.nombre : 'N/A'}</td>
                            <td>${movimiento.sucursal ? movimiento.sucursal.nombre : 'N/A'}</td>
                            <td>${movimiento.tipo.charAt(0).toUpperCase() + movimiento.tipo.slice(1)}</td>
                            <td>${movimiento.cantidad}</td>
                            <td>${movimiento.fecha}</td>
                            <td>${movimiento.user.name}</td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron movimientos.</td></tr>';
            }
        }
        displayMovements(movements);
    });
</script>
@endsection
