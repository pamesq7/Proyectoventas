@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-chart-bar text-primary me-2"></i>
        Reportes
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Reportes</li>
    </ol>

    <!-- Estadísticas Principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Usuarios</div>
                            <div class="h5 mb-0">{{ $estadisticas['total_usuarios'] }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Productos</div>
                            <div class="h5 mb-0">{{ $estadisticas['total_productos'] }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Pedidos</div>
                            <div class="h5 mb-0">{{ $estadisticas['total_pedidos'] }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Ventas Totales</div>
                            <div class="h5 mb-0">S/. {{ number_format($estadisticas['total_ventas'], 2) }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="row">
        <!-- Estados de Pedidos -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Estados de Pedidos
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="h4 text-info">{{ $estadosPedidos['solicitados'] }}</div>
                            <div class="small">Solicitados</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 text-warning">{{ $estadosPedidos['en_diseno'] }}</div>
                            <div class="small">En Diseño</div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-primary">{{ $estadosPedidos['en_confeccion'] }}</div>
                            <div class="small">En Confección</div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success">{{ $estadosPedidos['entregados'] }}</div>
                            <div class="small">Entregados</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estados de Pago -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-money-bill me-2"></i>
                    Estados de Pago
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="h4 text-success">{{ $estadosPago['pagados'] }}</div>
                            <div class="small">Pagados</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 text-danger">{{ $estadosPago['pendientes'] }}</div>
                            <div class="small">Pendientes</div>
                        </div>
                        <div class="col-12">
                            <div class="h5 text-warning">S/. {{ number_format($estadisticas['saldo_pendiente'], 2) }}</div>
                            <div class="small">Saldo Total Pendiente</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Clientes y Productos -->
    <div class="row">
        <!-- Tipos de Clientes -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users me-2"></i>
                    Tipos de Clientes
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-primary">{{ $estadisticas['clientes_naturales'] }}</div>
                            <div class="small">Clientes Naturales</div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-info">{{ $estadisticas['clientes_establecimientos'] }}</div>
                            <div class="small">Establecimientos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Productos -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-trophy me-2"></i>
                    Top 5 Productos Más Vendidos
                </div>
                <div class="card-body">
                    @if($topProductos->count() > 0)
                        @foreach($topProductos as $producto)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small">{{ $producto->nombre }}</span>
                                <span class="badge bg-primary">{{ $producto->total_vendido }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted small">No hay datos de productos vendidos</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Ventas del Mes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Ventas del Mes Actual
                </div>
                <div class="card-body text-center">
                    <div class="h3 text-success">S/. {{ number_format($ventasMesActual, 2) }}</div>
                    <p class="text-muted">{{ now()->format('F Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Enlaces Rápidos -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-link me-2"></i>
                    Enlaces Rápidos
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('ventas.index') }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-shopping-cart me-1"></i> Gestión de Ventas
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('ventas.morosos') }}" class="btn btn-outline-warning btn-sm w-100">
                                <i class="fas fa-exclamation-triangle me-1"></i> Clientes Morosos
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('pedidos.index') }}" class="btn btn-outline-info btn-sm w-100">
                                <i class="fas fa-clipboard-list me-1"></i> Pedidos
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('productos.index') }}" class="btn btn-outline-success btn-sm w-100">
                                <i class="fas fa-box me-1"></i> Productos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reportes PDF Consolidados -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file-pdf me-2 text-danger"></i>
                    Reportes PDF Consolidados
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                    <h6 class="card-title">Usuarios</h6>
                                    <p class="card-text small text-muted">Lista completa de usuarios del sistema</p>
                                    <a href="{{ route('export.usuarios.pdf') }}" class="btn btn-primary btn-sm" target="_blank">
                                        <i class="fas fa-download me-1"></i> Descargar PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-tie fa-2x text-success mb-2"></i>
                                    <h6 class="card-title">Empleados</h6>
                                    <p class="card-text small text-muted">Reporte de empleados y roles</p>
                                    <a href="{{ route('export.empleados.pdf') }}" class="btn btn-success btn-sm" target="_blank">
                                        <i class="fas fa-download me-1"></i> Descargar PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-secondary">
                                <div class="card-body text-center">
                                    <i class="fas fa-boxes fa-2x text-secondary mb-2"></i>
                                    <h6 class="card-title">Productos</h6>
                                    <p class="card-text small text-muted">Catálogo completo de productos</p>
                                    <a href="{{ route('export.productos.pdf') }}" class="btn btn-secondary btn-sm" target="_blank">
                                        <i class="fas fa-download me-1"></i> Descargar PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-dark">
                                <div class="card-body text-center">
                                    <i class="fas fa-palette fa-2x text-dark mb-2"></i>
                                    <h6 class="card-title">Diseños</h6>
                                    <p class="card-text small text-muted">Lista completa de diseños</p>
                                    <a href="{{ route('export.disenos.pdf') }}" class="btn btn-dark btn-sm" target="_blank">
                                        <i class="fas fa-download me-1"></i> Descargar PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-list fa-2x text-primary mb-2"></i>
                                    <h6 class="card-title">Pedidos</h6>
                                    <p class="card-text small text-muted">Historial completo de pedidos</p>
                                    <a href="{{ route('export.pedidos.pdf') }}" class="btn btn-primary btn-sm" target="_blank">
                                        <i class="fas fa-download me-1"></i> Descargar PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-friends fa-2x text-warning mb-2"></i>
                                    <h6 class="card-title">Clientes Naturales</h6>
                                    <p class="card-text small text-muted">Solo clientes naturales</p>
                                    <a href="{{ route('export.clientes-naturales.pdf') }}" class="btn btn-warning btn-sm" target="_blank">
                                        <i class="fas fa-download me-1"></i> Descargar PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mx-auto">
                            <div class="card border-danger">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-pie fa-2x text-danger mb-2"></i>
                                    <h6 class="card-title">Reporte Consolidado de Clientes</h6>
                                    <p class="card-text small text-muted">Todos los clientes (naturales + establecimientos) con estadísticas completas</p>
                                    <a href="{{ route('export.clientes-consolidado.pdf') }}" class="btn btn-danger btn-sm" target="_blank">
                                        <i class="fas fa-download me-1"></i> Descargar Reporte Completo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
