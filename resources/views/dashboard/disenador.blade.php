@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">🎨 Panel del Diseñador</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Estadísticas Rápidas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Borradores Asignados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $disenos->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mostrar la cantidad de diseños terminados -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                $empleadoId = auth()->user()->empleado ? auth()->user()->empleado->idEmpleado : null;
                                $terminadosCount = $empleadoId ? App\Models\Diseno::where('idEmpleado', $empleadoId)->where('estadoDiseño', 'terminado')->count() : 0;
                                @endphp
                                {{ $terminadosCount }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Borradores Asignados - Tabla de Diseños -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📦 Mis Diseños Asignados</h6>
                    <small class="text-muted d-block mt-2">
                        Total de diseños: {{ $disenos->count() }} | Empleado ID: {{ auth()->user()->empleado->idEmpleado ?? 'N/A' }}
                    </small>
                </div>
                <div class="card-body">
                    @if($disenos && $disenos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Imagen</th>
                                    <th>Comentario</th>
                                    <th>Estado Diseño</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $contador = 1; @endphp
                                @forelse($disenos as $diseno)
                                <tr>
                                    <td>{{ $contador++ }}</td>
                                    <td style="width: 120px;">
                                        @if($diseno->archivo)
                                        @php
                                        $extension = pathinfo($diseno->archivo, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $imagePath = asset('storage/' . $diseno->archivo);
                                        @endphp

                                        @if($isImage)
                                        <div class="text-center">
                                            <img src="{{ $imagePath }}"
                                                alt="Diseño"
                                                class="img-thumbnail"
                                                style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                                onclick="window.open('{{ $imagePath }}', '_blank')"
                                                onerror="this.style.display='none';">
                                            <div class="image-fallback" style="display: none;">
                                                <i class="fas fa-image text-muted"></i>
                                                <br>
                                                <small class="text-muted">Imagen no disponible</small>
                                            </div>
                                        </div>
                                        @else
                                        <div class="text-center">
                                            <i class="fas fa-file fa-2x text-muted"></i>
                                            <br>
                                            <small class="text-muted">{{ strtoupper($extension) }}</small>
                                            <br>
                                            <a href="{{ $imagePath }}" target="_blank" class="btn btn-xs btn-outline-info mt-1">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                        @endif
                                        @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-image fa-2x"></i>
                                            <br>
                                            <small>Sin archivo</small>
                                        </div>
                                        @endif
                                    </td>
                                    <td>{{ $diseno->comentario ?? 'Sin comentario' }}</td>
                                    <td>
                                        @php
                                        $badgeClass = 'secondary';
                                        if ($diseno->estadoDiseño == 'terminado') {
                                        $badgeClass = 'success';
                                        } elseif ($diseno->estadoDiseño == 'borrador') {
                                        $badgeClass = 'warning';
                                        } elseif ($diseno->estadoDiseño == 'no realizado') {
                                        $badgeClass = 'secondary';
                                        }
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}" style="color: black;">
                                            {{ ucfirst($diseno->estadoDiseño) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                        $venta = $diseno->detalleVenta->venta ?? null;
                                        @endphp
                                        @if($venta)
                                        @if($venta->clienteNatural && $venta->clienteNatural->user)
                                        {{ $venta->clienteNatural->user->name }}
                                        {{ $venta->clienteNatural->user->primerApellido }}
                                        @elseif($venta->clienteEstablecimiento)
                                        {{ $venta->clienteEstablecimiento->razonSocial }}
                                        @else
                                        <span class="text-muted">Cliente no especificado</span>
                                        @endif
                                        @else
                                        <span class="text-muted">Sin venta asociada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $diseno->estado ? 'success' : 'danger' }}" style="color: black;">
                                            {{ $diseno->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>{{ $diseno->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('diseñador.trabajar', $diseno->idDiseno) }}" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-edit"></i> Trabajar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No tienes borradores asignados en este momento.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i> No tienes borradores asignados en este momento.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom styles for this page -->
<style>
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }

    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }

    .text-xs {
        font-size: 0.7rem;
    }

    .text-gray-300 {
        color: #dddfeb !important;
    }

    .h5 {
        font-size: 1.25rem;
    }

    .mb-0,
    .my-0 {
        margin-bottom: 0 !important;
    }

    .no-underline {
        text-decoration: none !important;
    }
</style>
@endsection