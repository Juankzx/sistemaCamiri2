@extends('adminlte::page')

@section('template_title')
    Productos
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Productos') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('+ Agregar') }}
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
                                    <th>N°</th>   
									<th >Codigo de Barras</th>
                                    <th >Nombre</th>
                                    <th >Unidad de Medida</th>
									<th >Precio Compra</th>
									<th >Precio Venta</th>
                                    <th >Categoria</th>
									<th >Proveedor</th>
                                    <th >Estado</th>
                                    
                                    
									<th >Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($productos as $producto)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
                                            <td >{{ $producto->codigo_barra }}</td>
                                            <td >{{ $producto->nombre }}</td>
                                            <td >{{ $producto->unidadmedida->nombre }} - {{ $producto->unidadmedida->abreviatura }}</td>
										<td >{{ $producto->preciocompra }}</td>
										<td >{{ $producto->precioventa }}</td>
                                        <td >{{ $producto->categoria->nombre ?? 'Sin categoria'}}</td>
										<td >{{ $producto->proveedor->nombre?? 'Sin proveedor'}}</td>
                                        <td>{{ $producto->estado ? 'Activo' : 'Inactivo' }}</td>
                                        
                                        
										

                                            <td>
                                                <form action="{{ route('productos.destroy', $producto->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('productos.show', $producto->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('productos.edit', $producto->id) }}"><i class="fa fa-fw fa-edit"></i></a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('¿Quieres Borrar el item?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $productos->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
