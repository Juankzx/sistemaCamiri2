div class="row padding-1 p-1">
    <div class="col-md-12">
        <h4>Selecciona productos</h4>
        <div class="form-group">
            <label for="producto_id" class="form-label">Producto</label>
            <select name="productos[]" class="form-control" id="producto_id">
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" name="cantidades[]" class="form-control" id="cantidad">
        </div>
        <div class="form-group">
            <label for="precio_unitario" class="form-label">Precio unitario</label>
            <input type="number" name="precios_unitarios[]" class="form-control" id="precio_unitario">
        </div>
        <button type="button" class="btn btn-primary" id="agregarProducto">Agregar Producto</button>
    </div>
</div>

<script>
    document.getElementById('agregarProducto').addEventListener('click', function () {
        var productSelector = document.getElementById('producto_id');
        var selectedOption = productSelector.options[productSelector.selectedIndex];
        var productName = selectedOption.text;

        var cantidad = document.getElementById('cantidad').value;
        var precioUnitario = document.getElementById('precio_unitario').value;

        var subtotal = cantidad * precioUnitario;
        var iva = subtotal * 0.19;
        var total = subtotal + iva;

        // Añadir nueva fila a la tabla de productos seleccionados
        var table = document.getElementById('productosSeleccionados');
        var newRow = table.insertRow(-1);
        var productNameCell = newRow.insertCell(0);
        var cantidadCell = newRow.insertCell(1);
        var precioUnitarioCell = newRow.insertCell(2);
        var subtotalCell = newRow.insertCell(3);
        var ivaCell = newRow.insertCell(4);
        var totalCell = newRow.insertCell(5);

        productNameCell.innerHTML = productName;
        cantidadCell.innerHTML = cantidad;
        precioUnitarioCell.innerHTML = precioUnitario;
        subtotalCell.innerHTML = subtotal;
        ivaCell.innerHTML = iva;
        totalCell.innerHTML = total;

        // Limpiar campos
        document.getElementById('cantidad').value = '';
        document.getElementById('precio_unitario').value = '';
    });
</script>

<div class="row">
    <div class="col-md-12">
        <h4>Productos Seleccionados</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>IVA</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody id="productosSeleccionados">
                <!-- Aquí se agregarán los productos seleccionados -->
            </tbody>
        </table>
    </div>
</div>