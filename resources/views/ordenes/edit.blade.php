@extends('adminlte::page')

@section('title', 'Editar Orden de Compra')

@section('content')
<div class="container">
    <h1>Editar Orden de Compra #{{ $ordenCompra->numero_orden }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ordenes-compras.update', $ordenCompra->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Detalles de los Productos -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Detalles de los Productos</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="detallesOrdenCompra">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ordenCompra->detalles as $detalle)
                            <tr class="detail-group">
                                <td>
                                    <input type="hidden" name="detalles[{{ $loop->index }}][producto_id]" value="{{ $detalle->producto_id }}">
                                    {{ $detalle->producto->nombre }}
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="detalles[{{ $loop->index }}][cantidad]" value="{{ $detalle->cantidad }}" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeDetail(this)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="button" class="btn btn-primary mt-3" onclick="addNewRow()">
                    <i class="fas fa-plus"></i> Agregar Producto
                </button>
            </div>
        </div>

        <!-- Botones de Guardado y Volver -->
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('ordenes-compras.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<script>
    let productos = @json($productos);
    let productosAgregados = new Set();

    function addNewRow() {
        const container = document.querySelector('#detallesOrdenCompra tbody');
        const index = container.children.length;

        const productoOptions = productos.map(producto => {
            return `<option value="${producto.id}">${producto.nombre}</option>`;
        }).join('');

        const row = `
            <tr class="detail-group">
                <td>
                    <select class="form-control" name="detalles[${index}][producto_id]" required>
                        ${productoOptions}
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control" name="detalles[${index}][cantidad]" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeDetail(this)">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;

        container.insertAdjacentHTML('beforeend', row);
    }

    function removeDetail(button) {
        const row = button.closest('.detail-group');
        row.remove();
    }
</script>
@endsection
