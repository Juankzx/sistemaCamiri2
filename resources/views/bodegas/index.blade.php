@extends('adminlte::page')

@section('content')
    <div class="container">
        <h1>Lista de Bodegas</h1>
        <a href="{{ route('bodegas.create') }}" class="btn btn-primary">Agregar Bodega</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bodegas as $bodega)
                    <tr>
                        <td>{{ $bodega->id }}</td>
                        <td>{{ $bodega->nombre }}</td>
                        <td>
                        <a class="btn btn-sm btn-primary " href="{{ route('bodegas.show', $bodega->id) }}"><i class="fa fa-fw fa-eye"></i></a>    
                        <a href="{{ route('bodegas.edit', $bodega->id) }}" class="btn btn-sm btn-success"><i class="fa fa-fw fa-edit"></i></a>
                            
                            <form action="{{ route('bodegas.destroy', $bodega->id) }}" method="POST" style="display:inline-block;">
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