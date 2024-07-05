@extends('adminlte::page')

@section('content')
    <div class="container">
        <h1>Agregar Bodega</h1>
        <form action="{{ route('bodegas.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
@endsection