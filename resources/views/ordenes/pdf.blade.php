<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Pedido #{{ $orden->numero_orden }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            color: #333;
            margin: 0;
        }
        .info-section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .info-section h2 {
            font-size: 18px;
            color: #555;
            margin-bottom: 10px;
        }
        .info-section p {
            margin: 2px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table, .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .table th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Orden de Compra #{{ $orden->numero_orden }}</h1>
        </div>

        <div class="info-section">
            <h2>Detalles del Proveedor</h2>
            <p><strong>Nombre:</strong> {{ $orden->proveedor->nombre }}</p>
            <p><strong>RUT:</strong> {{ $orden->proveedor->rut }}</p>
            <p><strong>Dirección:</strong> {{ $orden->proveedor->direccion ?? 'No especificada' }}</p>
            <p><strong>Teléfono:</strong> {{ $orden->proveedor->telefono ?? 'No especificado' }}</p>
        </div>

        <div class="info-section">
            <h2>Detalles de la Orden</h2>
            <p><strong>Fecha de Creación:</strong> {{ $orden->created_at->format('d/m/Y H:i:s') }}</p>
            <p><strong>Estado:</strong> {{ $orden->estado }}</p>
            <p><strong>Creado por:</strong> {{ auth()->user()->name ?? 'Sistema' }}</p>
        </div>

        <h2>Detalles de los Productos</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orden->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Gracias por su compra</p>
        </div>
    </div>
</body>
</html>
