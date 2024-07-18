<!-- Modal Incrementar -->
<div class="modal fade" id="incrementarModal" tabindex="-1" role="dialog" aria-labelledby="incrementarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="incrementarModalLabel">Incrementar Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cantidad_incrementar">Cantidad</label>
                        <input type="number" class="form-control" id="cantidad_incrementar" name="cantidad" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Incrementar</button>
                </div>
            </form>
        </div>
    </div>
</div>
