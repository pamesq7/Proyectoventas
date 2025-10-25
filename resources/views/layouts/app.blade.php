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
        
        @include('partials.js')
        
        @stack('scripts')
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide notifications after 6 seconds
            const notifications = document.querySelectorAll('.notification-alert');
            notifications.forEach(function(notification) {
                setTimeout(function() {
                    if (notification && notification.classList.contains('show')) {
                        notification.style.transition = 'opacity 0.5s ease-out';
                        notification.style.opacity = '0';
                        
                        // Remove the notification from the DOM after the fade out
                        setTimeout(function() {
                            if (notification && notification.parentNode) {
                                notification.remove();
                            }
                        }, 500);
                    }
                }, 6000);
            });
            
            // Close button functionality
            document.querySelectorAll('.btn-close').forEach(function(button) {
                button.addEventListener('click', function() {
                    const alert = this.closest('.alert');
                    if (alert) {
                        alert.style.transition = 'opacity 0.5s ease-out';
                        alert.style.opacity = '0';
                        
                        // Remove the notification from the DOM after the fade out
                        setTimeout(function() {
                            if (alert && alert.parentNode) {
                                alert.remove();
                            }
                        }, 500);
                    }
                });
            });
            
            // Hover effect for notifications
            document.querySelectorAll('.notification-alert').forEach(function(notification) {
                notification.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                });
                
                notification.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
            
            // Function to show dynamic notifications
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
                
                // Auto-hide after 6 seconds
                setTimeout(function() {
                    if (notification && notification.classList.contains('show')) {
                        if (notification) {
                            const bsAlert = new bootstrap.Alert(notification);
                            bsAlert.close();
                        }
                    }
                    
                    if (notification) {
                        notification.style.transition = 'opacity 0.5s ease-out';
                        notification.style.opacity = '0';
                        
                        // Remove the notification from the DOM after the fade out
                        setTimeout(function() {
                            if (notification && notification.parentNode) {
                                notification.remove();
                            }
                        }, 500);
                    }
                }, 6000);
                
                // Hover effect for the new notification
                notification.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                });
                
                notification.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
                
                // Close button functionality for the new notification
                const closeButton = notification.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.addEventListener('click', function() {
                        if (notification) {
                            notification.style.transition = 'opacity 0.5s ease-out';
                            notification.style.opacity = '0';
                            
                            // Remove the notification from the DOM after the fade out
                            setTimeout(function() {
                                if (notification && notification.parentNode) {
                                    notification.remove();
                                }
                            }, 500);
                        }
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
            
           
        });
        </script>
    </body>
</html>
