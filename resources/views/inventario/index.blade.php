@extends('adminlte::page')

@section('template_title')
    Inventarios
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Inventarios') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('inventarios.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                                    {{ __('Create New') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Producto</th>
                                        <th>Imagen</th>
                                        <th>Sucursal</th>
                                        <th>Bodega</th>
                                        <th>Cantidad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventarios as $inventario)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $inventario->producto->nombre }}</td>
                                            <td>
                                                <img src="{{ asset('storage/' . $inventario->producto->imagen) }}" alt="{{ $inventario->producto->nombre }}" width="75" height="75">
                                            </td>
                                            <td>{{ $inventario->sucursal->nombre }}</td>
                                            <td>{{ optional($inventario->bodega)->nombre ?? 'N/A' }}</td>
                                            <td>{{ $inventario->cantidad }}</td>
                                            <td>
                                                <form action="{{ route('inventarios.destroy', $inventario->id) }}" method="POST">
                                                    <!-- <a class="btn btn-sm btn-primary" href="{{ route('inventarios.show', $inventario->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('inventarios.edit', $inventario->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('') }}</a>-->
                                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#increaseQuantityModal-{{ $inventario->id }}">
                                                        <i class="fa fa-fw fa-plus"></i> {{ __('') }}
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#decreaseQuantityModal-{{ $inventario->id }}">
                                                        <i class="fa fa-fw fa-minus"></i> {{ __('') }}
                                                    </button>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;">
                                                        <i class="fa fa-fw fa-trash"></i> {{ __('') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr> 
                                        <!-- Modal para aumentar la cantidad -->
                                        <div class="modal fade" id="increaseQuantityModal-{{ $inventario->id }}" tabindex="-1" role="dialog" aria-labelledby="increaseQuantityModalLabel-{{ $inventario->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="increaseQuantityModalLabel-{{ $inventario->id }}">{{ __('Incrementar Cantidad') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="{{ route('inventarios.updateQuantity', $inventario->id) }}">
                                                            @csrf
                                                            <div class="form-group">
                                                                <label for="cantidad" class="form-label">{{ __('Cantidad') }}</label>
                                                                <input type="number" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" id="cantidad" placeholder="Cantidad">
                                                                {!! $errors->first('cantidad', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cerrar') }}</button>
                                                                <button type="submit" class="btn btn-primary">{{ __('Realizar') }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <!-- Modal para disminuir la cantidad -->
                                    <div class="modal fade" id="decreaseQuantityModal-{{ $inventario->id }}" tabindex="-1" role="dialog" aria-labelledby="decreaseQuantityModalLabel-{{ $inventario->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="decreaseQuantityModalLabel-{{ $inventario->id }}">{{ __('Disminuir Cantidad') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="{{ route('inventarios.decreaseQuantity', $inventario->id) }}">
                                                            @csrf
                                                            <div class="form-group">
                                                                <label for="cantidad" class="form-label">{{ __('Cantidad') }}</label>
                                                                <input type="number" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" id="cantidad" placeholder="Cantidad">
                                                                {!! $errors->first('cantidad', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cerrar') }}</button>
                                                                <button type="submit" class="btn btn-primary">{{ __('Realizar') }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $inventarios->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection