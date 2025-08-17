@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Usuario</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar: {{ $user->nombre_completo }}</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        Editar Usuario: {{ $user->nombre_completo }}
                        <span class="badge bg-info ms-2">{{ $user->tipo_usuario }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user->idUser) }}" method="POST" id="editUserForm">
                        @csrf
                        @method('PUT')
                        
                        {{-- Tipo de Usuario (Solo lectura) --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-user-tag me-1"></i>
                                            Tipo de Usuario
                                        </h6>
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-1"></i>
                                            <strong>Tipo actual:</strong> 
                                            @if($user->clienteNatural)
                                                <span class="badge bg-primary">Cliente Natural</span>
                                                - Persona física individual
                                            @elseif($user->clienteEstablecimiento)
                                                <span class="badge bg-warning">Cliente Establecimiento</span>
                                                - Empresa o institución
                                            @elseif($user->empleado)
                                                <span class="badge bg-success">Empleado</span>
                                                - Personal de la empresa
                                            @endif
                                            <br><small class="text-muted">El tipo de usuario no puede ser modificado después de la creación.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Información Personal (Común para todos) --}}
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
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="ci" class="form-label">
                                        <i class="fas fa-id-badge me-1"></i>
                                        Cédula de Identidad <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('ci') is-invalid @enderror" 
                                           id="ci" 
                                           name="ci" 
                                           value="{{ old('ci', $user->ci) }}" 
                                           placeholder="Ej: 1234567"
                                           required>
                                    @error('ci')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Nombre --}}
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-1"></i>
                                        Nombre <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           placeholder="Nombre"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Primer Apellido --}}
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="primerApellido" class="form-label">
                                        Primer Apellido <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('primerApellido') is-invalid @enderror" 
                                           id="primerApellido" 
                                           name="primerApellido" 
                                           value="{{ old('primerApellido', $user->primerApellido) }}" 
                                           placeholder="Primer apellido"
                                           required>
                                    @error('primerApellido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Segundo Apellido --}}
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="segundApellido" class="form-label">
                                        Segundo Apellido
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('segundApellido') is-invalid @enderror" 
                                           id="segundApellido" 
                                           name="segundApellido" 
                                           value="{{ old('segundApellido', $user->segundApellido) }}" 
                                           placeholder="Segundo apellido (opcional)">
                                    @error('segundApellido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
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
                                           value="{{ old('email', $user->email) }}" 
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
                                           value="{{ old('telefono', $user->telefono) }}" 
                                           placeholder="Ej: 70123456">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Estado --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="estado" class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        Estado <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('estado') is-invalid @enderror" 
                                            id="estado" 
                                            name="estado" 
                                            required>
                                        <option value="1" {{ old('estado', $user->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('estado', $user->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Cambio de Contraseña (Opcional) --}}
                        <div class="row">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-key me-1"></i>
                                    Cambiar Contraseña (Opcional)
                                </h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>
                                        Nueva Contraseña
                                    </label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Dejar vacío para mantener la actual">
                                    <div class="form-text">Mínimo 6 caracteres. Dejar vacío para no cambiar.</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="fas fa-lock me-1"></i>
                                        Confirmar Nueva Contraseña
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Repetir nueva contraseña">
                                </div>
                            </div>
                        </div>

                        {{-- Campos específicos por tipo de usuario --}}
                        
                        {{-- Cliente Natural --}}
                        @if($user->clienteNatural)
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3 text-primary">
                                        <i class="fas fa-user me-1"></i>
                                        Información de Cliente Natural
                                    </h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nit_cliente" class="form-label">
                                            <i class="fas fa-file-alt me-1"></i>
                                            NIT (opcional)
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('nit_cliente') is-invalid @enderror" 
                                               id="nit_cliente" 
                                               name="nit_cliente" 
                                               value="{{ old('nit_cliente', $user->clienteNatural->nit) }}" 
                                               placeholder="Número de NIT">
                                        @error('nit_cliente')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="estado_cliente" class="form-label">
                                            <i class="fas fa-toggle-on me-1"></i>
                                            Estado del Cliente
                                        </label>
                                        <select class="form-select" id="estado_cliente" name="estado_cliente">
                                            <option value="1" {{ old('estado_cliente', $user->clienteNatural->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                            <option value="0" {{ old('estado_cliente', $user->clienteNatural->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Cliente Establecimiento --}}
                        @if($user->clienteEstablecimiento)
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3 text-warning">
                                        <i class="fas fa-building me-1"></i>
                                        Información del Establecimiento
                                    </h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nit_establecimiento" class="form-label">
                                            <i class="fas fa-file-alt me-1"></i>
                                            NIT del Establecimiento <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('nit_establecimiento') is-invalid @enderror" 
                                               id="nit_establecimiento" 
                                               name="nit_establecimiento" 
                                               value="{{ old('nit_establecimiento', $user->clienteEstablecimiento->nit) }}" 
                                               placeholder="NIT de la empresa"
                                               required>
                                        @error('nit_establecimiento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="razonSocial" class="form-label">
                                            <i class="fas fa-building me-1"></i>
                                            Razón Social <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('razonSocial') is-invalid @enderror" 
                                               id="razonSocial" 
                                               name="razonSocial" 
                                               value="{{ old('razonSocial', $user->clienteEstablecimiento->razonSocial) }}" 
                                               placeholder="Nombre de la empresa"
                                               required>
                                        @error('razonSocial')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="tipoEstablecimiento" class="form-label">
                                            <i class="fas fa-tags me-1"></i>
                                            Tipo de Establecimiento <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('tipoEstablecimiento') is-invalid @enderror" 
                                                id="tipoEstablecimiento" 
                                                name="tipoEstablecimiento"
                                                required>
                                            <option value="">Seleccione el tipo</option>
                                            <option value="Empresa Privada" {{ old('tipoEstablecimiento', $user->clienteEstablecimiento->tipoEstablecimiento) == 'Empresa Privada' ? 'selected' : '' }}>Empresa Privada</option>
                                            <option value="Institución Pública" {{ old('tipoEstablecimiento', $user->clienteEstablecimiento->tipoEstablecimiento) == 'Institución Pública' ? 'selected' : '' }}>Institución Pública</option>
                                            <option value="ONG" {{ old('tipoEstablecimiento', $user->clienteEstablecimiento->tipoEstablecimiento) == 'ONG' ? 'selected' : '' }}>ONG</option>
                                            <option value="Cooperativa" {{ old('tipoEstablecimiento', $user->clienteEstablecimiento->tipoEstablecimiento) == 'Cooperativa' ? 'selected' : '' }}>Cooperativa</option>
                                            <option value="Otro" {{ old('tipoEstablecimiento', $user->clienteEstablecimiento->tipoEstablecimiento) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                        @error('tipoEstablecimiento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="estado_establecimiento" class="form-label">
                                            <i class="fas fa-toggle-on me-1"></i>
                                            Estado del Establecimiento
                                        </label>
                                        <select class="form-select" id="estado_establecimiento" name="estado_establecimiento">
                                            <option value="1" {{ old('estado_establecimiento', $user->clienteEstablecimiento->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                            <option value="0" {{ old('estado_establecimiento', $user->clienteEstablecimiento->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="domicilioFiscal" class="form-label">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            Domicilio Fiscal <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control @error('domicilioFiscal') is-invalid @enderror" 
                                                  id="domicilioFiscal" 
                                                  name="domicilioFiscal" 
                                                  rows="3" 
                                                  placeholder="Dirección completa del establecimiento"
                                                  required>{{ old('domicilioFiscal', $user->clienteEstablecimiento->domicilioFiscal) }}</textarea>
                                        @error('domicilioFiscal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Empleado --}}
                        @if($user->empleado)
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3 text-success">
                                        <i class="fas fa-user-tie me-1"></i>
                                        Información del Empleado
                                    </h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cargo" class="form-label">
                                            <i class="fas fa-briefcase me-1"></i>
                                            Cargo <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('cargo') is-invalid @enderror" 
                                               id="cargo" 
                                               name="cargo" 
                                               value="{{ old('cargo', $user->empleado->cargo) }}" 
                                               placeholder="Ej: Vendedor, Diseñador, Gerente"
                                               required>
                                        @error('cargo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="rol" class="form-label">
                                            <i class="fas fa-user-shield me-1"></i>
                                            Rol en el Sistema <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('rol') is-invalid @enderror" 
                                                id="rol" 
                                                name="rol"
                                                required>
                                            <option value="">Seleccione el rol</option>
                                            <option value="vendedor" {{ old('rol', $user->empleado->rol) == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                                            <option value="disenador" {{ old('rol', $user->empleado->rol) == 'disenador' ? 'selected' : '' }}>Diseñador</option>
                                            <option value="administrador" {{ old('rol', $user->empleado->rol) == 'administrador' ? 'selected' : '' }}>Administrador</option>
                                            <option value="gerente" {{ old('rol', $user->empleado->rol) == 'gerente' ? 'selected' : '' }}>Gerente</option>
                                        </select>
                                        @error('rol')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="estado_empleado" class="form-label">
                                            <i class="fas fa-toggle-on me-1"></i>
                                            Estado del Empleado
                                        </label>
                                        <select class="form-select" id="estado_empleado" name="estado_empleado">
                                            <option value="1" {{ old('estado_empleado', $user->empleado->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                            <option value="0" {{ old('estado_empleado', $user->empleado->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Información del Sistema --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Información del Sistema
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <small class="text-muted">ID del Usuario:</small>
                                                <div><code>#{{ $user->idUser }}</code></div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Fecha de Registro:</small>
                                                <div>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'No disponible' }}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Última Actualización:</small>
                                                <div>{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'No disponible' }}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Email Verificado:</small>
                                                <div>
                                                    @if($user->email_verified_at)
                                                        <span class="badge bg-success">Sí</span>
                                                    @else
                                                        <span class="badge bg-warning">No</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                                            <i class="fas fa-arrow-left me-1"></i>
                                            Cancelar
                                        </a>
                                        <a href="{{ route('users.show', $user->idUser) }}" class="btn btn-info">
                                            <i class="fas fa-eye me-1"></i>
                                            Ver Detalles
                                        </a>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Actualizar Usuario
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
    // Validación del formulario
    $('#editUserForm').on('submit', function(e) {
        // Validar contraseñas si se están cambiando
        const password = $('#password').val();
        const passwordConfirmation = $('#password_confirmation').val();
        
        if (password && password !== passwordConfirmation) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            return false;
        }
        
        if (password && password.length < 6) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 6 caracteres');
            return false;
        }
    });

    // Confirmación antes de cambiar estado a inactivo
    $('#estado').on('change', function() {
        if ($(this).val() == '0') {
            if (!confirm('¿Estás seguro de que deseas desactivar este usuario? No podrá acceder al sistema.')) {
                $(this).val('1');
            }
        }
    });

    // Mostrar/ocultar confirmación de contraseña
    $('#password').on('input', function() {
        const passwordValue = $(this).val();
        if (passwordValue) {
            $('#password_confirmation').prop('required', true);
        } else {
            $('#password_confirmation').prop('required', false);
        }
    });
});
</script>
@endpush