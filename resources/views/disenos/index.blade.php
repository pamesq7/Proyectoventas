@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestión de Diseños</h3>
                    <div class="card-tools">
                        <a href="{{ route('disenos.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Diseño
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('disenos.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="estadoDiseno" class="form-control">
                                    <option value="">Todos los estados</option>
                                    @foreach($estadosDiseno as $estado)
                                        <option value="{{ $estado }}" {{ request('estadoDiseno') == $estado ? 'selected' : '' }}>
                                            {{ ucfirst($estado) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="estado" class="form-control">
                                    <option value="">Activo/Inactivo</option>
                                    <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="buscar" class="form-control" placeholder="Buscar por comentario..." value="{{ request('buscar') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary">Filtrar</button>
                                <a href="{{ route('disenos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de diseños -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Imagen</th>
                                    <th>Comentario</th>
                                    <th>Estado Diseño</th>
                                    <th>Empleado</th>
                                    <th>ID Diseñador</th>
                                    <th>Estado</th>
                                    <th>Fecha Creación</th>
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
                                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                                @endphp
                                                
                                                @if($isImage)
                                                    <div class="text-center">
                                                        <img src="{{ Storage::url($diseno->archivo) }}" 
                                                             alt="Diseño" 
                                                             class="img-thumbnail" 
                                                             style="max-width: 80px; max-height: 80px; cursor: pointer;"
                                                             onclick="window.open('{{ Storage::url($diseno->archivo) }}', '_blank')">
                                                        <br>
                                                        <small class="text-muted">{{ strtoupper($extension) }}</small>
                                                    </div>
                                                @else
                                                    <div class="text-center">
                                                        <i class="fas fa-file fa-2x text-muted"></i>
                                                        <br>
                                                        <small class="text-muted">{{ strtoupper($extension) }}</small>
                                                        <br>
                                                        <a href="{{ Storage::url($diseno->archivo) }}" target="_blank" class="btn btn-xs btn-outline-info">
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
                                            <span class="badge badge-{{ $diseno->estadoDiseño == 'terminado' ? 'success' : 'warning' }}">
                                                {{ ucfirst($diseno->estadoDiseño) }}
                                            </span>
                                        </td>
                                        <td>{{ $diseno->empleado->nombre ?? 'N/A' }}</td>
                                        <td>{{ $diseno->idDiseñador ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $diseno->estado ? 'success' : 'danger' }}">
                                                {{ $diseno->estado ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td>{{ $diseno->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('disenos.show', $diseno) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('disenos.edit', $diseno) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('disenos.destroy', $diseno) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este diseño?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No se encontraron diseños</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $disenos->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
