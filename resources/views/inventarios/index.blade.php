@extends('adminlte::page')

@section('title', 'Inventarios')

@section('content_header')
    <h1>Inventario</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-12">
        <a href="{{ route('inventarios.create') }}" class="btn btn-primary mb-2">Agregar Inventario +</a>
    </div>
</div>

<div class="container">
    <form method="GET" action="{{ route('inventarios.index') }}">
        <div class="form-group">
            <label for="search">Buscar:</label>
            <input type="text" name="search" id="search" class="form-control" placeholder="Nombre, código de barra o categoría" value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa fa-fw fa-search"></i></button>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Producto</th>
                <th>Bodega</th>
                <th>Sucursal</th>
                <th>Cantidad</th>
                <th>+/-</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventarios as $inventario)
            <tr>
                <td>
                    @if($inventario->producto->imagen)
                    <img src="{{ Storage::url($inventario->producto->imagen) }}" alt="{{ $inventario->producto->nombre }}" width="65">
                    @endif
                </td>
                <td>{{ $inventario->producto->nombre }}</td>
                <td>{{ $inventario->bodega ? $inventario->bodega->nombre : 'N/A' }}</td>
                <td>{{ $inventario->sucursal ? $inventario->sucursal->nombre : 'N/A' }}</td>
                <td>{{ $inventario->cantidad }}</td>
                <td>
                    <button class="btn btn-info" data-toggle="modal" data-target="#incrementarModal" data-id="{{ $inventario->id }}"><i class="fa fa-fw fa-plus"></i></button>
                    <button class="btn btn-warning" data-toggle="modal" data-target="#decrementarModal" data-id="{{ $inventario->id }}"><i class="fa fa-fw fa-minus"></i></button>
                </td>
                <td>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#transferModal" data-id="{{ $inventario->id }}"><i class="fas fa-exchange-alt"></i></button> 
                </button> 

                    <a href="{{ route('inventarios.edit', $inventario->id) }}" class="btn btn-success"><i class="fa fa-fw fa-edit"></i></a>
                    <form action="{{ route('inventarios.destroy', $inventario->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fa fa-fw fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $inventarios->withQueryString()->links() }}
</div>
@include('modals.incrementar')
@include('modals.decrementar')
@include('modals.transferirsucursal')
@stop

@section('css')
    <style>
        .container { padding-top: 20px; }
    </style>
@stop

@section('js')
<script>
    $('#incrementarModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var modal = $(this);
        modal.find('form').attr('action', '/inventarios/incrementar/' + id);
    });

    $('#decrementarModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var modal = $(this);
        modal.find('form').attr('action', '/inventarios/decrementar/' + id);
    });

    $('#transferModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var modal = $(this);
        modal.find('#transferForm').attr('action', '/inventarios/transferir/' + id);
    });
</script>
@stop