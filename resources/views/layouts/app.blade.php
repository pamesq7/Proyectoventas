<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.head')
    <style>
        /* Asegurar que el navbar esté siempre visible */
        .sb-topnav {
            z-index: 1039;
            position: fixed;
            width: 100%;
            top: 0;
        }

        /* Ajustar el padding del body para que el contenido no quede debajo del navbar */
        body.sb-nav-fixed #layoutSidenav {
            padding-top: 56px;
        }

        /* Asegurar que el sidebar no se superponga al navbar */
        #layoutSidenav_nav {
            z-index: 1038;
            height: calc(100vh - 56px);
            margin-top: 56px;
        }

        /* Estilos para el menú de usuario */
        .dropdown-menu {
            z-index: 1040;
        }

        /* Estilos para notificaciones */
        .notification-alert {
            min-width: 350px;
            max-width: 450px;
            border-radius: 10px;
            border: none;
            margin-bottom: 15px;
            animation: slideInRight 0.5s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .notification-alert .btn-close {
            margin-left: 15px;
        }

        .notification-alert i {
            font-size: 1.2rem;
        }
    </style>
    @stack('styles')
</head>

<body class="sb-nav-fixed">
    @include('partials.navbar')

    <div id="layoutSidenav">
        @include('partials.sidebar')

        <div id="layoutSidenav_content">
            <main>
                {{-- Sistema de Notificaciones --}}
                @if(session('success') || session('error') || session('warning') || session('info'))
                <div id="notification-container" class="position-fixed" style="top: 70px; right: 20px; z-index: 9999;">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-lg notification-alert" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                            <div>
                                <strong>¡Éxito!</strong><br>
                                {{ session('success') }}
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-lg notification-alert" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle fa-lg me-3 text-danger"></i>
                            <div>
                                <strong>¡Error!</strong><br>
                                {{ session('error') }}
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                    @endif

                    @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show shadow-lg notification-alert" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-lg me-3 text-warning"></i>
                            <div>
                                <strong>¡Atención!</strong><br>
                                {{ session('warning') }}
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                    @endif

                    @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show shadow-lg notification-alert" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-lg me-3 text-info"></i>
                            <div>
                                <strong>Información</strong><br>
                                {{ session('info') }}
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <div class="container-fluid px-4">
                    @yield('content')
                </div>
            </main>

            @include('partials.footer')
        </div>
    </div>

    {{-- Botón flotante para abrir el carrito lateral --}}
    <button id="carritoLateralToggle" ...>
        <i class="fas fa-shopping-cart me-2"></i>
        <span class="badge bg-danger" id="carritoCantidadBadge">
            {{ count(session('carrito', [])) }}
        </span>
    </button>


    {{-- Carrito lateral (offcanvas) --}}
    @include('partials.carrito-lateral')

    {{-- Scripts base (Bootstrap, jQuery, etc.) --}}
    @include('partials.js')

    {{-- Scripts específicos de vistas --}}
    @stack('scripts')

    {{-- Script de notificaciones + carrito --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==========================
            // 1) NOTIFICACIONES
            // ==========================
            const notifications = document.querySelectorAll('.notification-alert');
            notifications.forEach(function(notification) {
                setTimeout(function() {
                    if (notification && notification.classList.contains('show')) {
                        notification.style.transition = 'opacity 0.5s ease-out';
                        notification.style.opacity = '0';

                        setTimeout(function() {
                            if (notification && notification.parentNode) {
                                notification.remove();
                            }
                        }, 500);
                    }
                }, 6000);
            });

            document.querySelectorAll('.notification-alert .btn-close').forEach(function(button) {
                button.addEventListener('click', function() {
                    const alert = this.closest('.alert');
                    if (alert) {
                        alert.style.transition = 'opacity 0.5s ease-out';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            if (alert && alert.parentNode) {
                                alert.remove();
                            }
                        }, 500);
                    }
                });
            });

            document.querySelectorAll('.notification-alert').forEach(function(notification) {
                notification.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                });

                notification.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            window.showNotification = function(type, message, title = null) {
                const container = document.getElementById('notification-container') || createNotificationContainer();
                const alertClass = getAlertClass(type);
                const icon = getIcon(type);
                const alertTitle = title || getDefaultTitle(type);

                const notification = document.createElement('div');
                notification.className = `alert ${alertClass} alert-dismissible fade show shadow-lg notification-alert`;
                notification.setAttribute('role', 'alert');

                notification.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="${icon} fa-lg me-3"></i>
                        <div>
                            <strong>${alertTitle}</strong><br>
                            ${message}
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                `;

                container.appendChild(notification);

                setTimeout(function() {
                    if (notification && notification.classList.contains('show')) {
                        notification.style.transition = 'opacity 0.5s ease-out';
                        notification.style.opacity = '0';
                        setTimeout(function() {
                            if (notification && notification.parentNode) {
                                notification.remove();
                            }
                        }, 500);
                    }
                }, 6000);

                notification.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                });

                notification.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });

                const closeButton = notification.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.addEventListener('click', function() {
                        notification.style.transition = 'opacity 0.5s ease-out';
                        notification.style.opacity = '0';
                        setTimeout(function() {
                            if (notification && notification.parentNode) {
                                notification.remove();
                            }
                        }, 500);
                    });
                }

                return notification;
            };

            function createNotificationContainer() {
                const container = document.createElement('div');
                container.id = 'notification-container';
                container.className = 'position-fixed';
                container.style.top = '70px';
                container.style.right = '20px';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
                return container;
            }

            function getAlertClass(type) {
                const types = {
                    'success': 'alert-success',
                    'error': 'alert-danger',
                    'warning': 'alert-warning',
                    'info': 'alert-info'
                };
                return types[type] || 'alert-info';
            }

            function getIcon(type) {
                const icons = {
                    'success': 'fas fa-check-circle text-success',
                    'error': 'fas fa-exclamation-circle text-danger',
                    'warning': 'fas fa-exclamation-triangle text-warning',
                    'info': 'fas fa-info-circle text-info'
                };
                return icons[type] || 'fas fa-info-circle text-info';
            }

            function getDefaultTitle(type) {
                const titles = {
                    'success': '¡Éxito!',
                    'error': '¡Error!',
                    'warning': '¡Atención!',
                    'info': 'Información'
                };
                return titles[type] || 'Mensaje';
            }

            // ==========================
            // 2) CARRITO LATERAL (OFFCANVAS)
            // ==========================
            const carritoToggle = document.getElementById('carritoLateralToggle');
            const carritoLateral = document.getElementById('carritoLateral');
            const carritoContenido = document.getElementById('carritoContenido');
            const contadorCarrito = document.getElementById('carritoCantidadBadge');

            const offcanvasCarrito = carritoLateral ?
                bootstrap.Offcanvas.getOrCreateInstance(carritoLateral) :
                null;

            function abrirCarrito() {
                if (offcanvasCarrito) {
                    offcanvasCarrito.show();
                    actualizarCarrito();
                }
            }

            if (carritoToggle) {
                carritoToggle.addEventListener('click', function() {
                    abrirCarrito();
                });
            }

            function actualizarCarrito() {
                fetch('{{ route("carrito.ver") }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) return;

                        if (carritoContenido) {
                            carritoContenido.innerHTML = data.html;
                        }

                        if (contadorCarrito) {
                            contadorCarrito.textContent = data.count;
                            contadorCarrito.style.display = data.count > 0 ? 'inline-block' : 'none';
                        }
                    })
                    .catch(err => console.error(err));
            }

            // Delegación global para eliminar ítems y vaciar carrito
            document.addEventListener('click', function(e) {
                const btnEliminar = e.target.closest('.btnEliminarItemCarrito');
                const btnEliminarProducto = e.target.closest('.btnEliminarProductoCarrito');
                const btnVaciar = e.target.closest('#btnVaciarCarrito');

                // Eliminar ítem individual (por index)
                if (btnEliminar) {
                    e.preventDefault();
                    const index = btnEliminar.dataset.index;
                    if (!index && index !== 0) return;

                    fetch(`{{ route('carrito.eliminar', '') }}/${index}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.ok) {
                                actualizarCarrito();
                            } else {
                                alert(data.mensaje || 'Error al eliminar');
                            }
                        })
                        .catch(err => console.error(err));
                }

                // Eliminar producto completo (por idProducto)
                if (btnEliminarProducto) {
                    e.preventDefault();
                    const idProducto = btnEliminarProducto.dataset.idproducto;
                    if (!idProducto) return;

                    if (confirm('¿Eliminar este producto del carrito?')) {
                        fetch(`{{ route('carrito.eliminar-producto', '') }}/${idProducto}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(r => r.json())
                            .then(data => {
                                if (data.ok) {
                                    actualizarCarrito();
                                } else {
                                    alert(data.mensaje || 'Error al eliminar producto');
                                }
                            })
                            .catch(err => console.error(err));
                    }
                }

                // Vaciar carrito
                if (btnVaciar) {
                    e.preventDefault();
                    if (!confirm('¿Estás seguro de vaciar el carrito?')) return;

                    fetch(`{{ route('carrito.vaciar') }}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.ok) {
                                actualizarCarrito();
                                showNotification('success', 'El carrito se ha vaciado correctamente');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showNotification('error', 'Error al vaciar el carrito');
                        });
                }
            });

            // Estado inicial del badge
            if (contadorCarrito) {
                if (contadorCarrito) {
                    const carritoCount = parseInt("{{ count(session('carrito', [])) }}", 10) || 0;
                    contadorCarrito.style.display = carritoCount > 0 ? 'inline-block' : 'none';
                }

            }
        });
    </script>
</body>

</html>