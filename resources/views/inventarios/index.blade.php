@extends('adminlte::page')

@section('title', 'Inventarios')

@section('content_header')
    <h1>Inventario</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-12">
            <a href="{{ route('inventarios.create') }}" class="btn btn-primary mb-2">Agregar Inventario +</a>
            <button class="btn btn-secondary mb-2" data-toggle="modal" data-target="#transferirMasivoModal">Transferir Masivo</button>

        </div>
    </div>

    <div class="container">
        <!-- Campo de búsqueda en vivo -->
        <div class="row">
            <div class="col-md-4">
                <label for="search">Buscar:</label>
                <input type="text" id="inventorySearch" class="form-control" placeholder="Nombre de producto, bodega o sucursal">
            </div>
            <div class="col-md-4">
                <label for="filterLocation">Filtrar por Sucursal o Bodega:</label>
                <select id="filterLocation" class="form-control">
                    <option value="">Todas las Sucursales y Bodegas</option>
                    <optgroup label="Sucursales">
                        @foreach($sucursales as $sucursal)
                            <option value="sucursal-{{ $sucursal->id }}">Sucursal: {{ $sucursal->nombre }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Bodegas">
                        @foreach($bodegas as $bodega)
                            <option value="bodega-{{ $bodega->id }}">Bodega: {{ $bodega->nombre }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
        
        <!-- Tabla responsive -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Bodega</th>
                        <th>Sucursal</th>
                        <th>Cantidad</th>
                        <th>Stock Mínimo</th>
                        <th>Stock Crítico</th>
                        <th>+/-</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody">
                    @foreach($inventarios as $inventario)
                    <tr data-bodega="{{ $inventario->bodega ? $inventario->bodega->id : '' }}" data-sucursal="{{ $inventario->sucursal ? $inventario->sucursal->id : '' }}">
                        <td>{{ $inventario->producto->nombre }}</td>
                        <td>{{ $inventario->bodega ? $inventario->bodega->nombre : 'N/A' }}</td>
                        <td>{{ $inventario->sucursal ? $inventario->sucursal->nombre : 'N/A' }}</td> <!-- Mostrar la sucursal correctamente -->
                        <td>
                            {{-- Mostrar la cantidad con decimales si la unidad es Kilo --}}
                            @if($inventario->producto->unidadMedida->nombre == 'Kilo' || $inventario->producto->unidadMedida->abreviatura == 'Kg')
                                {{ number_format($inventario->cantidad, 2) }}
                            @else
                                {{ intval($inventario->cantidad) }} {{-- Mostrar como entero --}}
                            @endif
                        </td>
                        <td>{{ $inventario->stock_minimo }}</td>
                        <td>{{ $inventario->stock_critico }}</td>
                        <td>
                           <!-- Botón para abrir el modal de incrementar con información adicional -->
                <button class="btn btn-info btn-sm" 
                        data-toggle="modal" 
                        data-target="#incrementarModal" 
                        data-id="{{ $inventario->id }}" 
                        data-producto="{{ $inventario->producto->nombre }}" 
                        data-ubicacion="{{ $inventario->bodega ? $inventario->bodega->nombre : ($inventario->sucursal ? $inventario->sucursal->nombre : 'N/A') }}" 
                        data-cantidad="{{ $inventario->cantidad }}">
                        
                    <i class="fa fa-fw fa-plus"></i>
                </button>
                            
                            <!-- Botón para abrir el modal de decrementar -->
                            <button class="btn btn-warning btn-sm" 
                                    data-toggle="modal" 
                                    data-target="#decrementarModal" 
                                    data-id="{{ $inventario->id }}" 
                                    data-producto="{{ $inventario->producto->nombre }}" 
                                    data-ubicacion="{{ $inventario->bodega ? $inventario->bodega->nombre : ($inventario->sucursal ? $inventario->sucursal->nombre : 'N/A') }}" 
                                    data-cantidad="{{ $inventario->cantidad }}">
                                <i class="fa fa-fw fa-minus"></i>
                            </button>
                        </td>
                        <td>
                            <!-- Botón para abrir el modal de transferencia -->
                            <button class="btn btn-primary btn-sm" 
                                    data-toggle="modal" 
                                    data-target="#transferModal" 
                                    data-id="{{ $inventario->id }}" 
                                    data-producto="{{ $inventario->producto->nombre }}" 
                                    data-ubicacion="{{ $inventario->bodega ? $inventario->bodega->nombre : ($inventario->sucursal ? $inventario->sucursal->nombre : 'N/A') }}" 
                                    data-cantidad="{{ $inventario->cantidad }}">
                                <i class="fas fa-exchange-alt"></i>
                            </button>

                            <!-- Botón para editar -->
                            <a href="{{ route('inventarios.edit', $inventario->id) }}" class="btn btn-success btn-sm"><i class="fa fa-fw fa-edit"></i></a>

                            <form action="{{ route('inventarios.destroy', $inventario->id) }}" method="POST" style="display:inline;" class="form-delete" data-id="{{ $inventario->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger delete-btn btn-sm">
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
                    Mostrando {{ $inventarios->firstItem() }} a {{ $inventarios->lastItem() }} de {{ $inventarios->total() }} registros
                </p>
            </div>
            <div>
                {{ $inventarios->links('pagination::bootstrap-4') }} <!-- Estilo Bootstrap 4 para la paginación -->
            </div>
        </div>

        </div>
    </div>

    <!-- Modales -->
    @include('modals.incrementar')
    @include('modals.decrementar')
    @include('modals.transferirsucursal')
    @include('modals.transferirMasivoModal')
@stop

@section('js')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    <!-- Script para manejar los modales y enviar el formulario al controlador -->
    <script>
       
       
       // Cuando se abre el modal de incrementar
        $('#incrementarModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Elemento que disparó el modal
            var inventarioId = button.data('id'); // Obtener el ID del inventario
            var formAction = "{{ url('/inventarios/incrementar') }}/" + inventarioId; // Construir la URL con la nueva ruta
            $('#incrementarModal form').attr('action', formAction); // Establecer la acción del formulario
       
            // Datos adicionales del inventario
            var productoNombre = button.data('producto'); // Nombre del producto
            var ubicacion = button.data('ubicacion'); // Bodega o sucursal de origen
            var cantidadActual = button.data('cantidad'); // Cantidad actual
            var unidadMedida = button.data('unidad'); // Unidad de medida del producto (Kg, Unidad, etc.)
            var unidadMedida = button.data('unidad'); // Unidad de medida del producto (Kg, Unidad, etc.)


            // Actualizar el contenido del modal con la información
            document.getElementById('productoNombre').innerText = productoNombre;
            document.getElementById('ubicacion').innerText = ubicacion;
            document.getElementById('cantidadActual').innerText = cantidadActual;

             // Ajustar el campo de cantidad a incrementar según la unidad de medida
    var cantidadInput = document.getElementById('cantidad_incrementar');
    if (unidadMedida === 'Kilo' || unidadMedida === 'KG') {
        cantidadInput.setAttribute('step', '0.01'); // Permitir decimales para productos en Kg
        cantidadInput.setAttribute('min', '0.01'); // Establecer el mínimo a 0.01
    } else {
        cantidadInput.setAttribute('step', '1'); // Solo enteros para otros productos
        cantidadInput.setAttribute('min', '1'); // Establecer el mínimo a 1
    }

    
        });
    

        // Cuando se abre el modal de decrementar
    $('#decrementarModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Elemento que disparó el modal
        var inventarioId = button.data('id'); // Obtener el ID del inventario
        var formAction = "{{ url('/inventarios/decrementar') }}/" + inventarioId; // Construir la URL con la nueva ruta
        $('#decrementarModal form').attr('action', formAction); // Establecer la acción del formulario

        // Datos adicionales del inventario
        var productoNombre = button.data('producto'); // Nombre del producto
        var ubicacion = button.data('ubicacion'); // Bodega o sucursal de origen
        var cantidadActual = button.data('cantidad'); // Cantidad actual

        // Asignar valores a los elementos del modal
        document.getElementById('productoNombreDecrementar').innerText = productoNombre;
        document.getElementById('ubicacionDecrementar').innerText = ubicacion;
        document.getElementById('cantidadActualDecrementar').innerText = cantidadActual;
        });

        // Cuando se abre el modal de transferir
        $('#transferModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Elemento que disparó el modal
        var inventarioId = button.data('id'); // Obtener el ID del inventario
        var formAction = "{{ url('/inventarios/transferir') }}/" + inventarioId; // Construir la URL con la nueva ruta
        $('#transferForm').attr('action', formAction); // Establecer la acción del formulario

        // Datos adicionales del inventario
        var productoNombre = button.data('producto'); // Nombre del producto
        var ubicacion = button.data('ubicacion'); // Bodega o sucursal de origen
        var cantidadActual = button.data('cantidad'); // Cantidad actual disponible

        // Asignar valores a los elementos del modal
        document.getElementById('productoNombreTransferencia').innerText = productoNombre;
        document.getElementById('ubicacionTransferencia').innerText = ubicacion;
        document.getElementById('cantidadActualTransferencia').innerText = cantidadActual;
    });


