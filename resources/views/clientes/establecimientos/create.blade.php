@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Nuevo Cliente Establecimiento</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('clienteEstablecimiento.index') }}">Clientes Establecimientos</a></li>
        <li class="breadcrumb-item active">Nuevo Establecimiento</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>
                        Registro de Nuevo Cliente Establecimiento
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('clienteEstablecimiento.store') }}" method="POST" id="createEstablecimientoForm">
                        @csrf
                        
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
                                           value="{{ old('nit_establecimiento') }}" 
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
                                           value="{{ old('razonSocial') }}" 
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
                                        <option value="Empresa Privada" {{ old('tipoEstablecimiento') == 'Empresa Privada' ? 'selected' : '' }}>Empresa Privada</option>
                                        <option value="Institución Pública" {{ old('tipoEstablecimiento') == 'Institución Pública' ? 'selected' : '' }}>Institución Pública</option>
                                        <option value="ONG" {{ old('tipoEstablecimiento') == 'ONG' ? 'selected' : '' }}>ONG</option>
                                        <option value="Cooperativa" {{ old('tipoEstablecimiento') == 'Cooperativa' ? 'selected' : '' }}>Cooperativa</option>
                                        <option value="Otro" {{ old('tipoEstablecimiento') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('tipoEstablecimiento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Domicilio Fiscal --}}
                            <div class="col-md-6">
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
                                              required>{{ old('domicilioFiscal') }}</textarea>
                                    @error('domicilioFiscal')
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
                                           value="{{ old('ci') }}" 
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
                                           value="{{ old('name') }}" 
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
                                           value="{{ old('primerApellido') }}" 
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
                                           value="{{ old('segundApellido') }}" 
                                           placeholder="Segundo apellido (opcional)">
                                    @error('segundApellido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Email del Representante --}}
                            <div class="col-md-6">
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
                                           placeholder="correo@empresa.com"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Teléfono del Representante --}}
                            <div class="col-md-6">
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
                                            <li>El representante legal podrá acceder al sistema con su email y contraseña</li>
                                            <li>Podrá gestionar pedidos, ver historial de compras y realizar nuevos pedidos en nombre del establecimiento</li>
                                            <li>El NIT del establecimiento debe ser único en el sistema</li>
                                            <li>La cédula de identidad y email del representante deben ser únicos</li>
                                            <li>El representante legal será responsable de todas las transacciones del establecimiento</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('clienteEstablecimiento.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Crear Cliente Establecimiento
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
    // Generar email automático basado en razón social
    $('#razonSocial').on('blur', function() {
        if ($('#email').val() === '') {
            const razonSocial = $(this).val().toLowerCase()
                .replace(/\s+/g, '')
                .replace(/[^a-z0-9]/g, '')
                .substring(0, 10);
            if (razonSocial) {
                $('#email').val('contacto@' + razonSocial + '.com');
            }
        }
    });

    // Generar email alternativo basado en nombre del representante
    $('#name, #primerApellido').on('blur', function() {
        if ($('#email').val() === '' || $('#email').val().includes('contacto@')) {
            const nombre = $('#name').val().toLowerCase().replace(/\s+/g, '');
            const apellido = $('#primerApellido').val().toLowerCase().replace(/\s+/g, '');
            const razonSocial = $('#razonSocial').val().toLowerCase()
                .replace(/\s+/g, '')
                .replace(/[^a-z0-9]/g, '')
                .substring(0, 8);
            
            if (nombre && apellido && razonSocial) {
                $('#email').val(nombre + '.' + apellido + '@' + razonSocial + '.com');
            }
        }
    });

    // Validación del formulario
    $('#createEstablecimientoForm').on('submit', function(e) {
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
});
</script>
@endpush
