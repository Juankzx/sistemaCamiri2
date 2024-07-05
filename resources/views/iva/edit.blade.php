@extends('adminlte::page')

@section('template_title')
    {{ __('Update') }} Iva
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Iva</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('ivas.update', $iva->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('iva.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
