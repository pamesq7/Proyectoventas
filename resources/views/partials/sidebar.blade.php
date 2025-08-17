<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <hr class="sidebar-divider my-0">

                <!-- Dashboard -->
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    🏠 Dashboard
                </a>

                
                <!-- 🔧 GESTION USUARIO -->
                <div class="sb-sidenav-menu-heading">🔧 GESTION USUARIOS</div>

                <!-- Usuarios -->
                <a class="nav-link" href="{{ route('users.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                    👤 Usuarios
                </a>

                <!-- Empleados -->
                <a class="nav-link" href="#">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-tie"></i></div>
                    👔 Empleados
                </a>

                <!-- Clientes con Submenú -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseClientes"
                    aria-expanded="false" aria-controls="collapseClientes">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    👥 Clientes
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseClientes" aria-labelledby="headingClientes" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ route('clienteNatural.index') }}">👤 Naturales</a>
                        <a class="nav-link" href="{{ route('clienteEstablecimiento.index') }}">🏢 Establecimientos</a>
                    </nav>
                </div>

                <hr class="sidebar-divider">

                <!-- 📦 GESTIÓN DE PRODUCTOS -->
                <div class="sb-sidenav-menu-heading">📦 GESTIÓN DE PRODUCTOS</div>

                <!-- Productos -->
                <a class="nav-link" href="{{ route('productos.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                    📦 Productos
                </a>

                <!-- Configuración de Productos -->
                <a class="nav-link" href="{{ route('configuracion.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                    ⚙️ Configuración de Productos
                </a>

                <hr class="sidebar-divider">
                <div class="sb-sidenav-menu-heading"><i class="fas fa-clipboard-list me-2"></i>GESTIÓN DE PEDIDOS</div>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Sistema de Ventas</div>
            Gestión Deportiva
        </div>
    </nav>
</div>