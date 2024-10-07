@extends('adminlte::page')

@section('template_title')
    {{ __('Editar') }} Proveedores
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Editar') }} Proveedores</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('proveedores.update', $proveedore->id) }}" role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('proveedore.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para aplicar el formato automático del RUT y limitar a 9 dígitos
        function formatRUT(rut) {
            // Eliminar puntos, guiones y limitar a solo números y la letra K
            rut = rut.replace(/^0+|[^0-9kK]+/g, '').toUpperCase();
            
            // Limitar a un máximo de 9 caracteres numéricos antes del dígito verificador
            if (rut.length > 9) {
                rut = rut.slice(0, 9) + rut.slice(-1); // Permitir solo hasta 9 dígitos más el DV
            }

            if (rut.length <= 1) {
                return rut;
            }

            // Separar la parte numérica del dígito verificador
            let cuerpo = rut.slice(0, -1);
            let dv = rut.slice(-1);

            // Agregar puntos cada tres dígitos
            cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            return `${cuerpo}-${dv}`;
        }

        // Aplicar formato al escribir en el campo de RUT
        const rutInput = document.getElementById('rut');
        rutInput.addEventListener('input', function() {
            this.value = formatRUT(this.value);
        });

        // Limitar el número de caracteres a 12 (9 dígitos, 2 puntos, 1 guion)
        rutInput.setAttribute('maxlength', 12);
    });
</script>
@endsection
