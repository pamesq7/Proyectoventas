@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">🛠️ Gestión de Opciones</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Opciones</li>
    </ol>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small">Total Opciones</div>
                            <div class="h4">{{ $opciones->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cogs fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small">Opciones Activas</div>
                            <div class="h4">{{ $opciones->where('estado', 1)->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small">Total Características</div>
                            <div class="h4">{{ $opciones->sum(function($opcion) { return $opcion->caracteristicas->count(); }) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small">Opciones Inactivas</div>
                            <div class="h4">{{ $opciones->where('estado', 0)->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-pause-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Opciones -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    Lista de Opciones Maestras
                </div>
                <a href="{{ route('opciones.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Nueva Opción
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($opciones->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="opcionesTable">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Características</th>
                            <th>Estado</th>
                            <th>Creado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opciones as $opcion)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-cog me-2 text-primary"></i>
                                    <div>
                                        <strong>{{ $opcion->nombre }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $opcion->idOpcion }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ Str::limit($opcion->descripcion, 50) }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-info me-2">{{ $opcion->caracteristicas->count() }}</span>
                                    @if($opcion->caracteristicas->count() > 0)
                                    <small class="text-success">
                                        {{ $opcion->caracteristicas->where('estado', 1)->count() }} activas
                                    </small>
                                    @else
                                    <small class="text-muted">Sin características</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $opcion->estado ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $opcion->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $opcion->created_at ? $opcion->created_at->format('d/m/Y') : 'N/A' }}
                                    <br>
                                    {{ $opcion->created_at ? $opcion->created_at->format('H:i') : 'N/A' }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('opciones.show', ['opcion' => $opcion->idOpcion]) }}" 
                                       class="btn btn-outline-info" 
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('opciones.edit', ['opcion' => $opcion->idOpcion]) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($opcion->estado)
                                    <form action="{{ route('opciones.toggleEstado', ['opcion' => $opcion->idOpcion]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="btn btn-outline-warning" 
                                                title="Desactivar"
                                                onclick="return confirm('¿Desactivar esta opción?')">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('opciones.toggleEstado', ['opcion' => $opcion->idOpcion]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="btn btn-outline-success" 
                                                title="Activar">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                    @endif

                                    @if($opcion->caracteristicas->count() == 0)
                                    <form action="{{ route('opciones.destroy', ['opcion' => $opcion->idOpcion]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-outline-danger" 
                                                title="Eliminar"
                                                onclick="return confirm('¿Estás seguro de eliminar esta opción?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn btn-outline-secondary" 
                                            title="No se puede eliminar (tiene características)" 
                                            disabled>
                                        <i class="fas fa-lock"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-cogs fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No hay opciones registradas</h4>
                <p class="text-muted">Comienza creando tu primera opción maestra</p>
                <a href="{{ route('opciones.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Crear Primera Opción
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#opcionesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[ 0, "asc" ]],
        "pageLength": 25
    });
});
</script>
@endpush