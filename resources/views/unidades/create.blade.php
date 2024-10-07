@extends('adminlte::page')

@section('content')
    <div class="container">
        <h1>Crear Nueva Unidad de Medida</h1>
        <form action="{{ route('unidades.store') }}" method="POST" id="unidadForm">
            @csrf
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>
            <div class="form-group">
                <label for="abreviatura">Abreviatura:</label>
                <input type="text" class="form-control @error('abreviatura') is-invalid @enderror" id="abreviatura" name="abreviatura" value="{{ old('abreviatura') }}" required>
                {!! $errors->first('abreviatura', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('unidades.index') }}" class="btn btn-secondary">Volver</a>
        </form>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mensaje de éxito para alertar al usuario
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Mensaje de error para mostrar cualquier error de validación
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error en la operación',
                html: '{!! implode("<br>", $errors->all()) !!}',
                showConfirmButton: true
            });
        @endif

        // Validación del formulario para evitar que se dupliquen las unidades
        const form = document.getElementById('unidadForm');
        form.addEventListener('submit', function(event) {
            // Convertir el texto a minúsculas y eliminar espacios en blanco para nombre
            const nombre = document.getElementById('nombre').value.trim().toLowerCase();
            const abreviatura = document.getElementById('abreviatura').value.trim().toUpperCase();

            // Verificar si hay campos vacíos o duplicados
            if (nombre === "" || abreviatura === "") {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Por favor, completa todos los campos antes de enviar.',
                });
            }
        });
    });
</script>
@endsection
