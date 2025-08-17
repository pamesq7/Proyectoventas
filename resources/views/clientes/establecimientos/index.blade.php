@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Clientes Establecimientos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Clientes Establecimientos</li>
    </ol>

    {{-- Mensajes Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('successdelete'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-trash me-2"></i>
            {{ session('successdelete') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Estadísticas Rápidas --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Establecimientos</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $estadisticas['total_establecimientos'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x"></i>
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
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Establecimientos Activos</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $estadisticas['establecimientos_activos'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Ventas</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $estadisticas['total_ventas'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x"></i>
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
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Monto Total</div>
                            <div class="h5 mb-0 font-weight-bold">Bs. {{ number_format($estadisticas['monto_total'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas por Tipo de Establecimiento --}}
    @if($estadisticas['tipos_establecimiento']->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-1"></i>
                            Distribución por Tipo de Establecimiento
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($estadisticas['tipos_establecimiento'] as $tipo => $cantidad)
                                <div class="col-md-2 col-sm-4 text-center mb-3">
                                    <div class="border rounded p-3">
                                        <div class="h4 mb-1 text-primary">{{ $cantidad }}</div>
                                        <small class="text-muted">{{ $tipo }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabla de Establecimientos --}}
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    Lista de Clientes Establecimientos
                </div>
                <a href="{{ route('clienteEstablecimiento.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Nuevo Cliente Establecimiento
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="establecimientosTable">
                    <thead class="table-dark">
                        <tr>
                            <th>NIT</th>
                            <th>Establecimiento</th>
                            <th>Representante</th>
                            <th>Tipo</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Ventas</th>
                            <th>Monto Total</th>
                            <th>Última Venta</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientesEstablecimientos as $establecimiento)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $establecimiento->nit }}</code>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ strtoupper(substr($establecimiento->razonSocial, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $establecimiento->razonSocial }}</div>
                                            <small class="text-muted">ID: #{{ $establecimiento->idEstablecimiento }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-bold">{{ $establecimiento->representante->nombre_completo }}</div>
                                        <small class="text-muted">CI: {{ $establecimiento->representante->ci }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $establecimiento->tipoEstablecimiento }}</span>
                                </td>
                                <td>
                                    <a href="mailto:{{ $establecimiento->representante->email }}" class="text-decoration-none">
                                        {{ $establecimiento->representante->email }}
                                    </a>
                                </td>
                                <td>
                                    @if($establecimiento->representante->telefono)
                                        <a href="tel:{{ $establecimiento->representante->telefono }}" class="text-decoration-none">
                                            {{ $establecimiento->representante->telefono }}
                                        </a>
                                    @else
                                        <span class="text-muted">No registrado</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $establecimiento->total_ventas }}</span>
                                </td>
                                <td>
                                    <strong>Bs. {{ number_format($establecimiento->monto_total_ventas, 2) }}</strong>
                                </td>
                                <td>
                                    @if($establecimiento->ultima_venta)
                                        {{ \Carbon\Carbon::parse($establecimiento->ultima_venta)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">Sin ventas</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Estado del Establecimiento --}}
                                    @if($establecimiento->estado == 1)
                                        <span class="badge bg-success">
                                            <i class="fas fa-building me-1"></i>Establecimiento Activo
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-building me-1"></i>Establecimiento Inactivo
                                        </span>
                                    @endif
                                    
                                    {{-- Estado del Representante --}}
                                    @if($establecimiento->representante->estado == 1)
                                        <br><small class="badge bg-info mt-1">
                                            <i class="fas fa-user me-1"></i>Representante Activo
                                        </small>
                                    @else
                                        <br><small class="badge bg-warning mt-1">
                                            <i class="fas fa-user me-1"></i>Representante Inactivo
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('clienteEstablecimiento.show', $establecimiento->idEstablecimiento) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('clienteEstablecimiento.edit', $establecimiento->idEstablecimiento) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('clienteEstablecimiento.destroy', $establecimiento->idEstablecimiento) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Estás seguro de que deseas eliminar el establecimiento {{ addslashes($establecimiento->razonSocial) }}? Se marcará como inactivo.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-building fa-3x mb-3"></i>
                                        <p>No hay clientes establecimientos registrados</p>
                                        <a href="{{ route('clienteEstablecimiento.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>
                                            Crear Primer Establecimiento
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 14px;
    font-weight: bold;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#establecimientosTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        order: [[1, 'asc']],
        pageLength: 25,
        responsive: true,
        columnDefs: [
            {
                targets: [10], // Columna de acciones
                orderable: false,
                searchable: false
            }
        ]
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
