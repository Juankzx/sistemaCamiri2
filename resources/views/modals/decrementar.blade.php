<!-- Modal Decrementar -->
<div class="modal fade" id="decrementarModal" tabindex="-1" role="dialog" aria-labelledby="decrementarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="decrementarModalLabel">Decrementar Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cantidad_decrementar">Cantidad</label>
                        <input type="number" class="form-control" id="cantidad_decrementar" name="cantidad" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Decrementar</button>
                </div>
            </form>
        </div>
    </div>
</div>
