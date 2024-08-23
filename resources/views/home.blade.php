@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <!-- Selector de Sucursal -->
        <div class="col-12">
            <form action="{{ route('dashboard') }}" method="GET" class="form-inline mb-4">
                <label for="sucursal" class="mr-2">Seleccionar Sucursal:</label>
                <select name="sucursal_id" id="sucursal" class="form-control" onchange="this.form.submit()">
                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" {{ $selectedSucursal == $sucursal->id ? 'selected' : '' }}>
                            {{ $sucursal->nombre }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        
        <!-- Ventas últimos 7 días -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">Ventas últimos 7 días</div>
                <div class="card-body">
                    <h5 class="card-title">Total: ${{ number_format($ventas7dias, 0, ',', '.') }}</h5>
                    <canvas id="ventas7diasChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Ventas últimos meses -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">Ventas últimos meses</div>
                <div class="card-body">
                    <h5 class="card-title">Total: ${{ number_format($ventasUltimosMeses->sum('total'), 0, ',', '.') }}</h5>
                    <canvas id="ventasMesesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Ventas Efectivo vs Tarjeta -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">Ventas Efectivo / Tarjeta</div>
                <div class="card-body">
                    <h5 class="card-title">Total: ${{ number_format($ventasEfectivoTarjeta->sum('total'), 0, ',', '.') }}</h5>
                    <canvas id="ventasEfectivoTarjetaChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
    // Ventas últimos 7 días Chart
    var ctx7dias = document.getElementById('ventas7diasChart').getContext('2d');
    var ventas7diasChart = new Chart(ctx7dias, {
        type: 'line',
        data: {
            labels: {!! json_encode($ventasUltimosMeses->pluck('mes')->toArray()) !!}, // Verifica los datos y etiquetas
            datasets: [{
                label: 'Ventas',
                data: {!! json_encode($ventasUltimosMeses->pluck('total')->toArray()) !!}, // Verifica los datos
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: false,
            }]
        }
    });

    // Ventas últimos meses Chart
    var ctxMeses = document.getElementById('ventasMesesChart').getContext('2d');
    var ventasMesesChart = new Chart(ctxMeses, {
        type: 'bar',
        data: {
            labels: {!! json_encode($ventasUltimosMeses->pluck('mes')->toArray()) !!},
            datasets: [{
                label: 'Ventas',
                data: {!! json_encode($ventasUltimosMeses->pluck('total')->toArray()) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
            }]
        }
    });

    // Ventas Efectivo vs Tarjeta Chart
    var ctxEfectivoTarjeta = document.getElementById('ventasEfectivoTarjetaChart').getContext('2d');
    var ventasEfectivoTarjetaChart = new Chart(ctxEfectivoTarjeta, {
        type: 'bar',
        data: {
            labels: ['Efectivo', 'Tarjeta'],
            datasets: [{
                label: 'Ventas',
                data: {!! json_encode($ventasEfectivoTarjeta->pluck('total')->toArray()) !!},
                backgroundColor: ['rgba(75, 192, 192, 0.5)', 'rgba(255, 99, 132, 0.5)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1,
            }]
        }
    });
</script>
@endsection
