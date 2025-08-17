<!DOCTYPE html>
<html lang="en">
    <head>
        @include('partials.head')
    </head>
    <body class="sb-nav-fixed">
         @include('partials.navbar')
        <div id="layoutSidenav">
            @include('partials.sidebar')
            <div id="layoutSidenav_content">
                <main>
                    {{-- Sistema de Notificaciones --}}
                    @if(session('success') || session('error') || session('warning') || session('info'))
                        <div id="notification-container" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
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
                    
                     @yield('content')
                </main>
                @include('partials.footer')
            </div>
        </div>
         
         @include('partials.js')
         
         {{-- Estilos para notificaciones --}}
         <style>
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
         
         {{-- JavaScript para notificaciones --}}
         <script>
         document.addEventListener('DOMContentLoaded', function() {
             // Auto-hide notifications after 6 seconds
             const notifications = document.querySelectorAll('.notification-alert');
             notifications.forEach(function(notification) {
                 setTimeout(function() {
                     if (notification && notification.classList.contains('show')) {
                         const bsAlert = new bootstrap.Alert(notification);
                         bsAlert.close();
                     }
                 }, 6000);
             });
             
             // Add subtle animation on hover
             notifications.forEach(function(notification) {
                 notification.addEventListener('mouseenter', function() {
                     this.style.transform = 'scale(1.02)';
                     this.style.transition = 'transform 0.2s ease';
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
                         const bsAlert = new bootstrap.Alert(notification);
                         bsAlert.close();
                     }
                 }, 6000);
             };
             
             function createNotificationContainer() {
                 const container = document.createElement('div');
                 container.id = 'notification-container';
                 container.className = 'position-fixed';
                 container.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
                 document.body.appendChild(container);
                 return container;
             }
             
             function getAlertClass(type) {
                 const classes = {
                     'success': 'alert-success',
                     'error': 'alert-danger',
                     'warning': 'alert-warning',
                     'info': 'alert-info'
                 };
                 return classes[type] || 'alert-info';
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
                 return titles[type] || 'Notificación';
             }
         });
         </script>
         
          <!-- jQuery y Bootstrap JS -->
          <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          
          @stack('scripts')
     </body>
</html>
