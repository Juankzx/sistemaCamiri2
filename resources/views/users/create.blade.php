@extends('adminlte::page')

@section('template_title', 'Crear Usuario')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Crear Usuario</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Manejo de errores con SweetAlert -->
            @if ($errors->any())
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        html: '{!! implode("<br>", $errors->all()) !!}',
                        showConfirmButton: true,
                    });
                </script>
            @endif

            <!-- Formulario de creación de usuario -->
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <!-- Nombre -->
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Nombre" value="{{ old('name') }}" required>
                </div>

                <!-- Correo Electrónico -->
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Correo Electrónico" value="{{ old('email') }}" required>
                </div>

                <!-- Contraseña -->
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
                </div>

                <!-- Confirmar Contraseña -->
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirmar Contraseña" required>
                </div>

                <!-- Rol -->
                <div class="form-group">
                    <label for="role">Rol</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="">Seleccione un rol</option>
                        <option value="vendedor" {{ old('role') == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                        <option value="bodeguero" {{ old('role') == 'bodeguero' ? 'selected' : '' }}>Bodeguero</option>
                        <option value="administrador" {{ old('role') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                        <option value="root" {{ old('role') == 'root' ? 'selected' : '' }}>Root</option>
                    </select>
                </div>

                <!-- PIN (solo para vendedores) -->
                <div class="form-group" id="pin-field" style="display: none;">
                    <label for="pin">PIN</label>
                    <input type="text" name="pin" id="pin" class="form-control" placeholder="Ingrese un PIN de 6 dígitos" maxlength="6" pattern="\d{6}" title="El PIN debe contener exactamente 6 números" value="{{ old('pin') }}">
                </div>

                <!-- Botones -->
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                    
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role'); // Campo de selección de rol
        const pinField = document.getElementById('pin-field'); // Contenedor del PIN
        const pinInput = document.getElementById('pin'); // Input del PIN

        // Función para gestionar la visibilidad del campo PIN
        const togglePinField = () => {
            if (roleSelect.value === 'vendedor') {
                pinField.style.display = 'block';
            } else {
                pinField.style.display = 'none';
                pinInput.value = ''; // Limpiar el campo PIN si no es vendedor
            }
        };

        // Evento: cuando se cambia el rol
        roleSelect.addEventListener('change', togglePinField);

        // Llamar a la función al cargar la página (por si ya está seleccionado)
        togglePinField();

        // Validación en tiempo real para el PIN (solo números y 6 dígitos)
        pinInput.addEventListener('input', function () {
            const maxLength = 6;
            const numericPattern = /^\d*$/; // Solo números permitidos

            if (!numericPattern.test(this.value) || this.value.length > maxLength) {
                this.value = this.value.slice(0, maxLength).replace(/\D/g, ''); // Recortar y eliminar caracteres no numéricos
            }
        });

        // SweetAlert para mensajes de error
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                html: '{!! implode("<br>", $errors->all()) !!}',
                showConfirmButton: true,
            });
        @endif

        // SweetAlert para mensajes de éxito
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 1500
            });
        @endif
    });
</script>
@endsection
