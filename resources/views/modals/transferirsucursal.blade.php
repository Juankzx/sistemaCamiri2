<!-- Modal de Transferencia -->
<div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="transferModalLabel"><i class="fas fa-exchange-alt"></i> Transferir Producto a Sucursal</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="transferForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <!-- Información del Producto -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="card-subtitle text-muted">Producto</h6>
                                <h5 class="card-title" id="productoNombreTransferencia" style="margin-bottom: 8px;">-</h5>
                            </div>
                            <br><div class="mb-3">
                                <h6 class="card-subtitle text-muted">Bodega Actual</h6>
                                <p class="card-text" id="ubicacionTransferencia" style="margin-bottom: 8px;">-</p>
                            </div>
                            <div>
                                <h6 class="card-subtitle text-muted">Cantidad Disponible</h6>
                                <p class="card-text" style="font-weight: bold;" id="cantidadActualTransferencia">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Campo de Cantidad a Transferir -->
                    <div class="form-group">
                        <label for="cantidad"><i class="fas fa-boxes"></i> Cantidad a Transferir</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" placeholder="Ingrese la cantidad a transferir" required>
                    </div>

                    <!-- Selección de Sucursal -->
                    <div class="form-group">
                        <label for="sucursal_id"><i class="fas fa-warehouse"></i> Seleccionar Sucursal</label>
                        <select class="form-control" id="sucursal_id" name="sucursal_id" required>
                            <option value="" disabled selected>Seleccione una Sucursal</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
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
