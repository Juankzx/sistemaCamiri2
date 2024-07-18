
@extends('adminlte::page')

@section('template_title')
    Movimientos
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Movimientos') }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Producto</th>
                                        <th>Código Barra</th>
                                        <th>Bodega</th>
                                        <th>Sucursal</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($movimientos as $movimiento)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $movimiento->producto->nombre }}</td>
                                            <td>{{ $movimiento->producto->codigo_barra }}</td>
                                            <td>{{ $movimiento->bodega ? $movimiento->bodega->nombre : 'N/A' }}</td>
                                            <td>{{ $movimiento->sucursal ? $movimiento->sucursal->nombre : 'N/A' }}</td>
                                            <td>{{ $movimiento->tipo }}</td>
                                            <td>{{ $movimiento->cantidad }}</td>
                                            <td>{{ $movimiento->fecha }}</td>
                                            <td>{{ $movimiento->user->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $movimientos->links() !!}
            </div>
        </div>
    </div>
@endsection