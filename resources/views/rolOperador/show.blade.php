@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detalles del Pedido #{{ $pedido->idVenta }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.operador') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('rolOperador.index') }}">Pedidos</a></li>
        <li class="breadcrumb-item active">Pedido #{{ $pedido->idVenta }}</li>
    </ol>

    <div class="row">
        {{-- Información Principal del Pedido --}}
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
                            <div class="mb-3">
                                <label class="form-label text-muted">Cliente:</label>
                                <p class="mb-0">
                                    @php
                                    if ($pedido->clienteNatural && $pedido->clienteNatural->user) {
                                        $nombreCliente = trim($pedido->clienteNatural->user->name . ' ' .
                                        $pedido->clienteNatural->user->primerApellido . ' ' .
                                        ($pedido->clienteNatural->user->segundApellido ?? ''));
                                    } elseif ($pedido->clienteEstablecimiento) {
                                        $nombreCliente = $pedido->clienteEstablecimiento->razonSocial ??
                                        $pedido->clienteEstablecimiento->razonSocial ??
                                        'Establecimiento';
                                    } else {
                                        $nombreCliente = 'Cliente no especificado';
                                    }
                                    @endphp
                                    {{ $nombreCliente }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha de creación:</label>
                                <p class="mb-0">{{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Estado:</label>
                                <p class="mb-0">
                                    @php
                                        $estadoClases = [
                                            'pendiente' => 'warning',
                                            'procesando' => 'info',
                                            'completado' => 'success',
                                            'cancelado' => 'danger'
                                        ][$pedido->estado] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $estadoClases }}">
                                        {{ ucfirst($pedido->estado) }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Total:</label>
                                <p class="mb-0 fw-bold">${{ number_format($pedido->total, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($pedido->notas)
                    <div class="mt-3">
                        <label class="form-label text-muted">Notas:</label>
                        <p class="mb-0">{{ $pedido->notas }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Mostrar imagen del pedido si existe --}}
            @if ($pedido->disenos && $pedido->disenos->first() && $pedido->disenos->first()->archivo)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-image me-2"></i>
                        Imagen del Pedido
                    </h5>
                </div>
                <div class="card-body">
                    <img src="{{ asset('storage/' . $pedido->disenos->first()->archivo) }}" alt="Imagen del pedido" class="img-fluid" style="max-width: 100%; height: auto;">
                </div>
            </div>
            @endif

            {{-- Detalles del Pedido --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes me-2"></i>
                        Productos del Pedido
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pedido->detalleVentas as $detalle)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($detalle->producto && $detalle->producto->foto)
                                                <img src="{{ asset('storage/' . $detalle->producto->foto) }}"
                                                     alt="{{ $detalle->producto->nombre }}"
                                                     class="me-2"
                                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center me-2"
                                                     style="width: 40px; height: 40px; border-radius: 4px;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $detalle->producto->nombre ?? 'Producto no disponible' }}</div>
                                                @if($detalle->descripcion)
                                                    <small class="text-muted">{{ $detalle->descripcion }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">{{ $detalle->cantidad }}</td>
                                    <td class="text-end align-middle">${{ number_format($detalle->precioUnitario, 2) }}</td>
                                    <td class="text-end align-middle fw-semibold">${{ number_format($detalle->precioUnitario * $detalle->cantidad, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        No hay productos en este pedido
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal:</th>
                                    <th class="text-end">${{ number_format($pedido->subtotal, 2) }}</th>
                                </tr>
                                <tr class="table-active">
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end">${{ number_format($pedido->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Lateral --}}
        <div class="col-lg-4">
            {{-- Acciones --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Acciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($pedido->estado === 'pendiente')
                            <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#marcarComoCompletadoModal">
                                <i class="fas fa-check-circle me-1"></i> Marcar como Completado
                            </button>
                            <a href="{{ route('dashboard.operador') }}" class="btn btn-primary mb-2">
                                <i class="fas fa-edit me-1"></i> Editar Pedido
                            </a>
                            <button type="button" class="btn btn-danger mb-2" data-bs-toggle="modal" data-bs-target="#cancelarPedidoModal">
                                <i class="fas fa-times-circle me-1"></i> Cancelar Pedido
                            </button>
                        @elseif($pedido->estado === 'completado')
                            <a href="#" class="btn btn-success mb-2" disabled>
                                <i class="fas fa-check-double me-1"></i> Pedido Completado
                            </a>
                            <a href="{{ route('dashboard.operador') }}" class="btn btn-outline-primary mb-2">
                                <i class="fas fa-eye me-1"></i> Ver Detalles
                            </a>
                        @elseif($pedido->estado === 'cancelado')
                            <button type="button" class="btn btn-secondary mb-2" disabled>
                                <i class="fas fa-ban me-1"></i> Pedido Cancelado
                            </button>
                        @endif
                        
                        <a href="{{ route('rolOperador.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver a la lista
                        </a>
                    </div>
                </div>
            </div>

            {{-- Información del Cliente --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        Información del Cliente
                    </h5>
                </div>
                <div class="card-body">
                    @if($pedido->clienteNatural || $pedido->clienteEstablecimiento)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px; font-size: 1.25rem;">
                                    {{ substr($nombreCliente, 0, 1) }}
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">{{ $nombreCliente }}</h6>
                                <small class="text-muted">
                                    @if($pedido->clienteNatural)
                                        Cliente Natural
                                    @elseif($pedido->clienteEstablecimiento)
                                        Cliente Empresarial
                                    @else
                                        Cliente no especificado
                                    @endif
                                </small>
                            </div>
                        </div>
                        
                        <ul class="list-unstyled">
                            @php
                            $email = null;
                            $telefono = null;
                            $direccion = null;
                            if ($pedido->clienteNatural && $pedido->clienteNatural->user) {
                                $email = $pedido->clienteNatural->user->email;
                                $telefono = $pedido->clienteNatural->user->telefono;
                                $direccion = $pedido->clienteNatural->user->direccion;
                            } elseif ($pedido->clienteEstablecimiento) {
                                $email = $pedido->clienteEstablecimiento->email;
                                $telefono = $pedido->clienteEstablecimiento->telefono;
                                $direccion = $pedido->clienteEstablecimiento->direccion;
                            }
                            @endphp

                            @if($email)
                            <li class="mb-2">
                                <i class="fas fa-envelope me-2 text-muted"></i>
                                <a href="mailto:{{ $email }}" class="text-decoration-none">
                                    {{ $email }}
                                </a>
                            </li>
                            @endif

                            @if($telefono)
                            <li class="mb-2">
                                <i class="fas fa-phone me-2 text-muted"></i>
                                <a href="tel:{{ $telefono }}" class="text-decoration-none">
                                    {{ $telefono }}
                                </a>
                            </li>
                            @endif

                            @if($direccion)
                            <li class="mb-2">
                                <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                {{ $direccion }}
                            </li>
                            @endif
                        </ul>
                        
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                            <p class="mb-0">Cliente no especificado</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Historial de Estados --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Historial del Pedido
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @php
                            $estados = [
                                'pendiente' => ['icon' => 'clock', 'color' => 'warning', 'label' => 'Pendiente'],
                                'procesando' => ['icon' => 'cog', 'color' => 'info', 'label' => 'En Proceso'],
                                'completado' => ['icon' => 'check-circle', 'color' => 'success', 'label' => 'Completado'],
                                'cancelado' => ['icon' => 'times-circle', 'color' => 'danger', 'label' => 'Cancelado']
                            ];
                            
                            $currentState = $pedido->estado;
                            $currentStateIndex = array_search($currentState, array_keys($estados));
                        @endphp
                        
                        @foreach($estados as $key => $estado)
                            @php
                                $isActive = $key === $currentState;
                                $isCompleted = array_search($key, array_keys($estados)) < $currentStateIndex;
                                $isFuture = array_search($key, array_keys($estados)) > $currentStateIndex;
                            @endphp
                            
                            <div class="timeline-item {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }} {{ $isFuture ? 'future' : '' }}">
                                <div class="timeline-icon bg-{{ $estado['color'] }}">
                                    <i class="fas fa-{{ $estado['icon'] }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ $estado['label'] }}</h6>
                                    @if($isActive || $isCompleted)
                                        <small class="text-muted">
                                            {{ $pedido->updated_at->format('d/m/Y H:i') }}
                                        </small>
                                    @else
                                        <small class="text-muted">Pendiente</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
