@extends('adminlte::page')

@section('template_title')
    {{ __('Agregar') }} Inventario
@endsection

@section('content')
<div class="container">
    <h1>Agregar Inventario</h1>
    <form id="inventarioForm" action="{{ route('inventarios.storeMultiple') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="producto_id">Productos</label>
            <select class="form-control select2" id="producto_id" name="producto_ids[]" multiple="multiple" required>
                @foreach($productos as $producto)
                    @php
                        $isAgregado = in_array($producto->id, $productosInventariados); // Verificar si el producto ya está en inventario
                    @endphp
                    <option value="{{ $producto->id }}" 
                            data-status="{{ $isAgregado ? 'agregado' : 'no-agregado' }}" 
                            {{ $isAgregado ? 'disabled' : '' }}> <!-- Deshabilitar si ya está en inventario -->
                        {{ $producto->nombre }} - {{ $producto->codigo_barra }} - {{ $producto->categoria->nombre ?? 'Sin categoría' }} 
                        
                        @if($isAgregado)
                            (Agregado)
                        @endif
                    </option>
                @endforeach
            </select>
        </div>
        
        <!-- Tabla dinámica -->
        <div class="form-group">
            <label for="tabla_inventario">Detalles del Inventario</label>
            <table class="table table-bordered" id="tabla_inventario">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Bodega</th>
                        <th>Cantidad</th>
                        <th>Stock Mínimo</th>
                        <th>Stock Crítico</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                
            </table>
        </div>

        <div class="form-group d-flex justify-content-between">
            <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Volver
            </a>    
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Guardar
            </button>
        </div>
    </form>
</div>
@endsection

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet" />

<style>
.isAgregado {
    font-weight: bold;
}
</style>

@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Formatear el texto de los productos seleccionados
        function formatProducto(producto) {
            if (!producto.id) {
                return producto.text;
            }

            var status = $(producto.element).data('status') === 'agregado';
            var $producto = $('<span>'  + producto.text + '</span>');

            if (status) {
                $producto.addClass('isAgregado');
            }

            return $producto;
        }

        // Inicializar Select2
        $('.select2').select2({
            placeholder: "Busca tu Producto...",
            allowClear: true,
            templateResult: formatProducto,
            templateSelection: formatProducto
        });

        // Inicializar DataTable
        var tabla = $('#tabla_inventario').DataTable({
            language: {
                "lengthMenu": "Mostrar _MENU_ entradas",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
                "infoFiltered": "(filtrado de _MAX_ entradas totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Prim",
                    "last": "Últ",
                    "next": "Sig",
                    "previous": "Ant"
                },
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "emptyTable": "No hay datos disponibles en la tabla",
            }
        });

        // Array para almacenar los productos ya agregados en la tabla
        let productosEnTabla = [];

        // Evento cuando se selecciona un producto
        $('#producto_id').on('change', function() {
            let productosSeleccionados = $(this).val();

            productosSeleccionados.forEach(function(productoId) {
                // Verificar si el producto ya existe en la tabla
                if (productosEnTabla.includes(productoId)) {
                    
                } else {
                    // Añadir el producto al array de productos en la tabla
                    productosEnTabla.push(productoId);

                    // Agregar el producto a la tabla
                    let productoTexto = $("#producto_id option[value='" + productoId + "']").text();
                    tabla.row.add([
                        productoTexto,
                        '<select class="form-control" name="bodega_id['+ productoId +']">@foreach($bodegas as $bodega)<option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>@endforeach</select>',
                        '<input type="number" class="form-control" name="cantidad['+ productoId +']" value="1" required>',
                        '<input type="number" class="form-control" name="stock_minimo['+ productoId +']" value="0" required>',
                        '<input type="number" class="form-control" name="stock_critico['+ productoId +']" value="0" required>',
                        '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(this, \'' + productoId + '\')">X</button>'
                    ]).draw();
                }
            });
        });

        // Función para eliminar un producto de la tabla
        function eliminarProducto(button, productoId) {
            var row = $(button).closest('tr');
            $('#tabla_inventario').DataTable().row(row).remove().draw();

            // Remover el producto del array productosEnTabla
            const index = productosEnTabla.indexOf(productoId);
            if (index > -1) {
                productosEnTabla.splice(index, 1);
            }
        }

        window.eliminarProducto = eliminarProducto;
    });
</script>
@endsection
