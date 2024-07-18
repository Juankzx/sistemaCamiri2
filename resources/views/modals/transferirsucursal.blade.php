<!-- Modal de Transferencia -->

<div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="transferModalLabel">Transferir Producto a Sucursal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="transferForm" action="" method="POST">
          @csrf
          <div class="form-group">
            <label for="cantidad">Cantidad</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
          </div>
          <div class="form-group">
            <label for="sucursal_id">Sucursal</label>
            <select class="form-control" id="sucursal_id" name="sucursal_id" required>
            <option value="" disabled selected>Seleccione una Sucursal</option>      
            @foreach($sucursales as $sucursal)
                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Transferir</button>
        </form>
      </div>
    </div>
  </div>
</div>