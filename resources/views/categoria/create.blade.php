@extends('adminlte::page')

@section('template_title')
    {{ __('Crear') }} Categoria
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Crear') }} Categoria</span>
                    </div>
                    <div class="card-body bg-white">
                        <form id="createCategoryForm" method="POST" action="{{ route('categorias.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('categoria.form')
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
    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert para mensajes de éxito y error después de la creación
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error en la operación',
                html: '{!! implode("<br>", $errors->all()) !!}',
                showConfirmButton: true
            });
        @endif

        // Manejador para evitar el envío directo del formulario si se encuentran errores en el lado del cliente
        const form = document.getElementById('createCategoryForm');
        form.addEventListener('submit', function(event) {
            const nombreInput = document.getElementById('nombre').value.trim();
            const descripcionInput = document.getElementById('descripcion').value.trim();
            const estadoInput = document.getElementById('estado').value;

            if (!nombreInput || !descripcionInput || !estadoInput) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos incompletos',
                    text: 'Por favor, complete todos los campos antes de enviar.',
                    showConfirmButton: true
                });
            }
        });
    });
</script>
@endsection
