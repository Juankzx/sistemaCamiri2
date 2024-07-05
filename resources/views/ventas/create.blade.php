@extends('adminlte::page')

@section('title', 'Crear Venta')

@section('content_header')
    <h1>Crear Venta</h1>
@stop

@section('content')
<div class="container">
    <form action="{{ route('ventas.store') }}" method="POST">
        @csrf

        <!-- Información básica de la venta -->
        <div class="card">
            <div class="card-header">
                <h3>Datos de la Venta</h3>
            </div>
            <div class="card-body">
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <div class="form-group">
                    <label for="sucursal_id">Sucursal:</label>
                    <select class="form-control" id="sucursal_id" name="sucursal_id" required>
                        <option value="" disabled selected>Seleccione una sucursal</option>
                        @foreach ($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="metodo_pago_id">Método de Pago:</label>
                    <select class="form-control" id="metodo_pago_id" name="metodo_pago_id" required>
                        <option value="" disabled selected>Seleccione un método de pago</option>
                        @foreach ($metodosPago as $metodo)
                            <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    
                    <input type="hidden" class="form-control" id="fecha" name="fecha" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                </div>

            </div>
        </div>

        <!-- Detalles de la venta -->
        <div class="card mt-3">
            <div class="card-header">
                <h4>Detalles de la Venta</h4>
            </div>
            <div class="card-body" id="detallesVenta">
                <button type="button" class="btn btn-secondary mb-3" onclick="agregarDetalle()">Agregar Producto</button>
            </div>
        </div>
        <!-- Total de la venta -->
        <div class="form-group mt-3">
            <label for="total">Total de Factura</label>
            <input type="number" class="form-control" id="total" name="total" required readonly>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Guardar Venta</button>
    </form>
</div>

<script>
let indiceDetalle = 0;
let total = 0;

function agregarDetalle() {
    const container = document.getElementById('detallesVenta');
    const html = `
        <div class="detail-group form-group border p-3" id="detalle-${indiceDetalle}">
            <div class="row">
                <div class="col-md-4">
                    <label for="producto_id-${indiceDetalle}">Producto:</label>
                    <select class="form-control" id="producto_id-${indiceDetalle}" name="detalles[${indiceDetalle}][producto_id]" onchange="updatePrecioUnitario(${indiceDetalle})" required>
                        <option value="" disabled selected>Seleccione un producto</option>    
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" data-precioventa="{{ $producto->precioventa }}">{{ $producto->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="inventario_id-${indiceDetalle}">Inventario:</label>
                    <select class="form-control" id="inventario_id-${indiceDetalle}" name="detalles[${indiceDetalle}][inventario_id]" required>
                        <option value="" disabled selected>Seleccione un inventario</option>    
                        @foreach($inventarios as $inventario)
                            <option value="{{ $inventario->id }}">{{ $inventario->producto->nombre }} - {{ $inventario->sucursal->nombre }} - {{ $inventario->cantidad }} en stock - {{ $inventario->bodega ? $inventario->bodega->nombre : 'Sin bodega' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="cantidad-${indiceDetalle}">Cantidad:</label>
                    <input type="number" class="form-control" id="cantidad-${indiceDetalle}" name="detalles[${indiceDetalle}][cantidad]" oninput="calculateSubtotal(${indiceDetalle})" required>
                </div>
                <div class="col-md-3">
                    <label for="precio_unitario-${indiceDetalle}">Precio Unitario:</label>
                    <input type="number" class="form-control" id="precio_unitario-${indiceDetalle}" name="detalles[${indiceDetalle}][precio_unitario]" readonly required>
                </div>
                <div class="col-md-2">
                    <label for="subtotal-${indiceDetalle}">Subtotal:</label>
                    <input type="text" class="form-control" id="subtotal-${indiceDetalle}" name="detalles[${indiceDetalle}][subtotal]" readonly>
                </div>
            </div>
            <button type="button" class="btn btn-danger mt-3" onclick="eliminarDetalle(${indiceDetalle})">Eliminar Producto</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    updatePrecioUnitario(indiceDetalle); // Actualizar el precio unitario inicialmente al agregar el detalle
    indiceDetalle++;
}

function updatePrecioUnitario(indice) {
    const productoSelect = document.getElementById(`producto_id-${indice}`);
    const precioVenta = productoSelect.options[productoSelect.selectedIndex].getAttribute('data-precioventa');
    document.getElementById(`precio_unitario-${indice}`).value = precioVenta;
    calculateSubtotal(indice); // Calcula el subtotal cada vez que se cambia el producto
}

function calculateSubtotal(indice) {
    const cantidad = document.getElementById(`cantidad-${indice}`).value;
    const precio = document.getElementById(`precio_unitario-${indice}`).value;
    const subtotal = cantidad * precio;
    document.getElementById(`subtotal-${indice}`).value = subtotal.toFixed(0);
    calculateTotal();
}

function calculateTotal() {
    total = 0;
    document.querySelectorAll('[name^="detalles["][name$="][subtotal]"]').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('total').value = total.toFixed(0);
}

function eliminarDetalle(indice) {
    const detalle = document.getElementById(`detalle-${indice}`);
    detalle.remove();
    calculateTotal();  // Recalcular el total después de eliminar un detalle
}
</script>


@stop
