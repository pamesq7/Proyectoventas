<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Pedidos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stat-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            display: block;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        
        .financial-stats {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
        }
        
        .financial-stats h3 {
            margin: 0 0 10px 0;
            color: #28a745;
            font-size: 14px;
        }
        
        .financial-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        
        .table-container {
            margin-top: 20px;
        }
        
        .table-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }
        
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .badge-info { background-color: #17a2b8; }
        .badge-secondary { background-color: #6c757d; }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Pedidos</h1>
        <p>Generado el {{ date('d/m/Y H:i:s') }}</p>
        <p>Total de registros: {{ $estadisticas['total_pedidos'] }}</p>
    </div>

    <!-- Estadísticas por Estado -->
    <div class="stats-grid">
        <div class="stats-row">
            <div class="stat-item">
                <span class="stat-number">{{ $estadisticas['pedidos_pendientes'] }}</span>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $estadisticas['pedidos_en_proceso'] }}</span>
                <div class="stat-label">En Proceso</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $estadisticas['pedidos_completados'] }}</span>
                <div class="stat-label">Completados</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $estadisticas['pedidos_cancelados'] }}</span>
                <div class="stat-label">Cancelados</div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Financieras -->
    <div class="financial-stats">
        <h3>Resumen Financiero</h3>
        <div class="financial-row">
            <span>Total en Ventas:</span>
            <span><strong>${{ number_format($estadisticas['total_ventas'], 2) }}</strong></span>
        </div>
        <div class="financial-row">
            <span>Total Pagado:</span>
            <span><strong>${{ number_format($estadisticas['total_pagado'], 2) }}</strong></span>
        </div>
        <div class="financial-row">
            <span>Saldo Pendiente:</span>
            <span><strong>${{ number_format($estadisticas['saldo_pendiente'], 2) }}</strong></span>
        </div>
    </div>

    <!-- Tabla de Pedidos -->
    @if($pedidos->count() > 0)
        <div class="table-container">
            <div class="table-title">Lista Detallada de Pedidos</div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Empleado</th>
                        <th>Total</th>
                        <th>Saldo</th>
                        <th>Estado Pedido</th>
                        <th>Estado Pago</th>
                        <th>Fecha Entrega</th>
                        <th>Lugar Entrega</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos as $pedido)
                        <tr>
                            <td>{{ $pedido->idVenta }}</td>
                            <td>
                                @if($pedido->clienteNatural)
                                    {{ $pedido->clienteNatural->user->name ?? 'N/A' }}
                                    {{ $pedido->clienteNatural->user->primerApellido ?? '' }}
                                    <br><small>(Natural)</small>
                                @elseif($pedido->clienteEstablecimiento)
                                    {{ $pedido->clienteEstablecimiento->razonSocial ?? 'N/A' }}
                                    <br><small>(Establecimiento)</small>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                {{ $pedido->empleado->user->name ?? 'N/A' }}
                                {{ $pedido->empleado->user->primerApellido ?? '' }}
                            </td>
                            <td class="text-right">${{ number_format($pedido->total, 2) }}</td>
                            <td class="text-right">${{ number_format($pedido->saldo, 2) }}</td>
                            <td>
                                @php
                                    $estadoBadge = 'secondary';
                                    if ($pedido->estadoPedido == 'completado') {
                                        $estadoBadge = 'success';
                                    } elseif ($pedido->estadoPedido == 'en proceso') {
                                        $estadoBadge = 'warning';
                                    } elseif ($pedido->estadoPedido == 'cancelado') {
                                        $estadoBadge = 'danger';
                                    } elseif ($pedido->estadoPedido == 'pendiente') {
                                        $estadoBadge = 'info';
                                    }
                                @endphp
                                <span class="badge badge-{{ $estadoBadge }}">
                                    {{ ucfirst($pedido->estadoPedido) }}
                                </span>
                            </td>
                            <td>
                                @if($pedido->saldo == 0)
                                    <span class="badge badge-success">Pagado</span>
                                @elseif($pedido->saldo == $pedido->total)
                                    <span class="badge badge-danger">Pendiente</span>
                                @else
                                    <span class="badge badge-warning">Parcial</span>
                                @endif
                            </td>
                            <td>{{ $pedido->fechaEntrega ? date('d/m/Y', strtotime($pedido->fechaEntrega)) : 'N/A' }}</td>
                            <td>{{ $pedido->lugarEntrega ?? 'N/A' }}</td>
                            <td>{{ $pedido->created_at ? $pedido->created_at->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="no-data">
            <p>No se encontraron pedidos con los criterios especificados.</p>
        </div>
    @endif

    <div class="footer">
        <p>Sistema de Gestión de Ventas Deportivas - Reporte generado automáticamente</p>
    </div>
</body>
</html>
