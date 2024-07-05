<div class="form-group">
    <label for="numero_orden">Número de Orden</label>
    <input type="text" class="form-control" id="numero_orden" name="numero_orden" required>
</div>
<div class="form-group">
    <label for="proveedor_id">Proveedor</label>
    <select class="form-control" id="proveedor_id" name="proveedor_id" required>
        <option value="" disabled selected>Seleccione un proveedor</option>
        @foreach($proveedores as $proveedor)
            <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }} - {{ $proveedor->rut }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="estado">Estado</label>
    <select class="form-control" id="estado" name="estado">
        <option value="solicitado">Solicitado</option>
        <option value="entregado">Entregado</option>
        <option value="cancelado">Cancelado</option>
    </select>
</div>
