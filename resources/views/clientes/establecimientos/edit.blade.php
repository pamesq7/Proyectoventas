@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Cliente Establecimiento</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('clienteEstablecimiento.index') }}">Clientes Establecimientos</a></li>
        <li class="breadcrumb-item"><a href="{{ route('clienteEstablecimiento.show', $clienteEstablecimiento->idEstablecimiento) }}">{{ $clienteEstablecimiento->razonSocial }}</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Editar Cliente Establecimiento: {{ $clienteEstablecimiento->razonSocial }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('clienteEstablecimiento.update', $clienteEstablecimiento->idEstablecimiento) }}" method="POST" id="editEstablecimientoForm">
                        @csrf
                        @method('PUT')
                        
                        {{-- Información del Establecimiento --}}
                        <div class="row">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-building me-1"></i>
                                    Información del Establecimiento
                                </h6>
                            </div>
                        </div>

                        <div class="row">
                            {{-- NIT del Establecimiento --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nit_establecimiento" class="form-label">
                                        <i class="fas fa-file-invoice me-1"></i>
                                        NIT del Establecimiento <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nit_establecimiento') is-invalid @enderror" 
                                           id="nit_establecimiento" 
                                           name="nit_establecimiento" 
                                           value="{{ old('nit_establecimiento', $clienteEstablecimiento->nit) }}" 
                                           placeholder="Ej: 1234567890"
                                           required>
                                    @error('nit_establecimiento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Razón Social --}}
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="razonSocial" class="form-label">
                                        <i class="fas fa-building me-1"></i>
                                        Razón Social <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('razonSocial') is-invalid @enderror" 
                                           id="razonSocial" 
                                           name="razonSocial" 
                                           value="{{ old('razonSocial', $clienteEstablecimiento->razonSocial) }}" 
                                           placeholder="Nombre completo de la empresa o institución"
                                           required>
                                    @error('razonSocial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Tipo de Establecimiento --}}
                            <div class="col-md-6">
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
                                        <option value="Empresa Privada" {{ old('tipoEstablecimiento', $clienteEstablecimiento->tipoEstablecimiento) == 'Empresa Privada' ? 'selected' : '' }}>Empresa Privada</option>
                                        <option value="Institución Pública" {{ old('tipoEstablecimiento', $clienteEstablecimiento->tipoEstablecimiento) == 'Institución Pública' ? 'selected' : '' }}>Institución Pública</option>
                                        <option value="ONG" {{ old('tipoEstablecimiento', $clienteEstablecimiento->tipoEstablecimiento) == 'ONG' ? 'selected' : '' }}>ONG</option>
                                        <option value="Cooperativa" {{ old('tipoEstablecimiento', $clienteEstablecimiento->tipoEstablecimiento) == 'Cooperativa' ? 'selected' : '' }}>Cooperativa</option>
                                        <option value="Otro" {{ old('tipoEstablecimiento', $clienteEstablecimiento->tipoEstablecimiento) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('tipoEstablecimiento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Espacio reservado para futuras mejoras --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Nota:</strong> El estado del establecimiento se configura más abajo junto con el domicilio fiscal.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Domicilio Fiscal --}}
                            <div class="col-md-8">
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
                                              required>{{ old('domicilioFiscal', $clienteEstablecimiento->domicilioFiscal) }}</textarea>
                                    @error('domicilioFiscal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Estado del Establecimiento --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="estado_establecimiento" class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        Estado del Establecimiento <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('estado_establecimiento') is-invalid @enderror" 
                                            id="estado_establecimiento" 
                                            name="estado_establecimiento"
                                            required>
                                        <option value="1" {{ old('estado_establecimiento', $clienteEstablecimiento->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('estado_establecimiento', $clienteEstablecimiento->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('estado_establecimiento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Información del Representante Legal --}}
                        <div class="row">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-user-tie me-1"></i>
                                    Información del Representante Legal
                                </h6>
                            </div>
                        </div>

                        <div class="row">
                            {{-- CI del Representante --}}
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
                                           value="{{ old('ci', $clienteEstablecimiento->representante->ci) }}" 
                                           placeholder="Ej: 1234567"
                                           required>
                                    @error('ci')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Nombre del Representante --}}
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
                                           value="{{ old('name', $clienteEstablecimiento->representante->name) }}" 
                                           placeholder="Nombre del representante"
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
                                           value="{{ old('primerApellido', $clienteEstablecimiento->representante->primerApellido) }}" 
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
                                           value="{{ old('segundApellido', $clienteEstablecimiento->representante->segundApellido) }}" 
                                           placeholder="Segundo apellido (opcional)">
                                    @error('segundApellido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Email del Representante --}}
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
                                           value="{{ old('email', $clienteEstablecimiento->representante->email) }}" 
                                           placeholder="correo@empresa.com"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Teléfono del Representante --}}
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
                                           value="{{ old('telefono', $clienteEstablecimiento->representante->telefono) }}" 
                                           placeholder="Ej: 70123456">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Estado del Representante --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="estado" class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        Estado del Representante <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('estado') is-invalid @enderror" 
                                            id="estado" 
                                            name="estado"
                                            required>
                                        <option value="1" {{ old('estado', $clienteEstablecimiento->representante->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('estado', $clienteEstablecimiento->representante->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
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
                        </div>

                        {{-- Cambio de Contraseña (Opcional) --}}
                        <div class="row">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-key me-1"></i>
                                    Cambio de Contraseña (Opcional)
                                </h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Nota:</strong> Deje estos campos vacíos si no desea cambiar la contraseña del representante.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Nueva Contraseña --}}
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
                                           placeholder="Mínimo 6 caracteres (opcional)">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Confirmar Nueva Contraseña --}}
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
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Información del Sistema
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <strong>ID del Establecimiento:</strong> 
                                                    <code>#{{ $clienteEstablecimiento->idEstablecimiento }}</code>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>ID del Representante:</strong> 
                                                    <code>#{{ $clienteEstablecimiento->representante->idUser }}</code>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <strong>Fecha de Registro:</strong> 
                                                    {{ $clienteEstablecimiento->created_at ? $clienteEstablecimiento->created_at->format('d/m/Y H:i') : 'No disponible' }}
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Última Actualización:</strong> 
                                                    {{ $clienteEstablecimiento->updated_at ? $clienteEstablecimiento->updated_at->format('d/m/Y H:i') : 'No disponible' }}
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="small text-muted">
                                            <strong>Nota:</strong> Los estados del establecimiento y representante son independientes. 
                                            Puede activar/desactivar cada uno según sea necesario.
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
                                        <a href="{{ route('clienteEstablecimiento.show', $clienteEstablecimiento->idEstablecimiento) }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i>
                                            Cancelar
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            Actualizar Cliente Establecimiento
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Validación del formulario
    $('#editEstablecimientoForm').on('submit', function(e) {
        const password = $('#password').val();
        const passwordConfirmation = $('#password_confirmation').val();
        
        // Solo validar contraseñas si se ingresó una nueva
        if (password || passwordConfirmation) {
            if (password !== passwordConfirmation) {
                e.preventDefault();
                showNotification('error', 'Las contraseñas ingresadas no coinciden. Por favor, verifique e intente nuevamente.');
                $('#password_confirmation').focus();
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                showNotification('error', 'La contraseña debe tener al menos 6 caracteres para garantizar la seguridad.');
                $('#password').focus();
                return false;
            }
        }
        
        // Mostrar notificación de procesamiento
        showNotification('info', 'Procesando los cambios, por favor espere...', 'Guardando');
        
        // Deshabilitar el botón para evitar doble envío
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Guardando...');
    });

    // Formatear campos numéricos
    $('#ci, #telefono, #nit_establecimiento').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Capitalizar nombres y razón social
    $('#name, #primerApellido, #segundApellido, #razonSocial').on('blur', function() {
        this.value = this.value.replace(/\b\w/g, function(l) {
            return l.toUpperCase();
        });
    });

    // Validar NIT (debe tener al menos 7 dígitos)
    $('#nit_establecimiento').on('blur', function() {
        const nit = $(this).val();
        if (nit && nit.length < 7) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">El NIT debe tener al menos 7 dígitos</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Confirmación de cambios importantes con notificaciones
    $('#estado_establecimiento, #estado').on('change', function() {
        const fieldName = $(this).attr('id') === 'estado_establecimiento' ? 'establecimiento' : 'representante';
        const newState = $(this).val() == '1' ? 'activo' : 'inactivo';
        const originalValue = $(this).data('original-value');
        
        // Solo mostrar confirmación si realmente cambió
        if ($(this).val() != originalValue) {
            if (!confirm(`¿Está seguro de cambiar el estado del ${fieldName} a ${newState}?\n\nEste cambio afectará el acceso y las funcionalidades disponibles.`)) {
                // Revertir el cambio
                $(this).val(originalValue);
                showNotification('info', `El estado del ${fieldName} se mantuvo sin cambios.`);
            } else {
                showNotification('warning', `El estado del ${fieldName} será cambiado a ${newState} al guardar el formulario.`);
            }
        }
    });

    // Guardar valores originales para confirmación
    $('#estado_establecimiento, #estado_representante').each(function() {
        $(this).data('original-value', $(this).val());
    });
});
</script>
@endpush
