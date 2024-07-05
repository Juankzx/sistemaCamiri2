@extends('adminlte::page')

@section('content')
    <h1>Bodega: {{ $bodegaGeneral->nombre }}</h1>
    <table>
        <thead>
            <tr>
                <th>Nombre del Producto</th>
                <th>Codigo de barra</th>
                <th>Cantidad Total</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach ($productos as $producto)
                <tr>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->codigo_barra }}</td>
                    <td>{{ $producto->inventarios->sum('cantidad') }}</td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection