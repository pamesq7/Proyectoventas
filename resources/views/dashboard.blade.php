@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Panel Principal</li>
            </ol>
        </div>
    </div>

    <!-- Bienvenida Simple -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body text-center py-5">
                    <h2 class="mb-3">
                        <i class="fas fa-store me-2"></i>
                        Sistema de Ventas
                    </h2>
                    <p class="lead mb-0">Gestión Deportiva - Panel de Control</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Principales -->
    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Gestión de Usuarios
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-users me-2"></i>Usuarios y Clientes
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Gestión de Productos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-box me-2"></i>Productos y Ventas
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Reportes y Análisis
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-chart-bar me-2"></i>Estadísticas
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-analytics fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #4e73df, #224abe);
}
</style>
@endpush