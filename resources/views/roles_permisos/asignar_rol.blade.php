@extends('adminlte::page')

@section('title', 'Asignar Rol a Usuario')

@section('content_header')
    <h1 class="text-center mb-4">Asignar Rol a Usuario</h1>
@stop

@section('content')
<div class="container">
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h3 class="d-flex align-items-center">
                <i class="fas fa-user-tag mr-2"></i> Asignar Rol a Usuario
            </h3>
        </div>
        <div class="card-body">
            <!-- Mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Formulario para Asignar Rol -->
            <form action="{{ route('asignar-rol.store') }}" method="POST">
                @csrf
                <div class="form-row d-flex align-items-center">
                    <!-- Selección de Usuario con búsqueda (Select2) -->
                    <div class="form-group col-md-6">
                        <label for="usuario_id" class="font-weight-bold"><i class="fas fa-user"></i> Usuario:</label>
                        <select name="usuario_id" id="usuario_id" class="form-control select2" required>
                            <option value="">Seleccione un usuario</option>
                            @foreach ($usuarios as $usuario)
                                @if($usuario->id !== auth()->id() && !$usuario->roles->count())
                                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Selección de Rol -->
                    <div class="form-group col-md-6">
                        <label for="rol_id" class="font-weight-bold"><i class="fas fa-user-shield"></i> Rol:</label>
                        <select name="rol_id" id="rol_id" class="form-control" required>
                            <option value="">Seleccione un rol</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Botón de Asignar Rol -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary mt-3 px-4">
                        <i class="fas fa-user-plus"></i> Asignar Rol
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de Usuarios con Rol Asignado -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-users-cog mr-2"></i> Usuarios con Rol Asignado</h3>
            <!-- Buscador en vivo -->
            <input type="text" id="searchInput" class="form-control w-25" placeholder="Buscar usuario...">
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>N°</th>
                        <th><i class="fas fa-user"></i> Usuario</th>
                        <th><i class="fas fa-user-shield"></i> Rol Actual</th>
                        <th><i class="fas fa-tools"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody id="userTable">
                    @foreach ($usuarios as $index => $usuario)
                        @if($usuario->roles->count())
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $usuario->name }}</td>
                                <td>{{ $usuario->roles->pluck('name')->first() }}</td>
                                <td>
                                    <!-- Botón para Remover Rol -->
                                    <form action="{{ route('asignar-rol.destroy') }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Remover Rol" onclick="return confirm('¿Estás seguro de que deseas remover el rol de este usuario?')">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </form>

                                    <!-- Botón para Cambiar Rol -->
                                    <form action="{{ route('asignar-rol.store') }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">
                                        <select name="rol_id" class="form-control d-inline" style="width: auto;">
                                            <option value="">Cambiar Rol</option>
                                            @foreach ($roles as $rol)
                                                <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-warning btn-sm" title="Cambiar Rol">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Inicializa Select2 en el select de usuario
        $('.select2').select2({
            placeholder: "Seleccione un usuario",
            allowClear: true
        });

        // Función de búsqueda en vivo para la tabla
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#userTable tr');
            
            rows.forEach(row => {
                let userName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                row.style.display = userName.includes(filter) ? '' : 'none';
            });
        });
    });
</script>
@endsection

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding: .375rem .75rem;
    }
    .select2-container .select2-selection--single {
        height: calc(3rem + 2px);
    }
</style>
@endsection
