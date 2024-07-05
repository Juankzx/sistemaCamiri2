@extends('adminlte::page')

@section('content')
<div class="container">
    <h1>Crear Guía de Despacho</h1>
    <form action="{{ route('guias-despacho.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="numero_guia">Número de Guía</label>
            <input type="text" class="form-control" id="numero_guia" name="numero_guia" required>
        </div>
        <div class="form-group">
            <label for="fecha_entrega">Fecha de Entrega</label>
            <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
        </div>
        <div class="form-group">
            <label for="orden_compra_id">Orden de Compra</label>
            <select class="form-control" id="orden_compra_id" name="orden_compra_id" required>
                <option value="" disabled selected>Seleccione una Orden de Compra</option>
                @foreach($ordenCompra as $ordenCompras)
                    <option value="{{ $ordenCompras->id }}">N°: {{ $ordenCompras->numero_orden }} - Estado: {{ $ordenCompras->estado }} - Proveedor: {{ $ordenCompras->proveedor->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="estado">Estado</label>
            <select class="form-control" id="estado" name="estado">
                <option value="emitida">Emitida</option>
                <option value="en_transito">En Tránsito</option>
                <option value="entregada">Entregada</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Guía</button>
    </form>
</div>
@endsection
