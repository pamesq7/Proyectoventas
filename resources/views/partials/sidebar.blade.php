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
                <a class="nav-link {{ request()->routeIs('empleados.*') ? 'active' : '' }}" href="{{ route('empleados.index') }}">
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

                <!-- Diseños -->
                <a class="nav-link" href="{{ route('disenos.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-palette"></i></div>
                    🎨 Diseños
                </a>

                <!-- Configuración de Productos -->
                <a class="nav-link" href="{{ route('configuracion.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                    ⚙️ Configuración de Productos
                </a>

                <hr class="sidebar-divider">
                <div class="sb-sidenav-menu-heading"><i class="fas fa-clipboard-list me-2"></i>GESTIÓN DE PEDIDOS</div>
                
                <!-- Catálogo de Productos -->
                <a class="nav-link" href="{{ route('pedidos.catalogo') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                    🛒 Catálogo
                </a>

                <!-- Personalizar mi diseño -->
                <a class="nav-link" href="{{ route('pedidos.personalizar') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-magic"></i></div>
                    ✨ Personalizar mi diseño
                </a>



                <!-- Pedidos (Administración) -->
                <a class="nav-link" href="{{ route('pedidos.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                    📑 Pedidos
                </a>

                <hr class="sidebar-divider">

                <!-- 💰 GESTIÓN DE VENTAS -->
                <div class="sb-sidenav-menu-heading">💰 GESTIÓN DE VENTAS</div>

                <!-- Ventas con Submenú -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseVentas"
                    aria-expanded="false" aria-controls="collapseVentas">
                    <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                    💰 Ventas
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseVentas" aria-labelledby="headingVentas" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ route('ventas.index') }}">📊 Gestión de Ventas</a>
                       
                    </nav>
                </div>

                <hr class="sidebar-divider">

                <!-- 📊 REPORTES -->
                <div class="sb-sidenav-menu-heading">📊 REPORTES</div>

                <!-- Reportes -->
                <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                    📈 Reportes
                </a>

            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Sistema de Ventas</div>
            Gestión Deportiva
        </div>
    </nav>
</div>