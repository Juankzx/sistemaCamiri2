@extends('adminlte::page')

@section('template_title')
    Metodos Pagos
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Metodos Pagos') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('metodos-pagos.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Nombre</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($metodosPagos as $metodosPago)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $metodosPago->nombre }}</td>

                                            <td>
                                                <form action="{{ route('metodos-pagos.destroy', $metodosPago->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('metodos-pagos.show', $metodosPago->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('metodos-pagos.edit', $metodosPago->id) }}"><i class="fa fa-fw fa-edit"></i></a>
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
                {!! $metodosPagos->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
