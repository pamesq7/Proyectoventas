@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Crear Nuevo Usuario</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
        <li class="breadcrumb-item active">Crear Usuario</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Registro de Nuevo Usuario
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST" id="createUserForm">
                        @csrf

                        {{-- Selector de Tipo de Usuario --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-user-tag me-1"></i>
                                            Tipo de Usuario <span class="text-danger">*</span>
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="tipo_usuario"
                                                        id="cliente_natural" value="cliente_natural"
                                                        {{ old('tipo_usuario') == 'cliente_natural' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="cliente_natural">
                                                        <i class="fas fa-user text-primary me-1"></i>
                                                        <strong>Cliente Natural</strong>
                                                        <br><small class="text-muted">Persona física individual</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="tipo_usuario"
                                                        id="cliente_establecimiento" value="cliente_establecimiento"
                                                        {{ old('tipo_usuario') == 'cliente_establecimiento' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="cliente_establecimiento">
                                                        <i class="fas fa-building text-warning me-1"></i>
                                                        <strong>Cliente Establecimiento</strong>
                                                        <br><small class="text-muted">Empresa o institución</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="tipo_usuario"
                                                        id="empleado" value="empleado"
                                                        {{ old('tipo_usuario') == 'empleado' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="empleado">
                                                        <i class="fas fa-user-tie text-success me-1"></i>
                                                        <strong>Empleado</strong>
                                                        <br><small class="text-muted">Personal de la empresa</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @error('tipo_usuario')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
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
                                        value="{{ old('ci') }}"
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
                                        value="{{ old('name') }}"
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
                            {{-- Email --}}
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
                                        placeholder="correo@ejemplo.com"
                                        required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Teléfono --}}
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

                        
                        {{-- Campos específicos por tipo de usuario --}}

                        {{-- Cliente Natural --}}
                        <div id="campos_cliente_natural" class="tipo-usuario-campos" style="display: none;">
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
                                            value="{{ old('nit_cliente') }}"
                                            placeholder="Número de NIT">
                                        @error('nit_cliente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Botones --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i>
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            Crear Usuario
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Cliente Establecimiento --}}
                        <div id="campos_cliente_establecimiento" class="tipo-usuario-campos" style="display: none;">
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
                                            value="{{ old('nit_establecimiento') }}"
                                            placeholder="NIT de la empresa">
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
                                            value="{{ old('razonSocial') }}"
                                            placeholder="Nombre de la empresa">
                                        @error('razonSocial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tipoEstablecimiento" class="form-label">
                                            <i class="fas fa-tags me-1"></i>
                                            Tipo de Establecimiento <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('tipoEstablecimiento') is-invalid @enderror"
                                            id="tipoEstablecimiento"
                                            name="tipoEstablecimiento">
                                            <option value="">Seleccione el tipo</option>
                                            <option value="Colegio" {{ old('tipoEstablecimiento') == 'Colegio' ? 'selected' : '' }}>Colegio</option>
                                            <option value="Institución Alcaldía" {{ old('tipoEstablecimiento') == 'Institución Alcaldía' ? 'selected' : '' }}>Institución Alcaldía</option>
                                            <option value="Escuela de Fútbol o Basket" {{ old('tipoEstablecimiento') == 'Escuela de Fútbol o Basket' ? 'selected' : '' }}>Escuela de Fútbol o Basket</option>
                                            <option value="Otro" {{ old('tipoEstablecimiento') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                        @error('tipoEstablecimiento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Botones --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i>
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            Crear Usuario
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Empleado --}}
                        <div id="campos_empleado" class="tipo-usuario-campos" style="display: none;">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3 text-success">
                                        <i class="fas fa-user-tie me-1"></i>
                                        Información del Empleado
                                    </h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cargo" class="form-label">
                                            <i class="fas fa-briefcase me-1"></i>
                                            Cargo <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control @error('cargo') is-invalid @enderror"
                                            id="cargo"
                                            name="cargo"
                                            value="{{ old('cargo') }}"
                                            placeholder="Ej: Vendedor, Diseñador, Gerente">
                                        @error('cargo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rol" class="form-label">
                                            <i class="fas fa-user-shield me-1"></i>
                                            Rol en el Sistema <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('rol') is-invalid @enderror"
                                            id="rol"
                                            name="rol">
                                            <option value="">Seleccione el rol</option>
                                            <option value="administrador" {{ old('rol') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                                            <option value="diseñador" {{ old('rol') == 'diseñador' ? 'selected' : '' }}>Diseñador</option>
                                            <option value="operador" {{ old('rol') == 'operador' ? 'selected' : '' }}>Operador</option>
                                            <option value="cliente" {{ old('rol') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                            <option value="vendedor" {{ old('rol') == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                                        </select>
                                        @error('rol')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Botones --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i>
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            Crear Usuario
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
    // Verificar si jQuery está cargado
    if (typeof jQuery == 'undefined') {
        console.error('jQuery no está cargado');
    }

    // Función para mostrar/ocultar campos
    function toggleCamposTipoUsuario() {
        console.log('toggleCamposTipoUsuario ejecutándose...');

        // Ocultar todos los campos de tipo de usuario
        $('.tipo-usuario-campos').hide();

        // Obtener el valor del tipo de usuario seleccionado
        const tipoSeleccionado = $('input[name="tipo_usuario"]:checked');

        if (tipoSeleccionado.length > 0) {
            const valor = tipoSeleccionado.val();
            console.log('Mostrando campos para:', valor);

            // Mostrar solo los campos correspondientes al tipo seleccionado
            const camposAMostrar = $('#campos_' + valor);
            if (camposAMostrar.length) {
                camposAMostrar.show();
                console.log('Campos mostrados correctamente');
            } else {
                console.error('No se encontraron campos para el tipo:', valor);
            }
        }
    }

    // Inicialización cuando el documento esté listo
    $(document).ready(function() {
        console.log('jQuery listo');

        // Inicializar visibilidad de campos
        toggleCamposTipoUsuario();

        // Manejar cambio de tipo de usuario
        $(document).on('change', 'input[name="tipo_usuario"]', function() {
            console.log('Cambio detectado en tipo de usuario');
            toggleCamposTipoUsuario();
        });

        // Validación del formulario
        $('#createUserForm').on('submit', function(e) {
            const tipoUsuario = $('input[name="tipo_usuario"]:checked');

            if (tipoUsuario.length === 0) {
                e.preventDefault();
                alert('Por favor selecciona un tipo de usuario');
                return false;
            }

            const valor = tipoUsuario.val();

            // Validaciones específicas
            if (valor === 'cliente_establecimiento') {
                const nit = $('#nit_establecimiento').val();
                const razonSocial = $('#razonSocial').val();
                const tipoEst = $('#tipoEstablecimiento').val();
                const domicilio = $('#domicilioFiscal').val();

                if (!nit || !razonSocial || !tipoEst || !domicilio) {
                    e.preventDefault();
                    alert('Por favor completa todos los campos obligatorios del establecimiento');
                    return false;
                }
            }

            if (valor === 'empleado') {
                const cargo = $('#cargo').val();
                const rol = $('#rol').val();

                if (!cargo || !rol) {
                    e.preventDefault();
                    alert('Por favor completa todos los campos obligatorios del empleado');
                    return false;
                }
            }
        });

        // Generar email automático
        $('#name, #primerApellido').on('blur', function() {
            if ($('#email').val() === '') {
                const nombre = $('#name').val().toLowerCase();
                const apellido = $('#primerApellido').val().toLowerCase();
                if (nombre && apellido) {
                    $('#email').val(nombre + '.' + apellido + '@empresa.com');
                }
            }
        });
    });
</script>
@endpush