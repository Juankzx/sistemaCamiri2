@extends('adminlte::page')

@section('content')
    <div class="container">
        <h1>Editar Bodega</h1>
        <form action="{{ route('bodegas.update', $bodega->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $bodega->nombre }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>
@endsection