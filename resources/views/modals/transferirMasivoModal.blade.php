<!-- Modal de Transferencia Masiva -->
<div class="modal fade" id="transferirMasivoModal" tabindex="-1" role="dialog" aria-labelledby="transferirMasivoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="transferirMasivoModalLabel"><i class="fas fa-exchange-alt"></i> Transferencia Masiva a Sucursal</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="transferirMasivoForm" method="POST" action="{{ route('inventarios.transferirmasivo') }}">
                @csrf
                <div class="modal-body">
                    <!-- Selección de Sucursal Destino con Select2 -->
                    <div class="form-group">
                        <label for="sucursalDestino"><i class="fas fa-warehouse"></i> Sucursal Destino</label>
                        <select class="form-control select2" id="sucursalDestino" name="sucursal_id" required>
                            <option value="" disabled selected>Seleccione una Sucursal</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Tabla de Productos para Transferencia -->
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
                                <tr data-sucursal="{{ $inventario->sucursal ? $inventario->sucursal->id : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $inventario->producto->nombre }}</td>
                                    <td>{{ $inventario->bodega ? $inventario->bodega->nombre : 'N/A' }}</td>
                                    <td>{{ $inventario->sucursal ? $inventario->sucursal->nombre : 'N/A' }}</td>
                                    <td>{{ intval($inventario->cantidad) }}</td>
                                    <td>
                                        <input type="number" name="cantidad[{{ $inventario->producto->id }}]" class="form-control" min="0" placeholder="0">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Transferir Seleccionados</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inicializar Select2
        $('.select2').select2({
            width: '100%',
            placeholder: "Seleccione una opción",
            allowClear: true
        });
    });

    // Filtrar los productos en función de la sucursal seleccionada
    document.getElementById('sucursalDestino').addEventListener('change', function () {
        const destinoSucursalId = this.value;
        const rows = document.querySelectorAll('#modalInventoryTableBody tr');

        rows.forEach(row => {
            const sucursalId = row.getAttribute('data-sucursal');
            const isSameSucursal = sucursalId === destinoSucursalId;
            row.style.display = isSameSucursal ? 'none' : '';
        });
    });

    // Validar que al menos un producto tenga cantidad válida para transferir
    document.getElementById('transferirMasivoForm').addEventListener('submit', function(event) {
        const cantidadInputs = document.querySelectorAll('input[name^="cantidad"]');
        let validTransfer = false;

        cantidadInputs.forEach(input => {
            if (parseInt(input.value) > 0) {
                validTransfer = true;
            }
        });

        if (!validTransfer) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe ingresar una cantidad válida para al menos un producto antes de realizar la transferencia.',
            });
        }
    });
</script>
