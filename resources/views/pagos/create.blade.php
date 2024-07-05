@extends('adminlte::page')

@section('content')
<div class="container">
    <h1>Registrar Pago</h1>
    <form action="{{ route('pagos.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="factura_id">Factura</label>
            <select class="form-control" id="factura_id" name="factura_id" required>
                @foreach ($facturas as $factura)
                    <option value="{{ $factura->id }}">N°: {{ $factura->numero_factura }} - Total: ${{ $factura->total_factura }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="metodo_pago_id">Método de Pago</label>
            <select class="form-control" id="metodo_pago_id" name="metodo_pago_id" required>
                @foreach ($metodosPago as $metodo)
                    <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="monto">Monto</label>
            <input type="text" class="form-control" id="monto" name="monto" required>
        </div>
        <div class="form-group">
            <label for="fecha_pago">Fecha de Pago</label>
            <input type="date" class="form-control" id="fecha_pago" name="fecha_pago">
        </div>
        <div class="form-group">
            <label for="numero_transferencia">Número de Transferencia</label>
            <input type="text" class="form-control" id="numero_transferencia" name="numero_transferencia">
        </div>
       <div class="form-group">
            <label for="estado_pago">Estado del Pago</label>
            <select class="form-control" id="estado_pago" name="estado_pago" required>
                <option value="pendiente" selected>Pendiente</option>
                <option value="pagado">Pagado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Pago</button>
    </form>
</div>
@endsection