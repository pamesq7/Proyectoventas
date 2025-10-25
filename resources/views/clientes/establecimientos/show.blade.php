@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detalles del Cliente Establecimiento</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('clienteEstablecimiento.index') }}">Clientes Establecimientos</a></li>
        <li class="breadcrumb-item active">{{ $clienteEstablecimiento->razonSocial }}</li>
    </ol>

    <div class="row">
        {{-- Información Principal del Establecimiento --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>
                        {{ $clienteEstablecimiento->razonSocial }}
                        @if($clienteEstablecimiento->estado == 1 && $clienteEstablecimiento->representante->estado == 1)
                            <span class="badge bg-success ms-2">
                                <i class="fas fa-check me-1"></i>Activo
                            </span>
                        @else
                            <span class="badge bg-danger ms-2">
                                <i class="fas fa-times me-1"></i>Inactivo
                            </span>
                        @endif
                        <span class="badge bg-info ms-1">{{ $clienteEstablecimiento->tipoEstablecimiento }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Información del Establecimiento --}}
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Información del Establecimiento</h6>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">NIT:</label>
                                <div>
                                    <code class="bg-light px-2 py-1 rounded fs-6">{{ $clienteEstablecimiento->nit }}</code>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Razón Social:</label>
                                <div class="fw-bold">{{ $clienteEstablecimiento->razonSocial }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Tipo de Establecimiento:</label>
                                <div>
                                    <span class="badge bg-info fs-6">{{ $clienteEstablecimiento->tipoEstablecimiento }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Domicilio Fiscal:</label>
                                <div class="text-muted">{{ $clienteEstablecimiento->domicilioFiscal }}</div>
                            </div>
                        </div>
                        
                        {{-- Información del Representante Legal --}}
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Representante Legal</h6>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Nombre Completo:</label>
                                <div class="fw-bold">{{ $clienteEstablecimiento->representante->nombre_completo }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Cédula de Identidad:</label>
                                <div>
                                    <code class="bg-light px-2 py-1 rounded">{{ $clienteEstablecimiento->representante->ci }}</code>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Email:</label>
                                <div>
                                    <a href="mailto:{{ $clienteEstablecimiento->representante->email }}" class="text-decoration-none">
                                        {{ $clienteEstablecimiento->representante->email }}
                                    </a>
                                    @if($clienteEstablecimiento->representante->email_verified_at)
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-check-circle me-1"></i>Verificado
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($clienteEstablecimiento->representante->telefono)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Teléfono:</label>
                                    <div>
                                        <a href="tel:{{ $clienteEstablecimiento->representante->telefono }}" class="text-decoration-none">
                                            {{ $clienteEstablecimiento->representante->telefono }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
        
        {{-- Panel de Información del Sistema --}}
        <div class="col-lg-4">
            {{-- Información del Sistema --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Información del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">ID del Establecimiento:</label>
                        <div><code>#{{ $clienteEstablecimiento->idEstablecimiento }}</code></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">ID del Representante:</label>
                        <div><code>#{{ $clienteEstablecimiento->representante->idUser }}</code></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Fecha de Registro:</label>
                        <div>{{ $clienteEstablecimiento->created_at ? $clienteEstablecimiento->created_at->format('d/m/Y H:i') : 'No disponible' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Última Actualización:</label>
                        <div>{{ $clienteEstablecimiento->updated_at ? $clienteEstablecimiento->updated_at->format('d/m/Y H:i') : 'No disponible' }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted">Estado del Establecimiento:</label>
                        <div>
                            @if($clienteEstablecimiento->estado == 1)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- Acciones Rápidas --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cogs me-1"></i>
                        Acciones
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('clienteEstablecimiento.edit', $clienteEstablecimiento->idEstablecimiento) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>
                            Editar Cliente
                        </a>

                        <a href="{{ route('clienteEstablecimiento.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Volver a la Lista
                        </a>
                    </div>
                </div>
            </div>

            {{-- Resumen Rápido --}}
            @if($estadisticas['total_ventas'] > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-1"></i>
                            Resumen de Actividad
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <div class="h4 mb-0 text-primary">{{ $estadisticas['total_ventas'] }}</div>
                                    <small class="text-muted">Ventas</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="h4 mb-0 text-warning">{{ $estadisticas['ventas_este_mes'] }}</div>
                                <small class="text-muted">Este Mes</small>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <div class="h5 mb-0 text-success">Bs. {{ number_format($estadisticas['monto_total'], 2) }}</div>
                            <small class="text-muted">Total Gastado</small>
                        </div>
                    </div>
                </div>
            @else
            @endif
        </div>
    </div>
</div>
@endsection
