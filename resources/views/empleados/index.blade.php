@extends('layouts.app')

@section('title', 'Gestión de Empleados')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-user-tie text-primary me-2"></i>
        Gestión de Empleados
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Empleados</li>
    </ol>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Empleados</div>
                            <div class="h5 mb-0">{{ $estadisticas['total'] }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-users"></i>
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
                            <div class="small text-white-50">Activos</div>
                            <div class="h5 mb-0">{{ $estadisticas['activos'] }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-check-circle"></i>
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
                            <div class="small text-white-50">Administradores</div>
                            <div class="h5 mb-0">{{ $estadisticas['administradores'] }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-user-shield"></i>
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
                            <div class="small text-white-50">Vendedores</div>
                            <div class="h5 mb-0">{{ $estadisticas['vendedores'] }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-handshake"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Empleados -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Lista de Empleados
            </div>
            <div>
                <a href="{{ route('empleados.create') }}" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-plus me-1"></i> Nuevo Empleado
                </a>
                <a href="{{ route('export.empleados.pdf') }}" class="btn btn-success btn-sm" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i>
                    Exportar PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($empleados->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>N°</th>
                            <th>Empleado</th>
                            <th>Cargo</th>
                            <th>Rol</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $contador = ($empleados->currentPage() - 1) * $empleados->perPage() + 1; @endphp
                        @foreach($empleados as $empleado)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $empleado->user->name ?? 'N/A' }} {{ $empleado->user->primerApellido ?? '' }}</strong>
                                        <br>
                                        <small class="text-muted">CI: {{ $empleado->user->ci ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $empleado->cargo }}</span>
                            </td>
                            <td>
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
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst($empleado->rol) }}
                                </span>
                            </td>
                            <td>
                                @if($empleado->user && $empleado->user->email)
                                <div>
                                    <i class="fas fa-envelope text-muted me-1"></i>
                                    <small>{{ $empleado->user->email }}</small>
                                </div>
                                @endif
                                @if($empleado->user && $empleado->user->telefono)
                                <div>
                                    <i class="fas fa-phone text-muted me-1"></i>
                                    <small>{{ $empleado->user->telefono }}</small>
                                </div>
                                @endif
                            </td>
                            <td>
                                @if($empleado->estado)
                                <span class="badge bg-success">Activo</span>
                                @else
                                <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $empleado->created_at ? $empleado->created_at->format('d/m/Y') : 'No disponible' }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('empleados.show', $empleado) }}"
                                        class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('empleados.edit', $empleado) }}"
                                        class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('empleados.toggleEstado', $empleado) }}"
                                        method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="btn btn-{{ $empleado->estado ? 'secondary' : 'success' }} btn-sm"
                                            title="{{ $empleado->estado ? 'Desactivar' : 'Activar' }}">
                                            <i class="fas fa-{{ $empleado->estado ? 'times' : 'check' }}"></i>
                                        </button>
                                    </form>
                                    @if($empleado->user)
                                        <form action="{{ route('users.destroy', ['user' => $empleado->user->idUser]) }}"
                                            method="POST" style="display:inline;" 
                                            onsubmit="return confirm('¿Estás seguro de eliminar este empleado? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                title="Eliminar empleado">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-danger btn-sm" disabled 
                                            title="No se puede eliminar - Usuario no encontrado">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-3">
                {{ $empleados->links() }}
            </div>
            @else
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                No se encontraron empleados con los filtros aplicados.
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar este empleado? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formEliminar" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmarEliminacion(idEmpleado) {
        const form = document.getElementById('formEliminar');
        form.action = `{{ route('empleados.index') }}/${idEmpleado}`;
        const modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
        modal.show();
    }
</script>
@endpush
@endsection