// Interceptar la eliminación con SweetAlert
document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // Prevenir el envío inmediato del formulario
            var form = this.closest('form'); // Obtener el formulario más cercano al botón
            var id = form.getAttribute('data-id'); // Obtener el ID del formulario para verificación
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Enviar el formulario si se confirma la acción
                }
            });
        });
    });

    // SweetAlert para mensajes de éxito o error con tipos de acción
    @if(session('success'))
        let successMessage = '{{ session('success') }}';

        // Detectar mensajes específicos y cambiar el icono y título de SweetAlert
        if (successMessage.includes('transferido')) {
            Swal.fire({
                icon: 'success',
                title: '¡Transferencia realizada!',
                text: successMessage,
                timer: 3000,
                showConfirmButton: false
            });
        } else if (successMessage.includes('incrementada') || successMessage.includes('decrementada')) {
            Swal.fire({
                icon: 'success',
                title: '¡Stock actualizado!',
                text: successMessage,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: successMessage,
                timer: 3000,
                showConfirmButton: false
            });
        }
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
// Filtro de búsqueda por sucursal o bodega en un solo select
document.getElementById('inventorySearch').addEventListener('input', filterInventories);
    document.getElementById('filterLocation').addEventListener('change', filterInventories);

    function filterInventories() {
        const searchValue = document.getElementById('inventorySearch').value.toLowerCase();
        const locationValue = document.getElementById('filterLocation').value;

        const rows = document.querySelectorAll('#inventoryTableBody tr');

        rows.forEach(row => {
            const productName = row.children[0].textContent.toLowerCase();
            const bodega = row.getAttribute('data-bodega');
            const sucursal = row.getAttribute('data-sucursal');

            const matchesSearch = productName.includes(searchValue);
            let matchesLocation = true;

            // Verificar si se ha seleccionado una bodega o sucursal
            if (locationValue) {
                const [type, id] = locationValue.split('-');
                if (type === 'bodega') {
                    matchesLocation = bodega === id;
                } else if (type === 'sucursal') {
                    matchesLocation = sucursal === id;
                }
            }

            if (matchesSearch && matchesLocation) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    </script>
@stop