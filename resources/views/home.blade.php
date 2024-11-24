@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <form action="{{ route('home') }}" method="GET">
            <div class="form-group">
                <label for="sucursal_id">Seleccionar Sucursal</label>
                <select name="sucursal_id" id="sucursal_id" class="form-control" onchange="this.form.submit()">
                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" {{ $sucursal->id == $sucursalId ? 'selected' : '' }}>
                            {{ $sucursal->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>${{ number_format($ventasHoy, 0, ',', '.') }}</h3>
                <p>Ventas Hoy</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>${{ number_format($ventasMes, 0, ',', '.') }}</h3>
                <p>Ventas del Mes</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>${{ number_format($comprasMes, 0, ',', '.') }}</h3>
                <p>Compras del Mes</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $productosBajoStock }}</h3>
                <p>Productos con Bajo Stock</p>
            </div>
            <div class="icon">
                <i class="fas fa-box"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <h4>Comparación de Ventas Semanales</h4>
        <div class="card">
            <div class="card-body">
                <canvas id="ventasSemanaChart" style="height: 200px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <h4>Productos Más Vendidos</h4>
        <table class="table table-striped">
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
                        <td><span class="badge badge-success">{{ $producto->total_vendido }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <h4>Productos Menos Vendidos</h4>
        <table class="table table-striped">
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
                        <td><span class="badge badge-danger">{{ $producto->total_vendido }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <h4>Últimas Órdenes de Compra</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Proveedor</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ultimasOrdenesCompra as $orden)
                    <tr>
                        <td>{{ $orden->id }}</td>
                        <td>{{ $orden->proveedor->nombre }}</td>
                        <td>
                            <span class="badge {{ $orden->estado == 'entregado' ? 'badge-success' : 'badge-warning' }}">
                                {{ $orden->estado }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <h4>Últimas Facturas</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Número Factura</th>
                    <th>Fecha</th>
                    <th>Monto Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ultimasfacturas as $factura)
                    <tr>
                        <td>{{ $factura->numero_factura }}</td>
                        <td>{{ $factura->fecha_emision }}</td>
                        <td>${{ $factura->monto_total }}</td>
                        <td>
                            <span class="badge {{ $factura->estado_pago == 'pagado' ? 'badge-success' : 'badge-warning' }}">
                                {{ $factura->estado_pago }}
                            </span>
                        </td>
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
    const ventasSemanaChartCtx = document.getElementById('ventasSemanaChart').getContext('2d');
    const ventasSemanaChart = new Chart(ventasSemanaChartCtx, {
        type: 'line',
        data: {
            labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
            datasets: [
                {
                    label: 'Semana Actual',
                    data: [{{ implode(', ', $ventasSemanaActualData) }}],
                    borderColor: 'rgba(60,141,188,1)',
                    backgroundColor: 'rgba(60,141,188,0.2)',
                    fill: true
                },
                {
                    label: 'Semana Anterior',
                    data: [{{ implode(', ', $ventasSemanaAnteriorData) }}],
                    borderColor: 'rgba(210, 214, 222, 1)',
                    backgroundColor: 'rgba(210, 214, 222, 0.5)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    function cambiarPeriodo(periodo) {
        fetch(`/obtener-datos-ventas/${periodo}`)
            .then(response => response.json())
            .then(data => {
                renderChart(data.labels, data.values);
            })
            .catch(error => console.error('Error al obtener datos:', error));
    }

    // Renderiza el gráfico inicial
    document.addEventListener('DOMContentLoaded', () => {
        cambiarPeriodo('semana'); // Cargar ventas de la semana al inicio
    });
</script>
@endsection
