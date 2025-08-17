@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">👁️ Detalles de Opción</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('opciones.index') }}">Opciones</a></li>
        <li class="breadcrumb-item active">{{ $opcion->nombre }}</li>
    </ol>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>
                            {{ $opcion->nombre }}
                        </h5>
                        <span class="badge {{ $opcion->estado ? 'bg-success' : 'bg-secondary' }} fs-6">
                            {{ $opcion->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-align-left me-1"></i>
                                Descripción
                            </h6>
                            <p class="mb-0">{{ $opcion->descripcion }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Información del Sistema
                            </h6>
                            <div class="small">
                                <div><strong>ID:</strong> {{ $opcion->id }}</div>
                                <div><strong>Creado:</strong> {{ $opcion->created_at ? $opcion->created_at ? $opcion->created_at->: 'No disponible' format('d/m/Y H:i:s') : 'No disponible' }}</div>
                                <div><strong>Actualizado:</strong> {{ $opcion->updated_at ? $opcion->updated_at ? $opcion->updated_at- :>'No disponible' format('d/m/Y H:i:s') : 'No disponible' }}</div>
                                @if($opcion->created_at && $opcion->updated_at && $opcion->created_at && $opcion->updated_at && $opcion->created_at != $opcion->updated_at)
                                <div class="text-info">
                                    <i class="fas fa-clock me-1"></i>
                                    Última modificación hace {{ $opcion->updated_at->diffForHumans() }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('opciones.edit', ['opcion' => $opcion->idOpcion]) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>
                            Editar
                        </a>
                        
                        @if($opcion->estado)
                        <form action="{{ route('opciones.toggleEstado', ['opcion' => $opcion->idOpcion]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning" 
                                    onclick="return confirm('¿Desactivar esta opción? También se desactivarán sus características.')">
                                <i class="fas fa-pause me-1"></i>
                                Desactivar
                            </button>
                        </form>
                        @else
                        <form action="{{ route('opciones.toggleEstado', ['opcion' => $opcion->idOpcion]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-play me-1"></i>
                                Activar
                            </button>
                        </form>
                        @endif

                        @if($opcion->caracteristicas->count() == 0)
                        <form action="{{ route('opciones.destroy', ['opcion' => $opcion->idOpcion]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('¿Estás seguro de eliminar esta opción?')">
                                <i class="fas fa-trash me-1"></i>
                                Eliminar
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('opciones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Volver a la Lista
                        </a>
                    </div>
                </div>
            </div>

            <!-- Características Asociadas -->
            <div class="card mt-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Características ({{ $opcion->caracteristicas->count() }})
                        </h6>
                        @if($opcion->estado)
                        <a href="{{ route('caracteristicas.create') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-1"></i>
                            Agregar Característica
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($opcion->caracteristicas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Creado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($opcion->caracteristicas as $caracteristica)
                                <tr>
                                    <td>
                                        <strong>{{ $caracteristica->nombre }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($caracteristica->descripcion, 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $caracteristica->estado ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $caracteristica->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $caracteristica->created_at ? $caracteristica->created_at->format('d/m/Y') : 'No disponible' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Gestionar desde Configuración
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-list-ul fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay características</h5>
                        <p class="text-muted">Esta opción aún no tiene características asociadas.</p>
                        @if($opcion->estado)
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCaracteristicaModal">
                            <i class="fas fa-plus me-1"></i>
                            Agregar Primera Característica
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="col-lg-4">
            <!-- Estadísticas Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estadísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ $opcion->caracteristicas->count() }}</h4>
                                <small class="text-muted">Total Características</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-1">{{ $opcion->caracteristicas->where('estado', 1)->count() }}</h4>
                            <small class="text-muted">Activas</small>
                        </div>
                    </div>
                    
                    @if($opcion->caracteristicas->count() > 0)
                    <hr>
                    <div class="row text-center">
                        <div class="col-12">
                            <div class="progress mb-2" style="height: 8px;">
                                @php
                                    $porcentajeActivas = ($opcion->caracteristicas->where('estado', 1)->count() / $opcion->caracteristicas->count()) * 100;
                                @endphp
                                <div class="progress-bar bg-success" style="width: {{ $porcentajeActivas }}%"></div>
                            </div>
                            <small class="text-muted">{{ number_format($porcentajeActivas, 1) }}% Activas</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Uso en Productos -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        Uso en Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Variantes que usan esta opción:</span>
                        <span class="badge bg-info">0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Productos afectados:</span>
                        <span class="badge bg-warning">0</span>
                    </div>
                    <hr>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Esta información se actualizará cuando se implementen las variantes de productos.
                    </small>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('opciones.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            Nueva Opción
                        </a>
                        <a href="{{ route('opciones.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list me-1"></i>
                            Ver Todas las Opciones
                        </a>
                        @if($opcion->caracteristicas->count() > 0)
                        <button class="btn btn-outline-info btn-sm" onclick="exportarCaracteristicas()">
                            <i class="fas fa-download me-1"></i>
                            Exportar Características
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Confirmaciones para formularios
    $('form[method="POST"]').on('submit', function(e) {
        const form = $(this);
        const action = form.attr('action');
        
        if (action.includes('toggle')) {
            const confirmText = '¿Estás seguro de cambiar el estado de esta característica?';
            if (!confirm(confirmText)) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
@endpush
