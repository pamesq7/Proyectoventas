@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detalles del Usuario</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
        <li class="breadcrumb-item active">{{ $user->nombre_completo }}</li>
    </ol>

    <div class="row">
        {{-- Información Principal del Usuario --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        {{ $user->nombre_completo }}
                        @if($user->estado == 1)
                            <span class="badge bg-success ms-2">
                                <i class="fas fa-check me-1"></i>Activo
                            </span>
                        @else
                            <span class="badge bg-danger ms-2">
                                <i class="fas fa-times me-1"></i>Inactivo
                            </span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Información Básica --}}
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Información Personal</h6>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Cédula de Identidad:</label>
                                <div>
                                    <code class="bg-light px-2 py-1 rounded fs-6">{{ $user->ci }}</code>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Nombre Completo:</label>
                                <div class="fw-bold">{{ $user->nombre_completo }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Email:</label>
                                <div>
                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                        {{ $user->email }}
                                    </a>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-check-circle me-1"></i>Verificado
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($user->telefono)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Teléfono:</label>
                                    <div>
                                        <a href="tel:{{ $user->telefono }}" class="text-decoration-none">
                                            {{ $user->telefono }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Información Específica del Tipo --}}
                        <div class="col-md-6">
                            @if($user->clienteNatural)
                                <h6 class="border-bottom pb-2 mb-3 text-primary">
                                    <i class="fas fa-user me-1"></i>
                                    Cliente Natural
                                </h6>
                                @if($user->clienteNatural->nit)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">NIT:</label>
                                        <div>{{ $user->clienteNatural->nit }}</div>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label class="form-label text-muted">Estado del Cliente:</label>
                                    <div>
                                        @if($user->clienteNatural->estado == 1)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </div>
                                </div>
                            @elseif($user->clienteEstablecimiento)
                                <h6 class="border-bottom pb-2 mb-3 text-warning">
                                    <i class="fas fa-building me-1"></i>
                                    Cliente Establecimiento
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label text-muted">NIT del Establecimiento:</label>
                                    <div><code class="bg-light px-2 py-1 rounded">{{ $user->clienteEstablecimiento->nit }}</code></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Razón Social:</label>
                                    <div class="fw-bold">{{ $user->clienteEstablecimiento->razonSocial }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Tipo de Establecimiento:</label>
                                    <div>
                                        <span class="badge bg-info">{{ $user->clienteEstablecimiento->tipoEstablecimiento }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Domicilio Fiscal:</label>
                                    <div>{{ $user->clienteEstablecimiento->domicilioFiscal }}</div>
                                </div>
                            @elseif($user->empleado)
                                <h6 class="border-bottom pb-2 mb-3 text-success">
                                    <i class="fas fa-user-tie me-1"></i>
                                    Empleado
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Cargo:</label>
                                    <div class="fw-bold">{{ $user->empleado->cargo }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Rol en el Sistema:</label>
                                    <div>
                                        <span class="badge bg-success">{{ ucfirst($user->empleado->rol) }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Estado del Empleado:</label>
                                    <div>
                                        @if($user->empleado->estado == 1)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Panel de Información del Sistema --}}
        <div class="col-lg-4">
            {{-- Información del Sistema --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Información del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">ID del Usuario:</label>
                        <div><code>#{{ $user->idUser }}</code></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Tipo de Usuario:</label>
                        <div>
                            <span class="badge bg-info">{{ $user->tipo_usuario }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Fecha de Registro:</label>
                        <div>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'No disponible' }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted">Última Actualización:</label>
                        <div>{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'No disponible' }}</div>
                    </div>
                </div>
            </div>
            
            {{-- Acciones Rápidas --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cogs me-1"></i>
                        Acciones
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('users.edit', $user->idUser) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>
                            Editar Usuario
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Volver a la Lista
                        </a>
                        
                        {{-- Botón Eliminar --}}
                        <form action="{{ route('users.destroy', $user->idUser) }}" 
                              method="POST" 
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-danger w-100"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar el usuario {{ addslashes($user->nombre_completo) }}? Se marcará como inactivo.')">
                                <i class="fas fa-trash me-1"></i>
                                Eliminar Usuario
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
