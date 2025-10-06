@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Mis Diseños</h3>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('disenos.mis-disenos') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="estado" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por comentario..." value="{{ request('buscar') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de diseños -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Imagen</th>
                                    <th>Comentario</th>
                                    <th>Fecha de Creación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($disenos as $diseno)
                                    <tr>
                                        <td>{{ $diseno->idDiseno }}</td>
                                        <td style="width: 120px;">
                                            @if($diseno->archivo)
                                                @php
                                                    $extension = pathinfo($diseno->archivo, PATHINFO_EXTENSION);
                                                    $esImagen = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                @endphp
                                                @if($esImagen)
                                                    <img src="{{ asset('storage/' . $diseno->archivo) }}" alt="Diseño" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                                @else
                                                    <div class="text-center py-2">
                                                        <i class="fas fa-file-alt fa-3x text-muted"></i>
                                                        <p class="small mb-0">{{ strtoupper($extension) }}</p>
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-muted">Sin archivo</span>
                                            @endif
                                        </td>
                                        <td>{{ $diseno->comentario ?: 'Sin comentario' }}</td>
                                        <td>{{ $diseno->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @php
                                                $badgeClass = 'secondary';
                                                if ($diseno->estadoDiseño == 'terminado') {
                                                    $badgeClass = 'success';
                                                } elseif ($diseno->estadoDiseño == 'en proceso') {
                                                    $badgeClass = 'warning';
                                                } elseif ($diseno->estadoDiseño == 'no realizado') {
                                                    $badgeClass = 'danger';
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">
                                                {{ ucfirst($diseno->estadoDiseño) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('disenos.show', $diseno->idDiseno) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('disenos.edit', $diseno->idDiseno) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
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

                    <!-- Paginación -->
                    @if($disenos->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $disenos->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales -->
<style>
    .table th {
        background-color: #f8f9fa;
    }
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
</style>
@endsection
