@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Clientes Naturales</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Clientes Naturales</li>
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
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Clientes</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $estadisticas['total_clientes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x"></i>
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
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Clientes Activos</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $estadisticas['clientes_activos'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x"></i>
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

    {{-- Tabla de Clientes --}}
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    Lista de Clientes Naturales
                </div>
                <a href="{{ route('clienteNatural.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Nuevo Cliente Natural
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="clientesTable">
                    <thead class="table-dark">
                        <tr>
                            <th>CI</th>
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>NIT</th>
                            <th>Ventas</th>
                            <th>Monto Total</th>
                            <th>Última Venta</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $cliente->user->ci }}</code>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ strtoupper(substr($cliente->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $cliente->user->nombre_completo }}</div>
                                            <small class="text-muted">ID: #{{ $cliente->idCliente }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $cliente->user->email }}" class="text-decoration-none">
                                        {{ $cliente->user->email }}
                                    </a>
                                </td>
                                <td>
                                    @if($cliente->user->telefono)
                                        <a href="tel:{{ $cliente->user->telefono }}" class="text-decoration-none">
                                            {{ $cliente->user->telefono }}
                                        </a>
                                    @else
                                        <span class="text-muted">No registrado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cliente->nit)
                                        <code class="bg-light px-2 py-1 rounded">{{ $cliente->nit }}</code>
                                    @else
                                        <span class="text-muted">No registrado</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $cliente->total_ventas }}</span>
                                </td>
                                <td>
                                    <strong>Bs. {{ number_format($cliente->monto_total_ventas, 2) }}</strong>
                                </td>
                                <td>
                                    @if($cliente->ultima_venta)
                                        {{ \Carbon\Carbon::parse($cliente->ultima_venta)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">Sin ventas</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cliente->estado == 1 && $cliente->user->estado == 1)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('clienteNatural.show', $cliente->idCliente) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('clienteNatural.edit', $cliente->idCliente) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('clienteNatural.destroy', $cliente->idCliente) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Estás seguro de que deseas eliminar el cliente {{ addslashes($cliente->user->nombre_completo) }}? Se marcará como inactivo.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p>No hay clientes naturales registrados</p>
                                        <a href="{{ route('clienteNatural.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>
                                            Crear Primer Cliente
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
    $('#clientesTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        order: [[1, 'asc']],
        pageLength: 25,
        responsive: true,
        columnDefs: [
            {
                targets: [9], // Columna de acciones
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
