@extends('adminlte::page')

@section('title', 'Bodega')

@section('content_header')
    <h1>{{ $bodegaGeneral->nombre }}</h1>
@stop

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Buscar:</h4>
            <div class="row mt-2">
                <div class="col-md-6">
                    <input type="text" id="searchProduct" class="form-control" placeholder="Buscar producto...">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="productTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nombre del Producto</th>
                            <th>Stock Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $producto)
                            <tr>
                                <td>{{ $producto->nombre }}</td>
                                <td>{{ $producto->inventarios->sum('cantidad') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
    .thead-dark th {
        background-color: #343a40;
        color: white;
    }
    .card-header {
        padding: 1rem;
    }
    .card-body {
        padding: 1rem;
    }
</style>
@endsection

@section('js')
<script>
    document.getElementById('searchProduct').addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('#productTable tbody tr');

        rows.forEach(row => {
            const productName = row.cells[0].textContent.toLowerCase();
            const productCode = row.cells[1].textContent.toLowerCase();
            if (productName.includes(searchText) || productCode.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection
