<!-- Modal de Transferencia Masiva -->
<div class="modal fade" id="transferirMasivoModal" tabindex="-1" role="dialog" aria-labelledby="transferirMasivoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="transferirMasivoModalLabel"><i class="fas fa-exchange-alt"></i> Transferir Productos a Sucursal</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="transferirMasivoForm" method="POST" action="{{ route('inventarios.storeTransferirmasivo') }}">
                @csrf
                <div class="modal-body">
                    <!-- Selección de Sucursal Destino -->
                    <div class="form-group">
                        <label for="sucursalDestino">Sucursal Destino:</label>
                        <select id="sucursalDestino" name="sucursal_id" class="form-control" required>
                            <option value="" disabled selected>Seleccione una Sucursal</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Campo de Búsqueda de Producto -->
                    <div class="form-group">
                        <label for="buscarProducto">Buscar Producto:</label>
                        <input type="text" id="buscarProducto" class="form-control" placeholder="Nombre de producto">
                    </div>

                    <!-- Tabla de Productos para Transferir -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Bodega</th>
                                    <th>Sucursal Actual</th>
                                    <th>Cantidad Disponible</th>
                                    <th>Cantidad a Transferir</th>
                                </tr>
                            </thead>
                            <tbody id="modalInventoryTableBody">
                                @foreach($inventarios as $index => $inventario)
                                    <tr data-sucursal="{{ $inventario->sucursal ? $inventario->sucursal->id : '' }}" 
                                        data-producto="{{ strtolower($inventario->producto->nombre) }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $inventario->producto->nombre }}</td>
                                        <td>{{ $inventario->bodega ? $inventario->bodega->nombre : 'N/A' }}</td>
                                        <td>{{ $inventario->sucursal ? $inventario->sucursal->nombre : 'N/A' }}</td>
                                        <td>{{ intval($inventario->cantidad) }}</td>
                                        <td>
                                            <input type="number" name="cantidad[{{ $inventario->producto->id }}]" class="form-control" min="0" value="0">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-exchange-alt"></i> Transferir</button>
                </div>
            </form>
        </div>
    </div>
</div>


@section('js')
<script>
    // Filtrar los productos en función de la sucursal seleccionada
    document.getElementById('sucursalDestino').addEventListener('change', function () {
        const destinoSucursalId = this.value;
        const rows = document.querySelectorAll('#modalInventoryTableBody tr');

        rows.forEach(row => {
            const sucursalId = row.getAttribute('data-sucursal');
            const isSameSucursal = sucursalId === destinoSucursalId;

            // Ocultar el producto si pertenece a la sucursal seleccionada
            row.style.display = isSameSucursal ? 'none' : '';
        });
    });

    // Validar antes de enviar el formulario de transferencia masiva
    document.getElementById('transferirMasivoForm').addEventListener('submit', function(event) {
        let hasValidTransfer = false;
        const cantidadInputs = document.querySelectorAll('input[name^="cantidad["]');

        cantidadInputs.forEach(input => {
            const cantidad = parseFloat(input.value);
            if (cantidad > 0) {
                hasValidTransfer = true;
            }
        });

        if (!hasValidTransfer) {
            event.preventDefault(); // Cancelar el envío
            alert('Por favor, ingrese cantidades válidas para al menos un producto.');
        }
    });

    // Restablecer el modal cuando se cierra
    $('#transferirMasivoModal').on('hidden.bs.modal', function () {
        document.getElementById('sucursalDestino').value = ''; // Restablece la selección de la sucursal
        const cantidadInputs = document.querySelectorAll('input[name^="cantidad["]');
        cantidadInputs.forEach(input => input.value = ''); // Limpiar las cantidades

        // Mostrar todas las filas cuando se cierra el modal
        const rows = document.querySelectorAll('#modalInventoryTableBody tr');
        rows.forEach(row => row.style.display = '');
    });
</script>

@endsection
