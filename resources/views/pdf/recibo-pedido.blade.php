<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Recibo de Pedido #{{ $pedido->idVenta }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .info-cliente,
        .info-pedido {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .signature {
            margin-top: 50px;
            border-top: 1px solid #333;
            width: 50%;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>RECIBO DE PEDIDO</h2>
        <p>N° {{ $pedido->idVenta }} - Fecha: {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y') }}</p>
    </div>

    <div class="info-cliente">
        <h3>Datos del Cliente</h3>
        @if($pedido->tipoCliente === 'natural' && $pedido->clienteNatural && $pedido->clienteNatural->user)
        <p><strong>Cliente Natural</strong></p>
        <p><strong>Nombre:</strong> {{ $pedido->clienteNatural->user->name }} {{ $pedido->clienteNatural->user->primerApellido }} {{ $pedido->clienteNatural->user->segundApellido ?? '' }}</p>
        <p><strong>CI/NIT:</strong> {{ $pedido->clienteNatural->user->ci ?? 'N/A' }}</p>
        <p><strong>Teléfono:</strong> {{ $pedido->clienteNatural->user->telefono ?? 'N/A' }}</p>
        @elseif($pedido->tipoCliente === 'establecimiento' && $pedido->clienteEstablecimiento)
        <p><strong>Cliente Establecimiento</strong></p>
        <p><strong>Razón Social:</strong> {{ $pedido->clienteEstablecimiento->razonSocial }}</p>
        <p><strong>NIT:</strong> {{ $pedido->clienteEstablecimiento->nit ?? 'N/A' }}</p>
        <p><strong>Teléfono:</strong> {{ $pedido->clienteEstablecimiento->telefono ?? 'N/A' }}</p>
        <p><strong>Representante:</strong>
            {{ $pedido->clienteEstablecimiento->user->name ?? 'N/A' }}
            {{ $pedido->clienteEstablecimiento->user->primerApellido ?? '' }}
        </p>
        @else
        <p class="text-danger">Información del cliente no disponible</p>
        @endif
    </div>

    <div class="info-pedido">
        <h3>Información del Pedido</h3>
        <p><strong>Fecha de Entrega:</strong> {{ $pedido->fechaEntrega ? \Carbon\Carbon::parse($pedido->fechaEntrega)->format('d/m/Y') : 'Por definir' }}</p>
        <p><strong>Estado:</strong>
            @php
            $estados = [
            '0' => 'En Diseño',
            '1' => 'En Producción',
            '2' => 'Terminado',
            '3' => 'Entregado',
            '4' => 'Cancelado'
            ];
            $estadoKey = (string)($pedido->estadoPedido ?? '0');
            echo $estados[$estadoKey] ?? 'Pendiente';
            @endphp
        </p>
    </div>

    <h3>Detalle del Pedido</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->detalleVentas as $item)
            <tr>
                <td>{{ $item->producto->nombre ?? 'Producto no disponible' }}</td>
                <td class="text-center">{{ $item->cantidad }}</td>
                <td class="text-right">Bs. {{ number_format($item->precio_unitario, 2) }}</td>
                <td class="text-right">Bs. {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Subtotal:</th>
                <th class="text-right">Bs. {{ number_format($pedido->subtotal ?? 0, 2) }}</th>
            </tr>
            @if($pedido->descuento > 0)
            <tr>
                <th colspan="3" class="text-right">Descuento:</th>
                <th class="text-right">- Bs. {{ number_format($pedido->descuento, 2) }}</th>
            </tr>
            @endif
            <tr>
                <th colspan="3" class="text-right">Total:</th>
                <th class="text-right">Bs. {{ number_format($pedido->total, 2) }}</th>
            </tr>
            <tr>
                <th colspan="3" class="text-right">Pagado:</th>
                <th class="text-right">Bs. {{ number_format(($pedido->total - $pedido->saldo), 2) }}</th>
            </tr>
            @if($pedido->saldo > 0)
            <tr>
                <th colspan="3" class="text-right">Saldo Pendiente:</th>
                <th class="text-right">Bs. {{ number_format($pedido->saldo, 2) }}</th>
            </tr>
            @endif
        </tfoot>
    </table>

    <div class="mt-4">
        <p><strong>Método de pago:</strong> {{ $pedido->metodo_pago ?? 'No especificado' }}</p>
        @if($pedido->observaciones)
        <p><strong>Observaciones:</strong> {{ $pedido->observaciones }}</p>
        @endif
    </div>

    <div class="signature">
        <p>Firma del Cliente</p>
    </div>

    <div class="footer" style="margin-top: 50px; font-size: 10px; text-align: center;">
        <p>Gracias por su preferencia</p>
        <p>Este es un documento generado automáticamente</p>
    </div>
</body>

</html>