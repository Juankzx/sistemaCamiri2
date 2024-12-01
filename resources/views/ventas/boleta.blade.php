<!DOCTYPE html>
<html>
<head>
    <style>
        /* Estilos específicos para impresoras de 80 mm */
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            margin: 0;
            padding: 0;
            width: 80mm;
            max-width: 80mm;
        }
        .container {
            padding: 0;
            margin: 0 auto;
            text-align: center;
            box-sizing: border-box;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .details {
            text-align: left;
            margin-bottom: 10px;
            padding: 0 5mm;
        }
        .details p {
            margin: 0;
        }
        .items {
            text-align: left;
            margin-top: 10px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 10px 5mm;
        }
        .items p {
            margin: 5px 0;
        }
        .totals {
            margin-top: 10px;
            border-top: 1px solid #000;
            padding-top: 10px;
            text-align: left;
            padding: 0 5mm;
        }
        .totals p {
            margin: 5px 0;
        }
        .qr-code {
            text-align: center;
            margin-top: 15px;
        }
        .qr-code img {
            width: 30mm;
            height: 30mm;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">Boleta de Venta</div>
        <div class="details">
            <p>N° Venta: {{ $venta->id }}</p>
            <p>Sucursal: {{ $venta->sucursal->nombre }} - {{ $venta->sucursal->direccion }}</p>
            <p>Vendedor: {{ $venta->user->name }}</p>
            <p>Fecha: {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i:s') }}</p>
            <p>Método de Pago: {{ $venta->metodo_pago->nombre }}</p>
        </div>

        <div class="items">
            <p><strong>Productos:</strong></p>
            @foreach($venta->detallesVenta as $detalle)
                <p>{{ $detalle->producto->nombre }}<br>{{ $detalle->cantidad }} x ${{ number_format($detalle->precio_unitario, 0) }}</p>
            @endforeach
        </div>

        <div class="totals">
            <p>Subtotal: ${{ number_format($venta->total / 1.19, 0) }}</p>
            <p>IVA (19%): ${{ number_format($venta->total - ($venta->total / 1.19), 0) }}</p>
            <p><strong>Total: ${{ number_format($venta->total, 0) }}</strong></p>
        </div>

        @if($venta->metodo_pago_id == 1) <!-- 1 representa "Efectivo" -->
        <div class="payments">
            <p><strong>Monto Recibido:</strong> ${{ number_format($venta->monto_recibido, 0) }}</p>
            <p><strong>Vuelto:</strong> ${{ number_format($venta->monto_recibido - $venta->total, 0) }}</p>
        </div>
        @endif

        <div class="qr-code">
            <img src="{{ $qrCodeDataUri }}" alt="QR Code">
            <p>Escanea para ver online</p>
        </div>
    </div>
</body>
</html>
