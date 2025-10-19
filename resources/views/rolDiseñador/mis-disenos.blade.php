@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">📄 Mis Diseños</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Borradores</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $borradores->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pencil-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Terminados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $terminados->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="disenosTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="borradores-tab" data-bs-toggle="tab" data-bs-target="#borradores" type="button" role="tab">
                📦 Borradores ({{ $borradores->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="terminados-tab" data-bs-toggle="tab" data-bs-target="#terminados" type="button" role="tab">
                ✅ Terminados ({{ $terminados->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="disenosTabContent">
        <!-- TAB: BORRADORES -->
        <div class="tab-pane fade show active" id="borradores" role="tabpanel">
            <div class="row mt-4">
                @forelse ($borradores as $diseno)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            @if($diseno->archivo)
                                @php
                                    $extension = pathinfo($diseno->archivo, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $imagePath = asset('storage/' . $diseno->archivo);
                                @endphp
                                @if($isImage)
                                    <img src="{{ $imagePath }}" alt="Diseño" class="card-img-top" style="height: 200px; object-fit: cover; cursor: pointer;" onclick="window.open('{{ $imagePath }}', '_blank')" />
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-file fa-3x text-muted"></i>
                                    </div>
                                @endif
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                @php
                                    $venta = $diseno->detalleVenta->venta ?? null;
                                    $nombreCliente = 'Cliente no especificado';
                                    
                                    if ($venta) {
                                        // Obtener nombre del cliente natural
                                        if ($venta->clienteNatural && $venta->clienteNatural->user) {
                                            $user = $venta->clienteNatural->user;
                                            $nombreCliente = trim($user->name . ' ' . ($user->primerApellido ?? '') . ' ' . ($user->segundApellido ?? ''));
                                        }
                                        // Si no hay cliente natural, obtener del establecimiento
                                        elseif ($venta->clienteEstablecimiento) {
                                            $nombreCliente = $venta->clienteEstablecimiento->razonSocial ?? 'Establecimiento';
                                        }
                                    }
                                @endphp
                                <h5 class="card-title">{{ $nombreCliente }}</h5>
                                <p class="card-text small">{{ $diseno->comentario ?? 'Sin comentario' }}</p>
                                <small class="text-muted d-block mb-2">Creado: {{ $diseno->created_at->format('d/m/Y') }}</small>
                                <a href="{{ route('diseñador.trabajar', $diseno->idDiseno) }}" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-edit"></i> Trabajar
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No tienes borradores asignados en este momento.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB: TERMINADOS -->
        <div class="tab-pane fade" id="terminados" role="tabpanel">
            <div class="mt-4">
                @if($terminados->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Imagen</th>
                                    <th>Cliente</th>
                                    <th>Comentario</th>
                                    <th>Fecha Terminación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $contador = 1; @endphp
                                @foreach ($terminados as $diseno)
                                    @php
                                        $venta = $diseno->detalleVenta->venta ?? null;
                                        $nombreCliente = 'Cliente no especificado';
                                        
                                        if ($venta) {
                                            // Obtener nombre del cliente natural
                                            if ($venta->clienteNatural && $venta->clienteNatural->user) {
                                                $user = $venta->clienteNatural->user;
                                                $nombreCliente = trim($user->name . ' ' . ($user->primerApellido ?? '') . ' ' . ($user->segundApellido ?? ''));
                                            }
                                            // Si no hay cliente natural, obtener del establecimiento
                                            elseif ($venta->clienteEstablecimiento) {
                                                $nombreCliente = $venta->clienteEstablecimiento->razonSocial ?? 'Establecimiento';
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $contador++ }}</td>
                                        <td>
                                            @if($diseno->archivo)
                                                @php
                                                    $extension = pathinfo($diseno->archivo, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    $imagePath = asset('storage/' . $diseno->archivo);
                                                @endphp
                                                @if($isImage)
                                                    <img src="{{ $imagePath }}" alt="Imagen" width="50" height="50" style="object-fit: cover; cursor: pointer;" onclick="window.open('{{ $imagePath }}', '_blank')" />
                                                @else
                                                    <i class="fas fa-file"></i>
                                                @endif
                                            @else
                                                <i class="fas fa-image text-muted"></i>
                                            @endif
                                        </td>
                                        <td>{{ $nombreCliente }}</td>
                                        <td>{{ $diseno->comentario ?? '-' }}</td>
                                        <td>{{ $diseno->updated_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle"></i> No tienes diseños terminados aún.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        color: #4e73df;
        border-bottom-color: #4e73df;
        background-color: transparent;
    }

    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #4e73df;
    }

    .card {
        border: none;
        border-radius: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection
