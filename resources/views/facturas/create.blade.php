@extends('adminlte::page')

@section('content')
<div class="container">
    <h1>Crear Factura</h1>
    <form action="{{ route('facturas.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="guia_despacho_id">Guía de Despacho</label>
            <select class="form-control" id="guia_despacho_id" name="guia_despacho_id" required>
                @foreach ($guias_despacho as $guia)
                    <option value="{{ $guia->id }}">{{ $guia->numero_guia }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="numero_factura">Número de Factura</label>
            <input type="text" class="form-control" id="numero_factura" name="numero_factura" required>
        </div>
        <div class="form-group">
            <label for="fecha_factura">Fecha de Factura</label>
            <input type="date" class="form-control" id="fecha_factura" name="fecha_factura" required>
        </div>
        <div class="form-group">
            <label for="total_factura">Total de la Factura</label>
            <input type="number" class="form-control" id="total_factura" name="total_factura" required>
        </div>
        <div class="form-group">
            <label for="estado_pago">Estado de Pago</label>
            <select class="form-control" id="estado_pago" name="estado_pago" required>
                <option value="pendiente">Pendiente</option>
                <option value="pagado">Pagado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Factura</button>
    </form>
</div>
@endsection