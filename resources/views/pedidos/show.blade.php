@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Detalle del Pedido</h1>
        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="row">
        <!-- Información del Pedido -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Información del Pedido #{{ $pedido->idVenta }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Fecha de Pedido:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Fecha de Entrega:</strong> 
                                {{ $pedido->fechaEntrega ? \Carbon\Carbon::parse($pedido->fechaEntrega)->format('d/m/Y') : 'Pendiente' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            @php
                                $estados = [
                                    '0' => ['nombre' => 'En Diseño', 'clase' => 'bg-info'],
                                    '1' => ['nombre' => 'Producción', 'clase' => 'bg-warning'],
                                    '2' => ['nombre' => 'Terminado', 'clase' => 'bg-success'],
                                    '3' => ['nombre' => 'Entregado', 'clase' => 'bg-primary'],
                                    '4' => ['nombre' => 'Cancelado', 'clase' => 'bg-danger']
                                ];
                                $estado = $estados[$pedido->estadoPedido ?? '0'];
                            @endphp
                            <p><strong>Estado:</strong> 
                                <span class="badge {{ $estado['clase'] }}">{{ $estado['nombre'] }}</span>
                            </p>
                            <p><strong>Total:</strong> Bs. {{ number_format($pedido->total, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos del Pedido -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Productos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unitario</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedido->detalleVentas as $detalle)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($detalle->producto && $detalle->producto->foto)
                                            <img src="{{ asset('storage/' . $detalle->producto->foto) }}" 
                                                 class="img-thumbnail me-3" 
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                            <div class="bg-light d-flex align-items-center justify-content-center me-3"
                                                 style="width: 60px; height: 60px;">
                                                <i class="fas fa-box-open text-muted"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">{{ $detalle->producto->nombre ?? 'Producto no especificado' }}</h6>
                                                @if($detalle->detalleTallas->isNotEmpty())
                                                <div class="mt-1">
                                                    @foreach($detalle->detalleTallas as $dt)
                                                    <span class="badge bg-primary me-1">
                                                        {{ $dt->talla->nombre }}: {{ $dt->cantidad }}
                                                    </span>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                    <td class="text-end">Bs. {{ number_format($detalle->precioUnitario, 2) }}</td>
                                    <td class="text-end">Bs. {{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Cliente y Resumen -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Cliente</h5>
                </div>
                <div class="card-body">
                    @php
                        $nombreCliente = 'Cliente no especificado';
                        $telefono = 'N/A';
                        $tipoCliente = 'Natural';
                        
                        if ($pedido->clienteNatural && $pedido->clienteNatural->user) {
                            $user = $pedido->clienteNatural->user;
                            $nombreCliente = trim($user->name . ' ' . ($user->primerApellido ?? ''));
                            $telefono = $user->telefono ?? 'N/A';
                        } elseif ($pedido->clienteEstablecimiento) {
                            $tipoCliente = 'Establecimiento';
                            $nombreCliente = $pedido->clienteEstablecimiento->razonSocial;
                            $telefono = $pedido->clienteEstablecimiento->telefono ?? 'N/A';
                        }
                    @endphp
                    
                    <h6 class="mb-3">{{ $nombreCliente }}</h6>
                    <p class="mb-1"><i class="fas fa-user-tag me-2 text-muted"></i> {{ $tipoCliente }}</p>
                    <p class="mb-1"><i class="fas fa-phone me-2 text-muted"></i> {{ $telefono }}</p>
                    @if($pedido->lugarEntrega)
                    <p class="mb-0"><i class="fas fa-map-marker-alt me-2 text-muted"></i> {{ $pedido->lugarEntrega }}</p>
                    @endif
                </div>
            </div>

            <!-- Estado de Pago -->
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Estado de Pago</h5>
                    @php
                        $estadoPago = 'Pendiente';
                        $claseBadge = 'bg-warning';
                        if ($pedido->estadoPago === 'pagado') {
                            $estadoPago = 'Pagado';
                            $claseBadge = 'bg-success';
                        } elseif ($pedido->estadoPago === 'parcial') {
                            $estadoPago = 'Parcial';
                            $claseBadge = 'bg-info';
                        }
                    @endphp
                    <span class="badge {{ $claseBadge }}">{{ $estadoPago }}</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total:</span>
                        <strong>Bs. {{ number_format($pedido->total, 2) }}</strong>
                    </div>
                    @if($pedido->estadoPago === 'parcial')
                        @php
                            $montoPagado = $pedido->transacciones->sum('monto');
                            $saldoPendiente = $pedido->total - $montoPagado;
                        @endphp
                        <div class="d-flex justify-content-between mb-2">
                            <span>Pagado:</span>
                            <strong>Bs. {{ number_format($montoPagado, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Saldo Pendiente:</span>
                            <strong class="text-danger">Bs. {{ number_format($saldoPendiente, 2) }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Diseños -->
            @if(isset($pedido->disenos) && $pedido->disenos->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Diseños</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($pedido->disenos as $diseno)
                        @if($diseno->archivo)
                        <div class="col-6">
                            <a href="{{ asset('storage/' . $diseno->archivo) }}" 
                               data-lightbox="disenos-{{ $pedido->idVenta }}"
                               data-title="Diseño para pedido #{{ $pedido->idVenta }}">
                                <img src="{{ asset('storage/' . $diseno->archivo) }}" 
                                     class="img-thumbnail w-100" 
                                     style="height: 120px; object-fit: cover;">
                            </a>
                            <div class="text-center small mt-1">
                                @if($diseno->empleado && $diseno->empleado->user)
                                <div class="text-muted">Por: {{ $diseno->empleado->user->name }}</div>
                                @endif
                                <div class="text-muted">{{ $diseno->created_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
    }
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
    }
    .table th {
        border-top: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .img-thumbnail {
        border-radius: 0.5rem;
        transition: transform 0.2s;
    }
    .img-thumbnail:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@push('scripts')
<!-- Lightbox2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
<!-- Lightbox2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    // Inicializar lightbox
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'showImageNumberLabel': false
    });
</script>
@endpush
@endsection