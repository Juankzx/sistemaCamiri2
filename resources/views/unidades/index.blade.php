@extends('adminlte::page')


@section('content')
    <div class="container">
        <h1>Unidades de Medida</h1>
        <a href="{{ route('unidades.create') }}" class="btn btn-primary">Crear Nueva Unidad</a>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Abreviatura</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($unidades as $unidad)
                    <tr>
                        <td>{{ $unidad->id }}</td>
                        <td>{{ $unidad->nombre }}</td>
                        <td>{{ $unidad->abreviatura }}</td>
                        
                        <td>
                            <a href="{{ route('unidades.show', $unidad) }}" class="btn btn-sm btn-primary"><i class="fa fa-fw fa-eye"></i></a>
                            <a href="{{ route('unidades.edit', $unidad) }}" class="btn btn-sm btn-success"><i class="fa fa-fw fa-edit"></i></a>
                            <form action="{{ route('unidades.destroy', $unidad) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
