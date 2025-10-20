<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <hr class="sidebar-divider my-0">

                <!-- Dashboard -->
                @if(auth()->check() && auth()->user()->empleado && auth()->user()->empleado->rol === 'diseñador')
                <!-- Menú específico para diseñador -->
                <a class="nav-link" href="{{ route('dashboard.disenador') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    🏠 Panel del Diseñador
                </a>

                <div class="sb-sidenav-menu-heading">DISEÑADOR</div>

                <!-- 🎨 Mis Diseños - REDIRIGIR A disenos.index -->
                <a class="nav-link" href="{{ route('mis-disenos.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-palette"></i></div>
                    🎨 Mis Diseños
                </a>



               

                @elseif(auth()->check() && auth()->user()->empleado && auth()->user()->empleado->rol === 'operador')
                <!-- Menú específico para operador -->
                <a class="nav-link" href="{{ route('dashboard.operador') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    🏠 Panel del Operador
                </a>

                <div class="sb-sidenav-menu-heading">OPERACIONES</div>

                <!-- Pedidos -->
                <a class="nav-link" href="{{ route('pedidos.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                    📦 Pedidos
                </a>

                <!-- Catálogo -->
                <a class="nav-link" href="{{ route('pedidos.catalogo') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                    🛒 Catálogo
                </a>

                @elseif(auth()->check() && !auth()->user()->empleado)
                <!-- 🆕 MENÚ PARA CLIENTES -->
                <div class="sb-sidenav-menu-heading">👤 MI CUENTA</div>

                <a class="nav-link" href="{{ route('dashboard.cliente') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                    🏠 Mi Dashboard
                </a>

                <a class="nav-link" href="{{ route('cliente.pedidos') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-shopping-bag"></i></div>
                    📦 Mis Pedidos
                </a>

                <a class="nav-link" href="{{ route('cliente.productos') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                    🛍️ Productos
                </a>

                @else
                <!-- Menú para otros roles (admin, vendedor) -->
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    🏠 Dashboard
                </a>

                <!-- Resto del menú para otros roles -->
                @php
                $userRol = auth()->check() && auth()->user()->empleado ? auth()->user()->empleado->rol : null;
                @endphp

                @if(auth()->check() && in_array($userRol, ['administrador', 'vendedor']))
                <div class="sb-sidenav-menu-heading">🔧 GESTION USUARIOS</div>

                @if($userRol === 'administrador')
                <!-- Usuarios -->
                <a class="nav-link" href="{{ route('users.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                    👤 Usuarios
                </a>
                @endif

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
                @endif

                @if(auth()->check() && in_array($userRol, ['administrador', 'vendedor']))
                <hr class="sidebar-divider">

                <!-- 📦 GESTIÓN DE PRODUCTOS -->
                <div class="sb-sidenav-menu-heading">📦 GESTIÓN DE PRODUCTOS</div>

                <!-- Productos -->
                <a class="nav-link" href="{{ route('productos.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                    📦 Productos
                </a>


                @if($userRol === 'administrador')
                <!-- Configuración de Productos -->
                <a class="nav-link" href="{{ route('configuracion.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                    ⚙️ Configuración de Productos
                </a>
                @endif

                <hr class="sidebar-divider">
                <div class="sb-sidenav-menu-heading"><i class="fas fa-clipboard-list me-2"></i>GESTIÓN DE PEDIDOS</div>

                <!-- Catálogo de Productos -->
                <a class="nav-link" href="{{ route('pedidos.catalogo') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                    🛒 Catálogo
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

                @if($userRol === 'administrador')
                <hr class="sidebar-divider">

                <!-- 📊 REPORTES -->
                <div class="sb-sidenav-menu-heading">📊 REPORTES</div>

                <!-- Reportes -->
                <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                    📈 Reportes
                </a>
                <!-- En tu sidebar o navbar -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reportes.ventas-mensuales') }}">
                        <i class="fas fa-chart-bar me-2"></i>
                        Reportes de Ventas
                    </a>
                </li>
                @endif
                @endif
                @endif

            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Sistema de Ventas</div>
            Gestión Deportiva
        </div>
    </nav>
</div>