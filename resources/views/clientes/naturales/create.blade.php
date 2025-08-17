@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Nuevo Cliente Natural</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('clienteNatural.index') }}">Clientes Naturales</a></li>
        <li class="breadcrumb-item active">Nuevo Cliente</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Registro de Nuevo Cliente Natural
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('clienteNatural.store') }}" method="POST" id="createClienteForm">
                        @csrf
                        
                        {{-- Información Personal --}}
                        <div class="row">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-id-card me-1"></i>
                                    Información Personal
                                </h6>
                            </div>
                        </div>

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

                        {{-- Información Específica del Cliente --}}
                        <div class="row">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-file-alt me-1"></i>
                                    Información del Cliente
                                </h6>
                            </div>
                        </div>

                        <div class="row">
                            {{-- NIT --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nit" class="form-label">
                                        <i class="fas fa-file-invoice me-1"></i>
                                        NIT (opcional)
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nit') is-invalid @enderror" 
                                           id="nit" 
                                           name="nit" 
                                           value="{{ old('nit') }}" 
                                           placeholder="Número de NIT">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        El NIT es opcional para clientes naturales
                                    </div>
                                    @error('nit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Información de Acceso --}}
                        <div class="row">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-key me-1"></i>
                                    Información de Acceso al Sistema
                                </h6>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Contraseña --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>
                                        Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Mínimo 6 caracteres"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Confirmar Contraseña --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="fas fa-lock me-1"></i>
                                        Confirmar Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Repetir contraseña"
                                           required>
                                </div>
                            </div>
                        </div>

                        {{-- Información Adicional --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Información Importante
                                        </h6>
                                        <ul class="mb-0">
                                            <li>El cliente podrá acceder al sistema con su email y contraseña</li>
                                            <li>Podrá ver sus pedidos, historial de compras y realizar nuevos pedidos</li>
                                            <li>La cédula de identidad y email deben ser únicos en el sistema</li>
                                            <li>El NIT es opcional para clientes naturales</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('clienteNatural.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Crear Cliente Natural
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Generar email automático basado en nombre y apellido
    $('#name, #primerApellido').on('blur', function() {
        if ($('#email').val() === '') {
            const nombre = $('#name').val().toLowerCase().replace(/\s+/g, '');
            const apellido = $('#primerApellido').val().toLowerCase().replace(/\s+/g, '');
            if (nombre && apellido) {
                $('#email').val(nombre + '.' + apellido + '@cliente.com');
            }
        }
    });

    // Validación del formulario
    $('#createClienteForm').on('submit', function(e) {
        const password = $('#password').val();
        const passwordConfirmation = $('#password_confirmation').val();
        
        if (password !== passwordConfirmation) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            $('#password_confirmation').focus();
            return false;
        }
        
        if (password.length < 6) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 6 caracteres');
            $('#password').focus();
            return false;
        }
    });

    // Formatear CI (solo números)
    $('#ci').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Formatear teléfono (solo números)
    $('#telefono').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Formatear NIT (solo números)
    $('#nit').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Capitalizar nombres
    $('#name, #primerApellido, #segundApellido').on('blur', function() {
        this.value = this.value.replace(/\b\w/g, function(l) {
            return l.toUpperCase();
        });
    });
});
</script>
@endpush
