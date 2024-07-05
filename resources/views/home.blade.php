@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@endsection

@section('content')
    <!-- Accesos rápidos -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="ventas">0</h3>
                    <p>Ventas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="ventas" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="cajas">0</h3>
                    <p>Cajas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
                <a href="cajas" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="productos">0</h3>
                    <p>Productos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
                <a href="productos" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- /.row -->

    <!-- Gráfico de ventas mensuales -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ventas Mensuales</h3>
                </div>
                <div class="card-body">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simulación de datos, en un caso real estos datos vendrían de una API
            const data = {
                ventas: 150,
                cajas: 75,
                productos: 300
            };

            // Función para actualizar los datos en el dashboard
            function actualizarDatos() {
                document.getElementById('ventas').innerText = data.ventas;
                document.getElementById('cajas').innerText = data.cajas;
                document.getElementById('productos').innerText = data.productos;
            }

            // Actualizar los datos al cargar la página
            actualizarDatos();

            // Gráfico de ventas mensuales
            var ctx = document.getElementById('salesChart').getContext('2d');
            var salesChart = new Chart(ctx, {
                type: 'bar', // Tipo de gráfico: 'bar', 'line', 'pie', etc.
                data: {
                    labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'], // Etiquetas
                    datasets: [{
                        label: 'Ventas',
                        data: [10, 20, 30, 40, 50, 60, 70], // Datos de ejemplo
                        backgroundColor: 'rgba(60,141,188,0.9)', // Color de fondo de las barras
                        borderColor: 'rgba(60,141,188,0.8)', // Color del borde de las barras
                        borderWidth: 1 // Ancho del borde de las barras
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true // El eje y comienza en 0
                        }
                    }
                }
            });
        });
    </script>
@endsection
