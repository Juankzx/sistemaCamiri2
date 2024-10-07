@extends('adminlte::page')

@section('template_title')
    {{ __('Crear') }} Proveedor
@endsection

@section('content')
<section class="content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <span class="card-title">{{ __('Crear') }} Proveedor</span>
                </div>
                <div class="card-body bg-white">
                    <form method="POST" action="{{ route('proveedores.store') }}" role="form" enctype="multipart/form-data">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
<script>
    $(document).ready(function() {
        // Aplicar la máscara de RUT al campo
        $('#rut').inputmask({
            mask: "9[9.999.999]-9",
            greedy: false,
            definitions: {
                "9": { validator: "[0-9]", cardinality: 1 }
            },
            onBeforePaste: function (pastedValue, opts) {
                return pastedValue.toUpperCase();
            }
        });
    });
</script>
@endsection
