@extends('adminlte::page')

@section('content')
    <div class="container">
        <h1>Crear Nueva Unidad de Medida</h1>
        <form action="{{ route('unidades.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="abreviatura">Abreviatura:</label>
                <input type="text" class="form-control" id="abreviatura" name="abreviatura" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
@endsection
