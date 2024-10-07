@extends('adminlte::page')

@section('title', 'Detalle Venta')

@section('content_header')
    <h1 class="text-center mb-4">Detalle Venta: N° {{ $venta->id }}</h1>
@stop

@section('content')
<div class="container d-flex justify-content-center">
    <!-- Contenedor de información de la venta -->
    <div class="col-md-8">
        <div class="card">
            <h4 class="card-title text-center my-4">Detalles de la Venta</h4> <!-- Ajuste del margen superior del título -->
            <div class="card-body">
                <div class="row mb-3 justify-content-left">
                    <div class="col-md-8">
                        <!-- Información del vendedor y detalles de la venta con mejor alineación -->
                        <p><strong>Vendedor:</strong> {{ $venta->user->name }}</p>    
                        <p><strong>Método de Pago:</strong> {{ $venta->metodo_pago->nombre }}</p>
                        <p><strong>Fecha de Venta:</strong> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i:s') }}</p>
                        <p><strong>Sucursal:</strong> {{ $venta->sucursal->nombre }}</p>
                    </div>
                </div>

                <!-- Tabla de productos vendidos -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>SubTotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalConIva = 0;
                                $totalSinIva = 0;
                            @endphp
                            @foreach($venta->detallesVenta as $detalle)
                            @php
                                $precioUnitarioSinIva = $detalle->precio_unitario / 1.19;
                                $subtotalSinIva = $detalle->cantidad * $precioUnitarioSinIva;
                                $subtotalConIva = $detalle->cantidad * $detalle->precio_unitario;
                                $totalSinIva += $subtotalSinIva;
                                $totalConIva += $subtotalConIva;
                            @endphp
                            <tr>
                                <td>{{ $detalle->producto->nombre }}</td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>${{ number_format($detalle->precio_unitario, 0) }}</td>
                                <td>${{ number_format($subtotalConIva, 0) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Bruto (sin IVA):</th>
                                <th>${{ number_format($totalSinIva, 0) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">IVA (19%):</th>
                                <th>${{ number_format($totalSinIva * 0.19, 0) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Total Neto (con IVA):</th>
                                <th>${{ number_format($totalConIva, 0) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .container {
        margin-top: 30px;
    }

    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-title {
        font-size: 1.4rem;
        font-weight: bold;
        color: #495057;
        margin-top: -10px; /* Ajuste del margen superior del título */
    }

    .table thead {
        background-color: #343a40;
        color: white;
    }

    .table tbody tr:hover {
        background-color: #f2f2f2;
    }

    .table tfoot {
        font-weight: bold;
        background-color: #f8f9fa;
    }

    h1 {
        color: #333;
        font-weight: 600;
    }

    .text-left p {
        margin: 0.5rem 0; /* Reducir el margen entre los párrafos */
    }

    /* Ajustes responsivos para pantallas pequeñas */
    @media (max-width: 768px) {
        .card-body {
            text-align: center;
        }

        .card-body p {
            margin-bottom: 0.75rem;
        }

        .table thead th, .table tbody td, .table tfoot th {
            font-size: 0.9rem; /* Ajuste de tamaño de fuente en dispositivos pequeños */
        }
    }
</style>
@endsection

@section('js')
<script>
    console.log('Detalles de la Venta cargados correctamente!');
</script>
@endsection
