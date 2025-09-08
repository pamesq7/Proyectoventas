@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 text-center mb-5">
            <h1 class="display-4 fw-bold text-primary">Bienvenido a Nuestro Sistema de Ventas</h1>
            <p class="lead text-muted">Gestiona tus productos, clientes y ventas de manera eficiente</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-paint-brush fa-2x text-primary"></i>
                    </div>
                    <h4 class="h5 mb-3">Productos Personalizados</h4>
                    <p class="text-muted mb-0">Crea diseños únicos con nuestro editor fácil de usar.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-truck fa-2x text-success"></i>
                    </div>
                    <h4 class="h5 mb-3">Envío Rápido</h4>
                    <p class="text-muted mb-0">Recibe tus productos en tiempo récord con nuestro servicio de envío exprés.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-headset fa-2x text-info"></i>
                    </div>
                    <h4 class="h5 mb-3">Soporte 24/7</h4>
                    <p class="text-muted mb-0">Nuestro equipo está listo para ayudarte en cualquier momento.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection