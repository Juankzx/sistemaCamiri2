@extends('adminlte::page')

@section('template_title')
    Proveedores
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Proveedores') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('proveedores.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Nombre</th>
                                    <th >Rut</th>
									<th >Direccion</th>
									<th >Telefono</th>
									<th >Email</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proveedores as $proveedore)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $proveedore->nombre }}</td>
                                        <td >{{ $proveedore->rut }}</td>
										<td >{{ $proveedore->direccion }}</td>
										<td >{{ $proveedore->telefono }}</td>
										<td >{{ $proveedore->email }}</td>

                                            <td>
                                                <form action="{{ route('proveedores.destroy', $proveedore->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('proveedores.show', $proveedore->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('proveedores.edit', $proveedore->id) }}"><i class="fa fa-fw fa-edit"></i></a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $proveedores->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
