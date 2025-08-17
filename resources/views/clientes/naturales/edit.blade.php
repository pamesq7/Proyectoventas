@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Cliente Natural</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('clienteNatural.index') }}">Clientes Naturales</a></li>
        <li class="breadcrumb-item active">Editar: {{ $clienteNatural->user->nombre_completo }}</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        Editar Cliente Natural: {{ $clienteNatural->user->nombre_completo }}
                        @if($clienteNatural->estado == 1 && $clienteNatural->user->estado == 1)
                            <span class="badge bg-success ms-2">Activo</span>
                        @else
                            <span class="badge bg-danger ms-2">Inactivo</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('clienteNatural.update', $clienteNatural->idCliente) }}" method="POST" id="editClienteForm">
                        @csrf
                        @method('PUT')
                        
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
                                           value="{{ old('ci', $clienteNatural->user->ci) }}" 
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
                                           value="{{ old('name', $clienteNatural->user->name) }}" 
                                           placeholder="Nombre del cliente"
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
                                           value="{{ old('primerApellido', $clienteNatural->user->primerApellido) }}" 
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
                                           value="{{ old('segundApellido', $clienteNatural->user->segundApellido) }}" 
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
                                           value="{{ old('email', $clienteNatural->user->email) }}" 
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
                                           value="{{ old('telefono', $clienteNatural->user->telefono) }}" 
                                           placeholder="Ej: 70123456">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- NIT --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nit" class="form-label">
                                        <i class="fas fa-file-invoice me-1"></i>
                                        NIT (opcional)
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nit') is-invalid @enderror" 
                                           id="nit" 
                                           name="nit" 
                                           value="{{ old('nit', $clienteNatural->nit) }}" 
                                           placeholder="Número de NIT">
                                    @error('nit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Estados --}}
                        <div class="row">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-toggle-on me-1"></i>
                                    Estados del Sistema
                                </h6>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Estado del Usuario --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estado" class="form-label">
                                        <i class="fas fa-user-check me-1"></i>
                                        Estado del Usuario <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('estado') is-invalid @enderror" 
                                            id="estado" 
                                            name="estado" 
                                            required>
                                        <option value="1" {{ old('estado', $clienteNatural->user->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('estado', $clienteNatural->user->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Controla el acceso al sistema
                                    </div>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Estado del Cliente --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estado_cliente" class="form-label">
                                        <i class="fas fa-user-tag me-1"></i>
                                        Estado del Cliente <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('estado_cliente') is-invalid @enderror" 
                                            id="estado_cliente" 
                                            name="estado_cliente" 
                                            required>
                                        <option value="1" {{ old('estado_cliente', $clienteNatural->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('estado_cliente', $clienteNatural->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Controla la capacidad de realizar compras
                                    </div>
                                    @error('estado_cliente')
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
                                                <small class="text-muted">ID del Cliente:</small>
                                                <div><code>#{{ $clienteNatural->idCliente }}</code></div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">ID del Usuario:</small>
                                                <div><code>#{{ $clienteNatural->user->idUser }}</code></div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Fecha de Registro:</small>
                                                <div>{{ $clienteNatural->created_at ? $clienteNatural->created_at->format('d/m/Y H:i') : 'No disponible' }}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Última Actualización:</small>
                                                <div>{{ $clienteNatural->updated_at ? $clienteNatural->updated_at->format('d/m/Y H:i') : 'No disponible' }}</div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted">Email Verificado:</small>
                                                <div>
                                                    @if($clienteNatural->user->email_verified_at)
                                                        <span class="badge bg-success">Sí</span>
                                                    @else
                                                        <span class="badge bg-warning">No</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Total de Ventas:</small>
                                                <div>
                                                    <span class="badge bg-info">{{ $clienteNatural->ventas->count() }} ventas</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Monto Total:</small>
                                                <div>
                                                    <strong>Bs. {{ number_format($clienteNatural->ventas->sum('total'), 2) }}</strong>
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
                                        <a href="{{ route('clienteNatural.index') }}" class="btn btn-secondary me-2">
                                            <i class="fas fa-arrow-left me-1"></i>
                                            Cancelar
                                        </a>
                                        <a href="{{ route('clienteNatural.show', $clienteNatural->idCliente) }}" class="btn btn-info">
                                            <i class="fas fa-eye me-1"></i>
                                            Ver Detalles
                                        </a>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Actualizar Cliente
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
    $('#editClienteForm').on('submit', function(e) {
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
    $('#estado, #estado_cliente').on('change', function() {
        if ($(this).val() == '0') {
            const tipo = $(this).attr('id') === 'estado' ? 'usuario' : 'cliente';
            if (!confirm(`¿Estás seguro de que deseas desactivar el ${tipo}? Esto afectará su capacidad de usar el sistema.`)) {
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

    // Formatear campos
    $('#ci, #telefono, #nit').on('input', function() {
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
