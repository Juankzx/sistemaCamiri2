@extends('layouts.login')

@section('content')
<div class="login-header">
    <h2>Sistema Administrativo Minimarket</h2>
</div>
<form method="POST" action="{{ route('login.post') }}">
    @csrf
    <div class="form-group">
        <div class="input-group">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="form-group">
        <div class="input-group">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="form-group form-check">
        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember">Recordarme</label>
    </div>

    <!-- Botones con el "o" centrado -->
    <div class="text-center mt-4">
        <div class="d-flex align-items-center justify-content-center">
            <button type="submit" class="btn btn-primary">Acceder</button>
            <span class="mx-3 or-divider">o</span>
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#pinModal">
                Iniciar Sesión como Cajero
            </button>
        </div>
    </div>
</form>

<div class="login-footer">
    @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">Olvidé mi contraseña</a>
    @endif
</div>

<!-- Incluyendo el modal -->
@include('modals.login-pin-modal')

@endsection

@section('css')
<style>
    /* Estilo para el texto "o" */
    .or-divider {
        font-size: 1.2rem;
        font-weight: bold;
        color: #888;
    }
</style>
@endsection

@section('js')
<script>
    function addDigit(digit) {
        const pinInput = document.getElementById('pin');
        if (pinInput.value.length < 6) { // Máximo 6 dígitos
            pinInput.value += digit;
        }
    }

    function clearPin() {
        document.getElementById('pin').value = '';
    }

    // SweetAlert para mensajes de error al iniciar sesión como cajero
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
</script>
@endsection
