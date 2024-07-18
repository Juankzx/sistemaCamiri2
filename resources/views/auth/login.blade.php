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
    <button type="submit" class="btn btn-primary btn-block">Acceder</button>
    <div class="login-footer">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">Olvidé mi contraseña</a>
        @endif
        <br>
        @if (Route::has('register'))
            <a href="{{ route('register') }}">Crear una nueva cuenta</a>
        @endif
    </div>
</form>
@endsection
