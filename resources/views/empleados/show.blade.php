@extends('layouts.app')

@section('title', 'Detalles del Empleado')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-user-tie text-primary me-2"></i>
        Detalles del Empleado
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('empleados.index') }}">Empleados</a></li>
        <li class="breadcrumb-item active">{{ $empleado->user->name ?? 'Empleado' }}</li>
    </ol>

    <!-- Información Principal del Empleado -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        Información Personal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-user text-white fa-2x"></i>
                        </div>
                    </div>
                    
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nombre:</strong></td>
                            <td>{{ $empleado->user->name ?? 'N/A' }} {{ $empleado->user->primerApellido ?? '' }}</td>
                        </tr>
                        @if($empleado->user && $empleado->user->segundApellido)
                        <tr>
                            <td><strong>Segundo Apellido:</strong></td>
                            <td>{{ $empleado->user->segundApellido }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>CI:</strong></td>
                            <td>{{ $empleado->user->ci ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $empleado->user->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Teléfono:</strong></td>
                            <td>{{ $empleado->user->telefono ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-briefcase me-2"></i>
                        Información Laboral
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-id-badge me-2"></i>Cargo</h6>
                            <p class="text-muted">{{ $empleado->cargo }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-user-tag me-2"></i>Rol</h6>
                            @php
                                $rolColors = [
                                    'administrador' => 'danger',
                                    'vendedor' => 'success',
                                    'diseñador' => 'warning',
                                    'operador' => 'info',
                                    'cliente' => 'secondary'
                                ];
                                $color = $rolColors[$empleado->rol] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }} fs-6">{{ ucfirst($empleado->rol) }}</span>
                        </div>
                        <div class="col-md-6 mt-3">
                            <h6><i class="fas fa-calendar-plus me-2"></i>Fecha de Registro</h6>
                            <p class="text-muted">{{ $empleado->created_at ? $empleado->created_at->format('d/m/Y H:i') : 'No disponible' }}</p>
                        </div>
                        <div class="col-md-6 mt-3">
                            <h6><i class="fas fa-toggle-on me-2"></i>Estado</h6>
                            @if($empleado->estado)
                                <span class="badge bg-success fs-6">Activo</span>
                            @else
                                <span class="badge bg-danger fs-6">Inactivo</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Botones de Acción -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Volver a la Lista
                </a>
                <div>
                    <a href="{{ route('empleados.edit', $empleado) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-2"></i>
                        Editar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($estadisticas['ventas_por_mes']) && $estadisticas['ventas_por_mes']->count() > 0)
    // Gráfico de ventas por mes
    const ctx = document.getElementById('ventasPorMesChart').getContext('2d');
    const ventasPorMes = @json($estadisticas['ventas_por_mes']);
    
    const labels = ventasPorMes.map(item => {
        const fecha = new Date(item.año, item.mes - 1);
        return fecha.toLocaleDateString('es-ES', { year: 'numeric', month: 'short' });
    });
    
    const data = ventasPorMes.map(item => parseFloat(item.total));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ventas ($)',
                data: data,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Ventas: $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    @endif
});
</script>
@endpush
@endsection
