<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - {{ config('app.name', 'Sistema de Ventas') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
            <h1>Crear Cuenta</h1>
            <p class="text-muted">Regístrate para comenzar a usar nuestro sistema</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Nombre Completo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autocomplete="name" 
                        autofocus
                        placeholder="Ingresa tu nombre completo"
                    >
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email"
                        placeholder="Ingresa tu correo electrónico"
                    >
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password" 
                        required 
                        autocomplete="new-password"
                        placeholder="Crea una contraseña segura"
                        minlength="8"
                    >
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="far fa-eye"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <small class="form-text">Mínimo 8 caracteres</small>
            </div>

            <div class="form-group">
                <label for="password-confirm" class="form-label">Confirmar Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password-confirm" 
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password"
                        placeholder="Confirma tu contraseña"
                        minlength="8"
                    >
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-check mb-4">
                <input 
                    class="form-check-input @error('terms') is-invalid @enderror" 
                    type="checkbox" 
                    name="terms" 
                    id="terms" 
                    required
                    {{ old('terms') ? 'checked' : '' }}
                >
                <label class="form-check-label" for="terms">
                    Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">términos y condiciones</a>
                </label>
                @error('terms')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-register btn-auth mb-3">
                <i class="fas fa-user-plus me-2"></i> Crear Cuenta
            </button>

            <div class="auth-links">
                ¿Ya tienes una cuenta? 
                <a href="{{ route('login') }}">Inicia sesión aquí</a>
            </div>
        </form>

        <div class="auth-footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Sistema de Ventas') }}. Todos los derechos reservados.
        </div>
    </div>

    <!-- Modal Términos y Condiciones -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Términos y Condiciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Aceptación de los Términos</h6>
                    <p>Al registrarte en nuestro sistema, aceptas cumplir con estos términos y condiciones, así como con nuestra política de privacidad.</p>
                    
                    <h6 class="mt-4">2. Uso de la Cuenta</h6>
                    <p>Eres responsable de mantener la confidencialidad de tu cuenta y contraseña, y de restringir el acceso a tu computadora.</p>
                    
                    <h6 class="mt-4">3. Privacidad</h6>
                    <p>Respetamos tu privacidad y protegemos tus datos personales de acuerdo con nuestra política de privacidad.</p>
                    
                    <h6 class="mt-4">4. Cambios en los Términos</h6>
                    <p>Nos reservamos el derecho de modificar estos términos en cualquier momento. Te notificaremos de cualquier cambio importante.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar/ocultar contraseña
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const passwordInput = this.previousElementSibling;
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Validación de formulario
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>