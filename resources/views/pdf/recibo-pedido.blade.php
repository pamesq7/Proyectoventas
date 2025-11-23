<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Recibo de Pedido #{{ $pedido->idVenta }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #4a6da7;
        }

        .header h2 {
            color: #2c3e50;
            margin: 0;
            padding: 10px 0;
            font-size: 20px;
        }

        .info-cliente,
        .info-pedido {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8fafc;
            border-radius: 5px;
            border-left: 4px solid #4a6da7;
        }

        .info-cliente h3,
        .info-pedido h3 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }

        .table th {
            background-color: #4a6da7;
            color: white;
            padding: 10px;
            text-align: left;
        }

        .table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background-color: #f1f5f9 !important;
        }

        .signature {
            margin-top: 60px;
            text-align: center;
            width: 100%;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 60%;
            margin: 0 auto;
            padding-top: 10px;
        }

        .footer {
            margin-top: 50px;
            font-size: 10px;
            text-align: center;
            color: #666;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-box {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid #4a6da7;
        }

        .info-label {
            font-weight: bold;
            color: #4a6da7;
            margin-bottom: 5px;
            display: block;
        }

        .info-value {
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-0 {
            background-color: #e2e8f0;
            color: #4a5568;
        }

        /* En Diseño */
        .status-1 {
            background-color: #fff3cd;
            color: #856404;
        }

        /* En Producción */
        .status-2 {
            background-color: #c3e6cb;
            color: #155724;
        }

        /* Terminado */
        .status-3 {
            background-color: #d4edda;
            color: #155724;
        }

        /* Entregado */
        .status-4 {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Cancelado */

        .cliente-nombre {
            margin-bottom: 15px;
        }

        .cliente-nombre h4 {
            margin: 5px 0;
            color: #2c3e50;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .fw-bold {
            font-weight: 700 !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        .small {
            font-size: 0.875em;
        }

        .me-1 {
            margin-right: 0.25rem !important;
        }

        .mb-1 {
            margin-bottom: 0.25rem !important;
        }

        .mb-2 {
            margin-bottom: 0.5rem !important;
        }

        .d-flex {
            display: flex !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .align-items-start {
            align-items: flex-start !important;
        }

        .text-end {
            text-align: right !important;
        }

        .border-top {
            border-top: 1px solid #dee2e6 !important;
        }

        .pt-2 {
            padding-top: 0.5rem !important;
        }

        .mt-2 {
            margin-top: 0.5rem !important;
        }

        .fw-medium {
            font-weight: 500 !important;
        }

        .fa,
        .fas {
            font-weight: 900;
            width: 1.25em;
            text-align: center;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .text-primary {
            color: #0d6efd !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        /* Estilos para características de variantes */
        .producto-nombre {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .tallas {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .talla {
            background-color: #f1f5f9;
            padding: 1px 5px;
            border-radius: 3px;
            margin-right: 3px;
            font-size: 10px;
        }

        .caracteristicas {
            margin-top: 5px;
        }

        .caracteristicas-lista {
            list-style: none;
            padding: 0;
            margin: 5px 0 0 0;
            font-size: 11px;
        }

        .caracteristicas-lista li {
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .caracteristicas-lista li strong {
            color: #4a6da7;
        }

        .precio-extra {
            font-size: 10px;
            color: #28a745;
            margin-left: 5px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>RECIBO DE PEDIDO</h2>
        <p>N° {{ $pedido->idVenta }} - Fecha: {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y') }}</p>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <span class="info-label">INFORMACIÓN DEL CLIENTE</span>
            @php
            $clienteNombre = 'Cliente no especificado';
            $clienteDocumento = 'N/A';
            $clienteTelefono = 'N/A';
            $clienteEmail = 'N/A';
            $clienteDireccion = 'No especificada';
            $clienteTipo = 'No especificado';
            $clienteRepresentante = '';

            // Si es cliente natural
            if($pedido->idCliente) {
            $clienteNatural = \App\Models\ClienteNatural::with('user')->find($pedido->idCliente);
            if($clienteNatural && $clienteNatural->user) {
            $user = $clienteNatural->user;
            $clienteTipo = 'Cliente Natural';
            $clienteNombre = trim(implode(' ', array_filter([
            $user->name,
            $user->primerApellido,
            $user->segundApellido
            ])));
            $clienteDocumento = $user->ci ?? 'N/A';
            $clienteTelefono = $user->telefono ?? 'N/A';
            $clienteEmail = $user->email ?? 'N/A';
            $clienteDireccion = $user->direccion ?? 'No especificada';
            }
            }
            // Si es establecimiento
            elseif($pedido->idEstablecimiento) {
            $establecimiento = \App\Models\ClienteEstablecimiento::with('user')->find($pedido->idEstablecimiento);
            if($establecimiento) {
            $clienteTipo = 'Establecimiento';
            $clienteNombre = $establecimiento->razonSocial ?? 'N/A';
            $clienteDocumento = $establecimiento->nit ? 'NIT: ' . $establecimiento->nit : 'N/A';
            $clienteTelefono = $establecimiento->telefono ?? 'N/A';
            $clienteEmail = $establecimiento->email ?? 'N/A';
            $clienteDireccion = $establecimiento->domicilioFiscal ?? 'No especificada';

            // Si hay un representante
            if(isset($establecimiento->user)) {
            $clienteRepresentante = 'Rep. Legal: ' . trim(implode(' ', array_filter([
            $establecimiento->user->name,
            $establecimiento->user->primerApellido,
            $establecimiento->user->segundApellido
            ])));
            }
            }
            }

            // Obtener detalles de la dirección
            $direccionCompleta = $pedido->lugarEntrega ?? 'No especificada';
            $ubicacion = [];

            if(isset($pedido->direccion) && $pedido->direccion) {
            $direccion = $pedido->direccion;
            $direccionCompleta = $direccion->nombreDireccion ?? $direccionCompleta;

            // Cargar relaciones necesarias
            $direccion->load(['municipio.provincia.departamento']);

            if($direccion->municipio) {
            $municipio = $direccion->municipio;
            $provincia = $municipio->provincia ?? null;
            $departamento = $provincia->departamento ?? null;

            // Construir array de ubicación
            if($municipio->nombreMunicipio) $ubicacion[] = $municipio->nombreMunicipio;
            if($provincia && $provincia->nombreProvincia) $ubicacion[] = $provincia->nombreProvincia;
            if($departamento && $departamento->nombreDepartamento) $ubicacion[] = $departamento->nombreDepartamento;
            }
            }

            // Si no hay dirección en la tabla direcciones, usar los campos directos del pedido
            if(empty($ubicacion)) {
            if($pedido->municipio) $ubicacion[] = $pedido->municipio;
            if($pedido->provincia) $ubicacion[] = $pedido->provincia;
            if($pedido->departamento) $ubicacion[] = $pedido->departamento;
            }

            // Si no hay ubicación, usar la dirección del cliente
            if(empty($ubicacion) && !empty($clienteDireccion)) {
            $direccionCompleta = $clienteDireccion;
            }

            $ubicacionTexto = !empty($ubicacion) ? implode(', ', array_reverse($ubicacion)) : '';
            @endphp

            <div class="info-value">
                <div class="cliente-nombre">
                    <div class="fw-bold text-uppercase small text-muted">NOMBRE COMPLETO DEL CLIENTE</div>
                    <h4 class="mb-1">{{ $clienteNombre }}</h4>
                    @if($pedido->tipoCliente === 'establecimiento' && !empty($clienteRepresentante))
                    <div class="text-muted small">{{ $clienteRepresentante }}</div>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="mb-1">
                            <span class="text-muted me-2">Tipo:</span>
                            <span class="badge bg-secondary">{{ $clienteTipo }}</span>
                        </div>
                        <div class="mb-1">
                            <span class="text-muted me-2">Documento:</span>
                            <span class="fw-medium">{{ $clienteDocumento }}</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="mb-1">
                            <i class="fas fa-phone me-1 text-muted"></i>
                            <span class="fw-medium">{{ $clienteTelefono }}</span>
                        </div>
                        @if($clienteEmail !== 'N/A')
                        <div class="mb-1">
                            <i class="fas fa-envelope me-1 text-muted"></i>
                            <span class="fw-medium">{{ $clienteEmail }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-2 pt-2 border-top">
                    <div class="fw-bold mb-1">Dirección de Entrega:</div>
                    <div class="d-flex align-items-start">
                        <div class="me-2">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                        </div>
                        <div>
                            <div class="fw-medium">{{ $direccionCompleta }}</div>
                            @if(!empty($ubicacionTexto))
                            <div class="text-muted small">
                                <i class="fas fa-location-arrow text-primary"></i>
                                {{ $ubicacionTexto }}
                            </div>
                            @elseif(empty($direccionCompleta) || $direccionCompleta === 'No especificada')
                            <div class="text-muted small">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                Dirección no especificada
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-box">
            <span class="info-label">DETALLES DEL PEDIDO</span>
            <div class="info-value">
                @php
                $estados = [
                '0' => ['text' => 'En Diseño', 'class' => 'status-0'],
                '1' => ['text' => 'En Producción', 'class' => 'status-1'],
                '2' => ['text' => 'Terminado', 'class' => 'status-2'],
                '3' => ['text' => 'Entregado', 'class' => 'status-3'],
                '4' => ['text' => 'Cancelado', 'class' => 'status-4']
                ];
                $estadoKey = (string)($pedido->estadoPedido ?? '0');
                $estado = $estados[$estadoKey] ?? $estados['0'];
                @endphp
                <div class="mb-1">
                    <span class="text-muted me-2">Estado:</span>
                    <span class="status-badge {{ $estado['class'] }}">{{ $estado['text'] }}</span>
                </div>

                <div class="mb-1">
                    <span class="text-muted me-2">Fecha de Entrega:</span>
                    <span class="fw-medium">{{ $pedido->fechaEntrega ? \Carbon\Carbon::parse($pedido->fechaEntrega)->format('d/m/Y') : 'Por definir' }}</span>
                </div>

                <div class="mb-1">
                    <span class="text-muted me-2">Método de Pago:</span>
                    <span class="fw-medium">{{ $pedido->metodo_pago ?? 'No especificado' }}</span>
                </div>

                <div class="mb-1">
                    <span class="text-muted me-2">Fecha de Registro:</span>
                    <span class="fw-medium">{{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <h3>DETALLE DEL PEDIDO</h3>
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
                <td>
                    <div class="producto-nombre">
                        {{ $item->producto->nombre ?? 'Producto no disponible' }}
                    </div>

                    @if(isset($item->detalleTallas) && $item->detalleTallas->isNotEmpty())
                    <div class="tallas">
                        <span class="text-muted">Tallas: </span>
                        @foreach($item->detalleTallas as $detalleTalla)
                        <span class="talla">{{ $detalleTalla->talla->nombre ?? '' }}{{ !$loop->last ? ',' : '' }}</span>
                        @endforeach
                    </div>
                    @endif
                </td>
                <td class="text-center">{{ $item->cantidad }}</td>
                <td class="text-right">Bs. {{ number_format($item->precio_unitario, 2) }}</td>
                <td class="text-right">Bs. {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right">Bs. {{ number_format($pedido->subtotal ?? 0, 2) }}</td>
            </tr>
            @if($pedido->descuento > 0)
            <tr class="total-row">
                <td colspan="3" class="text-right"><strong>Descuento:</strong></td>
                <td class="text-right">- Bs. {{ number_format($pedido->descuento, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row" style="background-color: #e8f0fe !important;">
                <td colspan="3" class="text-right"><strong>Total del Pedido:</strong></td>
                <td class="text-right">Bs. {{ number_format($pedido->total, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><strong>Adelanto:</strong></td>
                <td class="text-right">Bs. {{ number_format(($pedido->total - $pedido->saldo), 2) }}</td>
            </tr>
            @if($pedido->saldo > 0)
            <tr class="total-row" style="background-color: #fff3cd !important;">
                <td colspan="3" class="text-right"><strong>Saldo Pendiente:</strong></td>
                <td class="text-right">Bs. {{ number_format($pedido->saldo, 2) }}</td>
            </tr>
            @endif
        </tfoot>
    </table>

    @if($pedido->observaciones)
    <div class="info-box" style="margin-top: 20px;">
        <span class="info-label">OBSERVACIONES</span>
        <div class="info-value">{{ $pedido->observaciones }}</div>
    </div>
    @endif

    <div class="signature">
        <div class="signature-line"></div>
        <p style="margin-top: 5px;">Firma del Cliente</p>
    </div>

    <div class="footer">
        <p>Gracias por su preferencia - Este es un documento generado automáticamente</p>
        <p>Fecha de impresión: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>

</html>