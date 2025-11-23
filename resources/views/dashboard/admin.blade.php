@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Panel de Administración</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Usuarios</h5>
                                    <p class="card-text display-4">{{ App\Models\User::count() }}</p>
                                    <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">Ver más</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Ventas Hoy</h5>
                                    <p class="card-text display-4">{{ App\Models\Venta::whereDate('fechaEntrega', today())->count() }}</p>
                                    <a href="{{ route('ventas.index') }}" class="btn btn-light btn-sm">Ver más</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Productos</h5>
                                    <p class="card-text display-4">{{ App\Models\Producto::count() }}</p>
                                    <a href="{{ route('productos.index') }}" class="btn btn-dark btn-sm">Ver más</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Ventas Recientes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>N°</th>
                                                    <th>Fecha</th>
                                                    <th>Total</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse (App\Models\Venta::latest()->take(5)->get() as $venta)
                                                <tr>
                                                    {{-- Número correlativo --}}
                                                    <td>{{ $loop->iteration }}</td>

                                                    {{-- Fecha de entrega segura --}}
                                                    <td>
                                                        <i class="fas fa-calendar-day text-primary me-1"></i>
                                                        {{ $venta->fechaEntrega
                    ? \Carbon\Carbon::parse($venta->fechaEntrega)->format('d/m/Y')
                    : 'Sin fecha'
                }}
                                                    </td>

                                                    {{-- Total --}}
                                                    <td>
                                                        <i class="fas fa-dollar-sign text-success me-1"></i>
                                                        <strong>{{ number_format($venta->total, 2) }} Bs.</strong>
                                                    </td>

                                                    {{-- Estado visual --}}
                                                    @php
                                                    $estados = [
                                                    0 => ['label' => 'Solicitado', 'badge' => 'secondary', 'icon' => 'fas fa-clipboard'],
                                                    1 => ['label' => 'En Diseño', 'badge' => 'info', 'icon' => 'fas fa-paint-brush'],
                                                    2 => ['label' => 'Confección', 'badge' => 'warning', 'icon' => 'fas fa-tshirt'],
                                                    3 => ['label' => 'Entregado', 'badge' => 'success', 'icon' => 'fas fa-check-circle'],
                                                    ];

                                                    $estado = $estados[$venta->estado] ?? [
                                                    'label' => 'Desconocido',
                                                    'badge' => 'dark',
                                                    'icon' => 'fas fa-question-circle'
                                                    ];
                                                    @endphp

                                                    <td>
                                                        <span class="badge bg-{{ $estado['badge'] }} px-3 py-2">
                                                            <i class="{{ $estado['icon'] }} me-1"></i>
                                                            {{ $estado['label'] }}
                                                        </span>
                                                    </td>
                                                </tr>

                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-3 text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        No hay ventas recientes
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Actividad Reciente</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Nuevo usuario registrado</span>
                                            <small class="text-muted">Hace 5 min</small>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Venta completada #1234</span>
                                            <small class="text-muted">Hace 2 horas</small>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Producto actualizado: Camiseta Roja</span>
                                            <small class="text-muted">Ayer</small>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Nuevo pedido recibido</span>
                                            <small class="text-muted">Ayer</small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection