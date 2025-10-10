@extends('layouts.app')

@section('title', 'Detalle del Pedido #' . $venta->idVenta)

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Encabezado -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mt-4">Pedido #{{ $venta->idVenta }}</h1>
                    <p class="text-muted">Fecha: {{ $venta->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <a href="{{ route('cliente.pedidos') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Historial
                </a>
            </div>

            <!-- Información de seguridad -->
            <div class="alert alert-success mb-4">
                <i class="fas fa-shield-alt me-2"></i>
                <strong>Modo seguro activado:</strong> Solo puedes ver detalles de tus propios pedidos.
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información General del Pedido -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Información del Pedido
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Estado del Pedido</h6>
                            @php
                            $estados = [
                                0 => ['texto' => 'Pendiente', 'color' => 'warning'],
                                1 => ['texto' => 'En proceso', 'color' => 'info'],
                                2 => ['texto' => 'Completado', 'color' => 'success'],
                                3 => ['texto' => 'Entregado', 'color' => 'primary']
                            ];
                            $estado = $estados[$venta->estadoPedido] ?? ['texto' => 'Desconocido', 'color' => 'secondary'];
                            @endphp
                            <span class="badge bg-{{ $estado['color'] }} fs-6">{{ $estado['texto'] }}</span>
                        </div>
                        <div class="col-md-6">
                            <h6>Estado de Pago</h6>
                            @php
                            $estado_pago = $venta->estado_pago ?? 'PENDIENTE';
                            $color_pago = match($estado_pago) {
                                'PAGADO' => 'success',
                                'PARCIAL' => 'warning',
                                'PENDIENTE' => 'danger',
                                default => 'secondary'
                            };
                            @endphp
                            <span class="badge bg-{{ $color_pago }}">{{ $estado_pago }}</span>
                        </div>
                    </div>

                    @if($venta->fechaEntrega)
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Fecha de Entrega Estimada</h6>
                            <p class="mb-0">{{ $venta->fechaEntrega->format('d/m/Y') }}</p>
                        </div>
                        @if($venta->lugarEntrega)
                        <div class="col-md-6">
                            <h6>Lugar de Entrega</h6>
                            <p class="mb-0">{{ $venta->lugarEntrega }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Productos del Pedido -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box me-2"></i>
                        Productos del Pedido
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Talla</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->detalleVentas as $detalle)
                                <tr>
                                    <td>
                                        <strong>{{ $detalle->producto->nombre ?? 'Producto no encontrado' }}</strong>
                                        @if($detalle->descripcion)
                                        <br><small class="text-muted">{{ $detalle->descripcion }}</small>
                                        @endif
                                        @if($detalle->nombrePersonalizado)
                                        <br><small class="text-info">Personalizado: {{ $detalle->nombrePersonalizado }}</small>
                                        @endif
                                        @if($detalle->numeroPersonalizado)
                                        <br><small class="text-info">Número: {{ $detalle->numeroPersonalizado }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($detalle->talla)
                                        <span class="badge bg-secondary">{{ $detalle->talla->nombre }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $detalle->cantidad }}</td>
                                    <td>${{ number_format($detalle->precioUnitario, 2) }}</td>
                                    <td>${{ number_format($detalle->cantidad * $detalle->precioUnitario, 2) }}</td>
                                    <td>
                                        @if($detalle->estado == 1)
                                        <span class="badge bg-success">Activo</span>
                                        @else
                                        <span class="badge bg-danger">Anulado</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <th colspan="4" class="text-end">TOTAL:</th>
                                    <th>${{ number_format($venta->total, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="col-lg-4">
            <!-- Resumen Financiero -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Resumen Financiero
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total del Pedido:</span>
                        <strong>${{ number_format($venta->total, 2) }}</strong>
                    </div>
                    @if($venta->saldo > 0)
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Saldo Pendiente:</span>
                        <strong>${{ number_format($venta->saldo, 2) }}</strong>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between text-success">
                        <span>Total Pagado:</span>
                        <strong>${{ number_format($venta->monto_pagado ?? ($venta->total - $venta->saldo), 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Historial de Pagos -->
            @if($venta->transacciones && $venta->transacciones->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt me-2"></i>
                        Historial de Pagos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->transacciones as $transaccion)
                                <tr>
                                    <td>{{ $transaccion->created_at->format('d/m/Y') }}</td>
                                    <td>${{ number_format($transaccion->monto, 2) }}</td>
                                    <td>
                                        @if($transaccion->metodoPago == 'efectivo')
                                        <span class="badge bg-success">Efectivo</span>
                                        @elseif($transaccion->metodoPago == 'transferencia')
                                        <span class="badge bg-info">Transferencia</span>
                                        @else
                                        <span class="badge bg-secondary">{{ ucfirst($transaccion->metodoPago) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
