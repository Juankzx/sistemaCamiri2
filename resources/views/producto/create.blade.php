@extends('adminlte::page')

@section('template_title')
    {{ __('Agregar ') }} Producto
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Agregar Producto') }}</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('productos.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('producto.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Mostrar mensajes de error usando SweetAlert
    @if ($errors->any())
        let errorMessages = '';
        @foreach ($errors->all() as $error)
            errorMessages += '{{ $error }}<br>';
        @endforeach

        Swal.fire({
            icon: 'error',
            title: 'Errores de validación',
            html: errorMessages, // Mostrar todos los mensajes de error en HTML
            showConfirmButton: true
        });
    @endif

    // Mostrar mensajes de éxito cuando se crea un producto
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Producto creado exitosamente!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    // Mostrar mensajes de error general (no solo validación)
    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: '{{ session('error') }}',
            showConfirmButton: true
        });
    @endif
</script>
@endsection
