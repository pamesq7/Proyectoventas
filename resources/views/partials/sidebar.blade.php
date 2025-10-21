<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <hr class="sidebar-divider my-0">

                {{-- MENU PARA DISEÑADOR --}}
                @if(auth()->check() && optional(auth()->user()->empleado)->rol === 'diseñador')
                    <a class="nav-link" href="{{ route('dashboard.disenador') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        🏠 Panel del Diseñador
                    </a>

                    <div class="sb-sidenav-menu-heading">DISEÑADOR</div>
                    <a class="nav-link" href="{{ route('mis-disenos.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-palette"></i></div>
                        🎨 Mis Diseños
                    </a>

                {{-- MENU PARA OPERADOR --}}
                @elseif(auth()->check() && optional(auth()->user()->empleado)->rol === 'operador')
                    <a class="nav-link" href="{{ route('dashboard.operador') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        🏠 Panel del Operador
                    </a>

                    <div class="sb-sidenav-menu-heading">OPERACIONES</div>
                    <a class="nav-link" href="{{ route('rolOperador.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                        📦 Pedidos
                    </a>
                    <a class="nav-link" href="{{ route('rolOperador.catalogo') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                        🛒 Catálogo
                    </a>

                {{-- MENU PARA CLIENTES --}}
                @elseif(auth()->check() && !auth()->user()->empleado)
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

                {{-- MENU PARA ADMINISTRADOR --}}
                @elseif(auth()->check() && optional(auth()->user()->empleado)->rol === 'administrador')
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        🏠 Dashboard Administrador
                    </a>

                    <div class="sb-sidenav-menu-heading">🔧 GESTIÓN USUARIOS</div>
                    <a class="nav-link" href="{{ route('users.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                        👤 Usuarios
                    </a>
                    <a class="nav-link" href="{{ route('empleados.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-tie"></i></div>
                        👔 Empleados
                    </a>
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseClientes">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        👥 Clientes
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseClientes" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('clienteNatural.index') }}">👤 Naturales</a>
                            <a class="nav-link" href="{{ route('clienteEstablecimiento.index') }}">🏢 Establecimientos</a>
                        </nav>
                    </div>

                    <hr class="sidebar-divider">
                    <div class="sb-sidenav-menu-heading">📦 GESTIÓN DE PRODUCTOS</div>
                    <a class="nav-link" href="{{ route('productos.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                        📦 Productos
                    </a>
                    <a class="nav-link" href="{{ route('configuracion.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                        ⚙️ Configuración de Productos
                    </a>

                    <hr class="sidebar-divider">
                    <div class="sb-sidenav-menu-heading">📋 PEDIDOS</div>
                    <a class="nav-link" href="{{ route('pedidos.catalogo') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                        🛒 Catálogo
                    </a>
                    <a class="nav-link" href="{{ route('pedidos.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                        📑 Pedidos
                    </a>

                    <hr class="sidebar-divider">
                    <div class="sb-sidenav-menu-heading">💰 VENTAS</div>
                    <a class="nav-link" href="{{ route('ventas.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                        💰 Ventas
                    </a>

                    <hr class="sidebar-divider">
                    <div class="sb-sidenav-menu-heading">📊 REPORTES</div>
                    <a class="nav-link" href="{{ route('reportes.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                        📈 Reportes
                    </a>
                    <a class="nav-link" href="{{ route('reportes.ventas-mensuales') }}">
                        <i class="fas fa-chart-bar me-2"></i> Reportes de Ventas
                    </a>

                {{-- MENU PARA VENDEDOR --}}
                @elseif(auth()->check() && optional(auth()->user()->empleado)->rol === 'vendedor')
                    <a class="nav-link" href="{{ route('rolVendedor.dashboard') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        🏠 Dashboard Vendedor
                    </a>
                    <a class="nav-link" href="{{ route('pedidos.catalogo') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                        🛒 Catálogo
                    </a>
                    <a class="nav-link" href="{{ route('pedidos.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                        📑 Pedidos
                    </a>

                    <div class="sb-sidenav-menu-heading">💰 VENTAS</div>
                    <a class="nav-link" href="{{ route('ventas.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-money-bill"></i></div>
                        💰 Ventas
                    </a>

                    <div class="sb-sidenav-menu-heading">📊 REPORTES</div>
                    <a class="nav-link" href="{{ route('reportes.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                        📈 Reportes
                    </a>
                    <a class="nav-link" href="{{ route('reportes.ventas-mensuales') }}">
                        <i class="fas fa-chart-bar me-2"></i> Reportes de Ventas
                    </a>
                @endif
            </div>
        </div>

        <div class="sb-sidenav-footer">
            <div class="small">Sistema de Ventas</div>
            Gestión Deportiva
        </div>
    </nav>
</div>
