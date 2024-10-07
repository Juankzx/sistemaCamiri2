<!-- Modal Decrementar -->
<div class="modal fade" id="decrementarModal" tabindex="-1" role="dialog" aria-labelledby="decrementarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="decrementarModalLabel"><i class="fas fa-minus-circle"></i> Decrementar Stock</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="">
                @csrf
                <div class="modal-body">
                    <!-- Información del Producto -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <!-- Ajustes de los encabezados y el contenido del producto -->
                            <div class="mb-3">
                                <h6 class="card-subtitle text-muted">Producto</h6>
                                <h5 class="card-title" id="productoNombreDecrementar" style="margin-bottom: 8px;">-</h5>
                            </div>
                            <br><div class="mb-3">
                                <h6 class="card-subtitle text-muted">Bodega / Sucursal</h6>
                                <p class="card-text" id="ubicacionDecrementar" style="margin-bottom: 8px;">-</p>
                            </div>
                            <div>
                                <h6 class="card-subtitle text-muted">Cantidad Actual</h6>
                                <p class="card-text" style="font-weight: bold;" id="cantidadActualDecrementar">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Campo de Cantidad a Decrementar -->
                    <div class="form-group">
                        <label for="cantidad_decrementar"><i class="fas fa-boxes"></i> Cantidad a Decrementar</label>
                        <input type="number" class="form-control" id="cantidad_decrementar" name="cantidad" min="1" placeholder="Ingrese la cantidad a restar" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-minus"></i> Decrementar</button>
                </div>
            </form>
        </div>
    </div>
</div>
