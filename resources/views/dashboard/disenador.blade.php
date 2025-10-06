@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Panel del Diseñador</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Diseños Pendientes Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Diseños Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Diseno::where('estadoDiseño', 'pendiente')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Diseños en Proceso Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Proceso</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Diseno::where('estadoDiseño', 'en proceso')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Diseños Completados Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Diseno::where('estadoDiseño', 'completado')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mis Diseños Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Mis Diseños</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Diseno::where('idEmpleado', auth()->user()->empleado->idEmpleado ?? 0)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-palette fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Diseños Recientes -->
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Mis Diseños Recientes</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('disenos.mis-disenos') }}">Ver todos</a>
                            <a class="dropdown-item" href="{{ route('disenos.create') }}">Nuevo diseño</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Archivo</th>
                                    <th>Comentario</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $misDisenos = App\Models\Diseno::where('idEmpleado', auth()->user()->empleado->idEmpleado ?? 0)
                                        ->latest()
                                        ->take(5)
                                        ->get();
                                @endphp
                                
                                @forelse ($misDisenos as $diseno)
                                    <tr>
                                        <td>#{{ $diseno->idDiseno }}</td>
                                        <td>
                                            @if($diseno->archivo)
                                                <a href="{{ asset('storage/' . $diseno->archivo) }}" target="_blank">
                                                    {{ Str::limit(basename($diseno->archivo), 20) }}
                                                </a>
                                            @else
                                                <span class="text-muted">Sin archivo</span>
                                            @endif
                                        </td>
                                        <td>{{ $diseno->comentario ? Str::limit($diseno->comentario, 30) : 'Sin comentario' }}</td>
                                        <td>{{ $diseno->created_at ? $diseno->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>
                                            @php
                                                $estadoClases = [
                                                    'pendiente' => 'warning',
                                                    'en proceso' => 'info',
                                                    'completado' => 'success',
                                                    'rechazado' => 'danger'
                                                ][$diseno->estadoDiseño] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $estadoClases }}">
                                                {{ ucfirst($diseno->estadoDiseño) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('disenos.show', $diseno->idDiseno) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($diseno->estadoDiseño != 'completado')
                                                <a href="{{ route('disenos.edit', $diseno->idDiseno) }}" 
                                                   class="btn btn-warning btn-sm" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No tienes diseños asignados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
    .mb-0, .my-0 {
        margin-bottom: 0 !important;
    }
    .no-underline {
        text-decoration: none !important;
    }
</style>

<!-- Page level plugins -->
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

<!-- Page level custom scripts -->
<script>
    // Aquí puedes agregar scripts personalizados para gráficos si es necesario
</script>
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
