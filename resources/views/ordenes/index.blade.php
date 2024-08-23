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
                        <th>Total</th>
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
                            <span class="badge {{ $orden->estado == 'solicitado' ? 'bg-danger' : ($orden->estado == 'entregado' ? 'bg-success' : 'bg-warning') }}">
                                {{ $orden->estado }}
                            </span>
                        </td>
                        <td>${{ $orden->total }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="{{ route('ordenes-compras.show', $orden->id) }}">
                                <i class="fa fa-fw fa-eye"></i>
                            </a>
                            <a href="{{ route('ordenes-compras.edit', $orden) }}" class="btn btn-sm btn-info">
                                <i class="fa fa-fw fa-edit"></i>
                            </a>
                            @if($orden->estado == 'solicitado')
                            <a href="{{ route('ordenes-compras.entregar', $orden->id) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i>
                            </a>
                            @endif
                            <form action="{{ route('ordenes-compras.destroy', $orden) }}" method="POST" style="display: inline-block;">
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
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargamos las órdenes desde la variable PHP en un array de objetos JS
        const orders = @json($ordenes);

        // Configuración de Fuse.js
        const fuseOptions = {
            keys: ['proveedor.nombre', 'estado', 'created_at'], // Claves de búsqueda
            threshold: 0.3 // Sensibilidad de la búsqueda
        };

        const fuse = new Fuse(orders, fuseOptions);

        // Inicialmente mostramos todas las órdenes
        displayOrders(orders);

        // Manejador de búsqueda por proveedor
        document.getElementById('searchProveedor').addEventListener('input', function(e) {
            filterOrders();
        });

        // Manejador de búsqueda por estado
        document.getElementById('searchEstado').addEventListener('change', function(e) {
            filterOrders();
        });

        // Manejador de búsqueda por fecha
        document.getElementById('searchFecha').addEventListener('change', function(e) {
            filterOrders();
        });

        // Función para filtrar las órdenes basadas en los inputs de búsqueda
        function filterOrders() {
            const searchProveedor = document.getElementById('searchProveedor').value.trim();
            const searchEstado = document.getElementById('searchEstado').value;
            const searchFecha = document.getElementById('searchFecha').value;

            let filteredOrders = orders;

            // Búsqueda por proveedor usando Fuse.js
            if (searchProveedor !== '') {
                const result = fuse.search(searchProveedor);
                filteredOrders = result.map(r => r.item);
            }

            // Filtrar por estado
            if (searchEstado !== '') {
                filteredOrders = filteredOrders.filter(order => order.estado === searchEstado);
            }

            // Filtrar por fecha
            if (searchFecha !== '') {
                filteredOrders = filteredOrders.filter(order => order.created_at.startsWith(searchFecha));
            }

            displayOrders(filteredOrders);
        }

        // Función para mostrar las órdenes
        function displayOrders(filteredOrders) {
            const tableBody = document.querySelector('#ordenesTableBody');
            tableBody.innerHTML = '';

            if (filteredOrders.length > 0) {
                filteredOrders.forEach(order => {
                    const row = `
                        <tr>
                            <td>${order.numero_orden}</td>
                            <td>${order.proveedor.nombre} - ${order.proveedor.rut}</td>
                            <td>${new Date(order.created_at).toLocaleString()}</td>
                            <td class="text-center">
                                <span class="badge ${order.estado === 'solicitado' ? 'bg-danger' : (order.estado === 'entregado' ? 'bg-success' : 'bg-warning')}">
                                    ${order.estado}
                                </span>
                            </td>
                            <td>$${order.total}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="/ordenes-compras/${order.id}"><i class="fa fa-fw fa-eye"></i></a>
                                <a href="/ordenes-compras/${order.id}/edit" class="btn btn-sm btn-info"><i class="fa fa-fw fa-edit"></i></a>
                                ${order.estado === 'solicitado' ? `<a href="/ordenes-compras/${order.id}/entregar" class="btn btn-sm btn-success"><i class="fas fa-check"></i></a>` : ''}
                                <form action="/ordenes-compras/${order.id}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron órdenes.</td></tr>';
            }
        }
    });
</script>
@endsection
