@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Categorías</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Categorías</li>
    </ol>

    {{-- Mensajes de éxito o error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Mensaje específico para eliminación exitosa --}}
    @if(session('successdelete'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
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

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    Lista de Categorías
                </div>
                <a href="{{ route('categorias.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>
                    Nueva Categoría
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($categorias->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="categoriasTable">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Productos</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categorias as $categoria)
                                <tr>
                                    <td>{{ $categoria->idCategoria }}</td>
                                    <td>
                                        <strong>{{ $categoria->nombreCategoria }}</strong>
                                    </td>
                                    <td>
                                        {{ $categoria->descripcion ? Str::limit($categoria->descripcion, 50) : 'Sin descripción' }}
                                    </td>
                                    <td>
                                        @if($categoria->estado == 1)
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
                                        <span class="badge bg-info">
                                            {{ $categoria->productos->count() }} productos
                                        </span>
                                    </td>
                                    <td>
                                        {{ $categoria->created_at ? $categoria->created_at->format('d/m/Y H:i') : 'No disponible' }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('categorias.show', $categoria->idCategoria) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('categorias.edit', $categoria->idCategoria) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Botón Eliminar con Formulario DELETE -->
                                            <form action="{{ route('categorias.destroy', $categoria->idCategoria) }}" 
                                                  method="POST" 
                                                  style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-sm"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de que deseas eliminar la categoría {{ addslashes($categoria->nombreCategoria) }}? Se marcará como inactiva.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay categorías registradas</h5>
                    <p class="text-muted">Comienza agregando tu primera categoría</p>
                    <a href="{{ route('categorias.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Crear Primera Categoría
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Ya no necesitamos modal, usamos confirmación directa en el formulario --}}
@endsection

@push('scripts')
<script>
    // Inicializar DataTable
    $(document).ready(function() {
        $('#categoriasTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "responsive": true,
            "order": [[ 1, "asc" ]]
        });
    });

    // Ya no necesitamos JavaScript para eliminación, se maneja directamente en el formulario
</script>
@endpush