@extends('layouts.app')

@section('title', 'Editar Empleado')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-user-edit text-primary me-2"></i>
        Editar Empleado
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('empleados.index') }}">Empleados</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie me-2"></i>
                        Editar Información del Empleado
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Por favor corrige los siguientes errores:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('empleados.update', $empleado) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Información del Usuario Asociado -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-user me-2"></i>
                                            Usuario Asociado
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Nombre:</strong> 
                                                {{ $empleado->user->name ?? 'N/A' }} 
                                                {{ $empleado->user->primerApellido ?? '' }}
                                                {{ $empleado->user->segundApellido ?? '' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>CI:</strong> {{ $empleado->user->ci ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <strong>Email:</strong> {{ $empleado->user->email ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <strong>Teléfono:</strong> {{ $empleado->user->telefono ?? 'N/A' }}
                                            </div>
                                        </div>
                                        <div class="form-text mt-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Para cambiar la información personal, debe editarse desde la gestión de usuarios.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información Laboral -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="mb-3">
                                    <i class="fas fa-briefcase me-2"></i>
                                    Información Laboral
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cargo" class="form-label">Cargo <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('cargo') is-invalid @enderror" 
                                       id="cargo" name="cargo" 
                                       value="{{ old('cargo', $empleado->cargo) }}" 
                                       placeholder="Ej: Vendedor Senior, Diseñador Gráfico..."
                                       required>
                                @error('cargo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                                <select class="form-select @error('rol') is-invalid @enderror" 
                                        id="rol" name="rol" required>
                                    <option value="">Seleccione un rol...</option>
                                    <option value="administrador" 
                                            {{ old('rol', $empleado->rol) == 'administrador' ? 'selected' : '' }}>
                                        Administrador
                                    </option>
                                    <option value="vendedor" 
                                            {{ old('rol', $empleado->rol) == 'vendedor' ? 'selected' : '' }}>
                                        Vendedor
                                    </option>
                                    <option value="diseñador" 
                                            {{ old('rol', $empleado->rol) == 'diseñador' ? 'selected' : '' }}>
                                        Diseñador
                                    </option>
                                    <option value="operador" 
                                            {{ old('rol', $empleado->rol) == 'operador' ? 'selected' : '' }}>
                                        Operador
                                    </option>
                                    <option value="cliente" 
                                            {{ old('rol', $empleado->rol) == 'cliente' ? 'selected' : '' }}>
                                        Cliente
                                    </option>
                                </select>
                                @error('rol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="mb-3">
                                    <i class="fas fa-toggle-on me-2"></i>
                                    Estado
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           id="estado" name="estado" value="1"
                                           {{ old('estado', $empleado->estado) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="estado">
                                        Empleado Activo
                                    </label>
                                </div>
                                <div class="form-text">
                                    Los empleados inactivos no podrán acceder al sistema.
                                </div>
                            </div>
                        </div>

                        <!-- Estadísticas del Empleado -->
                        @php
                            $ventasCount = $empleado->ventas ? $empleado->ventas->count() : 0;
                            $disenosCount = $empleado->disenos ? $empleado->disenos->count() : 0;
                            $totalVentas = $empleado->ventas ? $empleado->ventas->sum('total') : 0;
                        @endphp

                        @if($ventasCount > 0 || $disenosCount > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="mb-3">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Estadísticas del Empleado
                                </h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card bg-info text-white">
                                            <div class="card-body text-center">
                                                <h4>{{ $ventasCount }}</h4>
                                                <small>Ventas Realizadas</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h4>${{ number_format($totalVentas, 2) }}</h4>
                                                <small>Total Vendido</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body text-center">
                                                <h4>{{ $disenosCount }}</h4>
                                                <small>Diseños Creados</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Información de Registro (CORREGIDO) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-secondary">
                                    <h6><i class="fas fa-calendar me-2"></i>Información de Registro:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Fecha de Registro:</strong> 
                                            {{ $empleado->created_at ? $empleado->created_at->format('d/m/Y H:i') : 'No disponible' }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Última Actualización:</strong> 
                                            {{ $empleado->updated_at ? $empleado->updated_at->format('d/m/Y H:i') : 'No disponible' }}
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
                                        Volver
                                    </a>
                                    <div>
                                        <a href="{{ route('empleados.show', $empleado) }}" class="btn btn-info me-2">
                                            <i class="fas fa-eye me-2"></i>
                                            Ver Detalles
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Actualizar Empleado
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación adicional del formulario
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const cargo = document.getElementById('cargo').value.trim();
        const rol = document.getElementById('rol').value;
        
        if (!cargo || !rol) {
            e.preventDefault();
            alert('Por favor complete todos los campos obligatorios.');
            return false;
        }
    });
    
    // Confirmación antes de cambiar el estado si tiene ventas
    const estadoCheckbox = document.getElementById('estado');
    const ventasCount = {{ $ventasCount }};
    
    if (estadoCheckbox) {
        estadoCheckbox.addEventListener('change', function() {
            if (!this.checked && ventasCount > 0) {
                if (!confirm('Este empleado tiene ' + ventasCount + ' ventas registradas. ¿Está seguro de desactivarlo?')) {
                    this.checked = true;
                }
            }
        });
    }
});
</script>
@endpush
@endsection