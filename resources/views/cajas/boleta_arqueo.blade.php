@extends($isVendedor ? 'layouts.app' : 'adminlte::page')

@section('title', 'Boleta de Arqueo de Caja')

@section('content')
<div class="ticket">
    <!-- Encabezado de la Boleta -->
    <div class="header">
        <h3>Arqueo de Caja</h3>
        <p>Caja #{{ $caja->id }}</p>
    </div>
    <hr>

    <!-- Información del Usuario -->
    <div class="user-info">
        <p><strong>Usuario:</strong> {{ $caja->user->name }}</p>
        <p><strong>Sucursal:</strong> {{ $caja->sucursal->nombre }}</p>
    </div>
    <hr>

    <!-- Fechas de Apertura y Cierre -->
    <div class="dates">
        <p><strong>Fecha Apertura:</strong> {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i:s') }}</p>
        <p><strong>Fecha Cierre:</strong> {{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y H:i:s') : 'N/A' }}</p>
    </div>
    <hr>

    <!-- Resumen de Ventas por Método de Pago -->
    <div class="payment-summary">
        <h4>Resumen de Métodos de Pago</h4>
        <table>
            <tr>
                <td><strong>Efectivo:</strong></td>
                <td>${{ number_format($totalEfectivo, 0, ',', '.') }}</td>
                <td>({{ $ventasEfectivo }} ventas)</td>
            </tr>
            <tr>
                <td><strong>Tarjeta:</strong></td>
                <td>${{ number_format($totalTarjeta, 0, ',', '.') }}</td>
                <td>({{ $ventasTarjeta }} ventas)</td>
            </tr>
            <tr>
                <td><strong>Amipass:</strong></td>
                <td>${{ number_format($totalAmipass, 0, ',', '.') }}</td>
                <td>({{ $ventasAmipass }} ventas)</td>
            </tr>
        </table>
    </div>
    <hr>

    <!-- Totales Finales -->
    <div class="totals">
        <p><strong>Monto de Apertura:</strong> ${{ number_format($caja->monto_apertura, 0, ',', '.') }}</p>
        <p><strong>Total Ventas (Efectivo):</strong> ${{ number_format($totalEfectivo, 0, ',', '.') }}</p>
        <p><strong>Balance Final (A Entregar):</strong> ${{ number_format($balanceFinal, 0, ',', '.') }}</p>
    </div>
    <hr>


    <!-- Información Adicional -->
    <div class="additional-info">
        <p><strong>Total Ventas Realizadas:</strong> {{ $totalVentasRealizadas }}</p>
        
        <p><strong>Firma del Cajero:<br></strong> </p>
        <p><strong></strong> ___________________</p>
    </div>
    <hr>

    <!-- Pie de Página -->
    <div class="footer">
        <p>Fecha y Hora de Impresión: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>Gracias por su preferencia</p>
    </div>
</div>

<!-- Botón para imprimir -->
<button id="printButton" class="btn btn-primary mt-3">Imprimir Boleta</button>
@endsection

@section('js')
<script>
    // Función para imprimir la boleta
    document.getElementById('printButton').addEventListener('click', function () {
        window.print();
    });
</script>
@endsection

@section('css')
<style>
    .ticket {
        width: 300px;
        font-family: 'Courier New', Courier, monospace;
        padding: 10px;
        margin: 0 auto;
        border: 1px solid black;
        box-shadow: 0px 0px 5px #000;
    }
    .header, .user-info, .dates, .payment-summary, .totals, .additional-info, .footer {
        text-align: center;
    }
    h3, h4 {
        margin: 5px 0;
        padding: 0;
    }
    table {
        width: 100%;
        margin-top: 10px;
        border-collapse: collapse;
    }
    td {
        text-align: left;
        padding: 2px 0;
    }
    hr {
        border: 1px dashed black;
    }
    .footer p {
        font-size: 10px;
        margin: 0;
        padding: 0;
    }
</style>
@endsection
