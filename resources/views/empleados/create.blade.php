@extends('layouts.app')

@section('title', 'Nuevo Empleado')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-user-plus text-primary me-2"></i>
        Nuevo Empleado
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('empleados.index') }}">Empleados</a></li>
        <li class="breadcrumb-item active">Nuevo</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie me-2"></i>
                        Información del Empleado
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

                    <form action="{{ route('empleados.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            {{-- CI --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ci" class="form-label">
                                        <i class="fas fa-id-badge me-1"></i>
                                        Cédula de Identidad <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('ci') is-invalid @enderror" 
                                           id="ci" 
                                           name="ci" 
                                           value="{{ old('ci') }}" 
                                           placeholder="Ej: 1234567"
                                           required>
                                    @error('ci')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Nombre --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-1"></i>
                                        Nombre <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Nombre del cliente"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Primer Apellido --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="primerApellido" class="form-label">
                                        Primer Apellido <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('primerApellido') is-invalid @enderror" 
                                           id="primerApellido" 
                                           name="primerApellido" 
                                           value="{{ old('primerApellido') }}" 
                                           placeholder="Primer apellido"
                                           required>
                                    @error('primerApellido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Segundo Apellido --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="segundApellido" class="form-label">
                                        Segundo Apellido
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('segundApellido') is-invalid @enderror" 
                                           id="segundApellido" 
                                           name="segundApellido" 
                                           value="{{ old('segundApellido') }}" 
                                           placeholder="Segundo apellido (opcional)">
                                    @error('segundApellido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>
                                        Correo Electrónico <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="correo@ejemplo.com"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Teléfono --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">
                                        <i class="fas fa-phone me-1"></i>
                                        Teléfono
                                    </label>
                                    <input type="tel" 
                                           class="form-control @error('telefono') is-invalid @enderror" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="{{ old('telefono') }}" 
                                           placeholder="Ej: 70123456">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                       value="{{ old('cargo') }}" 
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
                                    <option value="administrador" {{ old('rol') == 'administrador' ? 'selected' : '' }}>
                                        Administrador
                                    </option>
                                    <option value="vendedor" {{ old('rol') == 'vendedor' ? 'selected' : '' }}>
                                        Vendedor
                                    </option>
                                    <option value="diseñador" {{ old('rol') == 'diseñador' ? 'selected' : '' }}>
                                        Diseñador
                                    </option>
                                    <option value="operador" {{ old('rol') == 'operador' ? 'selected' : '' }}>
                                        Operador
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
                                           {{ old('estado', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="estado">
                                        Empleado Activo
                                    </label>
                                </div>
                                <div class="form-text">
                                    Los empleados inactivos no podrán acceder al sistema.
                                </div>
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Información Importante:</h6>
                                    <ul class="mb-0">
                                        <li>El empleado heredará la información personal (nombre, teléfono, email) del usuario seleccionado.</li>
                                        <li>Una vez creado, podrá gestionar ventas y diseños según su rol asignado.</li>
                                        <li>Los administradores tendrán acceso completo al sistema.</li>
                                        <li>Los vendedores podrán gestionar ventas y clientes.</li>
                                    </ul>
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
                                        <button type="reset" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-undo me-2"></i>
                                            Limpiar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Crear Empleado
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
    // Mejorar la visualización del select de usuarios
    const userSelect = document.getElementById('idUser');
    
    // Agregar evento para mostrar información adicional del usuario seleccionado
    userSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            // Aquí podrías agregar lógica adicional si necesitas mostrar más información
            console.log('Usuario seleccionado:', selectedOption.text);
        }
    });
    
    // Validación adicional del formulario
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const idUser = document.getElementById('idUser').value;
        const cargo = document.getElementById('cargo').value.trim();
        const rol = document.getElementById('rol').value;
        
        if (!idUser || !cargo || !rol) {
            e.preventDefault();
            alert('Por favor complete todos los campos obligatorios.');
            return false;
        }
    });
});
</script>
@endpush
@endsection
