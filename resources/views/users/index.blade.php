@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Usuarios</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol>

    {{-- Mensajes de éxito o error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Mensaje específico para eliminación exitosa --}}
    @if(session('successdelete'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-trash me-2"></i>
            {{ session('successdelete') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Estadísticas rápidas --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="h4 mb-0">{{ $users->where('clienteNatural')->count() }}</div>
                            <div>Clientes Naturales</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user fa-2x"></i>
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
                            <div class="h4 mb-0">{{ $users->where('clienteEstablecimiento')->count() }}</div>
                            <div>Establecimientos</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-building fa-2x"></i>
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
                            <div class="h4 mb-0">{{ $users->where('empleado')->count() }}</div>
                            <div>Empleados</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-tie fa-2x"></i>
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
                            <div class="h4 mb-0">{{ $users->count() }}</div>
                            <div>Total Usuarios</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-users me-1"></i>
                    Lista de Usuarios
                </div>
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>
                    Nuevo Usuario
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="usersTable">
                        <thead class="table-dark">
                            <tr>
                                <th>CI</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Tipo de Usuario</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded">{{ $user->ci }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $user->nombre_completo }}</strong>
                                        @if($user->email_verified_at)
                                            <br><small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>Email verificado
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                            {{ $user->email }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($user->telefono)
                                            <a href="tel:{{ $user->telefono }}" class="text-decoration-none">
                                                {{ $user->telefono }}
                                            </a>
                                        @else
                                            <span class="text-muted">No registrado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->clienteNatural)
                                            <span class="badge bg-primary">
                                                <i class="fas fa-user me-1"></i>Cliente Natural
                                            </span>
                                        @elseif($user->clienteEstablecimiento)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-building me-1"></i>Establecimiento
                                            </span>
                                        @elseif($user->empleado)
                                            <span class="badge bg-success">
                                                <i class="fas fa-user-tie me-1"></i>Empleado
                                            </span>
                                            @if($user->empleado->cargo)
                                                <br><small class="text-muted">{{ $user->empleado->cargo }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Usuario Base</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->estado == 1)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Activo
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'No disponible' }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('users.show', $user->idUser) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user->idUser) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Botón Eliminar con Formulario DELETE -->
                                            <form action="{{ route('users.destroy', $user->idUser) }}" 
                                                  method="POST" 
                                                  style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-sm"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de que deseas eliminar el usuario {{ addslashes($user->nombre_completo) }}? Se marcará como inactivo.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay usuarios registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer usuario</p>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Crear Primer Usuario
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inicializar DataTable
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "responsive": true,
            "order": [[ 1, "asc" ]], // Ordenar por nombre
            "pageLength": 25
        });
    });
</script>
@endpush
