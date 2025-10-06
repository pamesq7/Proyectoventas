@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Clientes Naturales (Solo Lectura)</h3>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('clientes.consulta') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="buscar" class="form-control" 
                                           placeholder="Buscar por nombre, apellido o CI..." 
                                           value="{{ request('buscar') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de clientes -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>CI</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clientes as $cliente)
                                    <tr>
                                        <td>{{ $cliente->ci }}</td>
                                        <td>{{ $cliente->nombres }}</td>
                                        <td>{{ $cliente->apellidoPaterno }} {{ $cliente->apellidoMaterno }}</td>
                                        <td>{{ $cliente->telefono ?? 'No especificado' }}</td>
                                        <td>{{ $cliente->user->email ?? 'No tiene usuario' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $cliente->estado ? 'success' : 'danger' }}">
                                                {{ $cliente->estado ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No se encontraron clientes naturales.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($clientes->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $clientes->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
                
                <div class="card-footer text-muted">
                    <div class="row">
                        <div class="col-md-6">
                            Mostrando {{ $clientes->firstItem() ?? 0 }} a {{ $clientes->lastItem() ?? 0 }} de {{ $clientes->total() }} registros
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-info">Modo de solo lectura</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales -->
<style>
    .table th {
        background-color: #f8f9fa;
        white-space: nowrap;
    }
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endsection
