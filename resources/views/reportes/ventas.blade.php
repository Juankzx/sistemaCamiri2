@extends('adminlte::page')

@section('title', 'Reporte de Ventas')

@section('content_header')
    <h1>Reporte de Ventas</h1>
@stop

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <label for="sucursal_id">Sucursal:</label>
        <select name="sucursal_id" id="sucursal_id" class="form-control form-control-sm">
            <option value="0">Todas</option>
            <!-- Agrega opciones de sucursales dinámicamente si es necesario -->
        </select>
    </div>
    <div class="col-md-3">
        <label for="fecha_desde">Desde:</label>
        <input type="date" id="fecha_desde" name="fecha_desde" class="form-control form-control-sm" />
    </div>
    <div class="col-md-3">
        <label for="fecha_hasta">Hasta:</label>
        <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control form-control-sm" />
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary btn-sm me-2">Filtrar</button>
        <button class="btn btn-secondary btn-sm">Limpiar</button>
    </div>
</div>

<!-- Gráfico de Comparación de Ventas Semanal -->
<div class="row mt-3 justify-content-center">
    <div class="col-md-8">
        <h4 class="text-center">Comparación de Ventas Semanal</h4>
        <div class="chart-container" style="position: relative; height:350px; width:100%">
            <canvas id="ventasSemanaChart"></canvas>
        </div>
    </div>
</div>

<!-- Sección de Ventas Diarias -->
<div class="row mt-5">
    <div class="col-md-6">
        <h4>Ventas Diarias</h4>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventasDiarias as $venta)
                <tr>
                    <td>{{ $venta->fecha }}</td>
                    <td>${{ number_format($venta->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Productos Más Vendidos -->
    <div class="col-md-3">
        <h4>Productos Más Vendidos</h4>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Total Vendido</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productosMasVendidos as $producto)
                <tr>
                    <td>{{ $producto->nombre }}</td>
                    <td><span class="badge bg-success">{{ $producto->total_vendido }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Productos Menos Vendidos -->
    <div class="col-md-3">
        <h4>Productos Menos Vendidos</h4>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Total Vendido</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productosMenosVendidos as $producto)
                <tr>
                    <td>{{ $producto->nombre }}</td>
                    <td><span class="badge bg-danger">{{ $producto->total_vendido }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración del gráfico de comparación de ventas semanal
    const ventasSemanaChartCtx = document.getElementById('ventasSemanaChart').getContext('2d');
    const ventasSemanaChart = new Chart(ventasSemanaChartCtx, {
        type: 'line',
        data: {
            labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
            datasets: [
                {
                    label: 'Semana Actual',
                    data: [
                        {{ $ventasSemanaActual->get('Monday', 0) }},
                        {{ $ventasSemanaActual->get('Tuesday', 0) }},
                        {{ $ventasSemanaActual->get('Wednesday', 0) }},
                        {{ $ventasSemanaActual->get('Thursday', 0) }},
                        {{ $ventasSemanaActual->get('Friday', 0) }},
                        {{ $ventasSemanaActual->get('Saturday', 0) }},
                        {{ $ventasSemanaActual->get('Sunday', 0) }}
                    ],
                    borderColor: 'rgba(60,141,188,1)',
                    backgroundColor: 'rgba(60,141,188,0.2)',
                    fill: true
                },
                {
                    label: 'Semana Anterior',
                    data: [
                        {{ $ventasSemanaAnterior->get('Monday', 0) }},
                        {{ $ventasSemanaAnterior->get('Tuesday', 0) }},
                        {{ $ventasSemanaAnterior->get('Wednesday', 0) }},
                        {{ $ventasSemanaAnterior->get('Thursday', 0) }},
                        {{ $ventasSemanaAnterior->get('Friday', 0) }},
                        {{ $ventasSemanaAnterior->get('Saturday', 0) }},
                        {{ $ventasSemanaAnterior->get('Sunday', 0) }}
                    ],
                    borderColor: 'rgba(210, 214, 222, 1)',
                    backgroundColor: 'rgba(210, 214, 222, 0.5)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
