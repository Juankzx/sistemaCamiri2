@extends('adminlte::page')

@section('template_title')
    {{ __('Crear') }} Inventario
@endsection

@if(session('error'))
    <script>
        window.onload = function() {
            alert('{{ session('error') }}');
        };
    </script>
@endif

@section('content')
<div class="container">
    <h1>Agregar Inventario</h1>
    <form action="{{ route('inventarios.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="productoSearch">Buscar Producto</label>
            <input type="text" class="form-control mb-3" id="productoSearch" placeholder="Buscar por nombre, categoría o código de barras...">
            <select class="form-control" id="producto_id" name="producto_id" required>
                <option value="" disabled selected>Seleccione un Producto</option>    
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}" data-categoria="{{ $producto->categoria->nombre ?? '' }}" data-codigo="{{ $producto->codigo_barra }}">
                        {{ $producto->nombre }} - {{ $producto->categoria->nombre ?? 'Sin categoría' }} - {{ $producto->codigo_barra }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="bodega_id">Bodega</label>
            <select class="form-control" id="bodega_id" name="bodega_id" required>
                <option value="" disabled selected>Seleccione una Bodega</option>    
                @foreach($bodegas as $bodega)
                    <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="cantidad">Cantidad</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>

<script>
document.getElementById('productoSearch').addEventListener('input', function() {
    const searchText = this.value.toLowerCase();
    const options = document.querySelectorAll('#producto_id option');

    let foundMatch = false;

    options.forEach(option => {
        const nombre = option.getAttribute('data-nombre').toLowerCase();
        const categoria = option.getAttribute('data-categoria').toLowerCase();
        const codigo = option.getAttribute('data-codigo').toLowerCase();

        if (nombre.includes(searchText) || categoria.includes(searchText) || codigo.includes(searchText)) {
            option.style.display = '';
            if (!foundMatch) {
                option.selected = true;
                foundMatch = true;
            }
        } else {
            option.style.display = 'none';
        }
    });

    if (!foundMatch) {
        document.querySelector('#producto_id').selectedIndex = 0;
    }
});
</script>
@endsection

@section('css')
<style>
    .form-group {
        margin-bottom: 15px;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
</style>
@endsection
