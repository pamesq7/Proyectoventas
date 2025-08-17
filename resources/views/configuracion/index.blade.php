@extends('layouts.app')

@section('title', 'Configuración de Productos')

@section('content')
<div class="container-fluid">
    <!-- Header Principal -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs me-2"></i>Configuración de Productos
            </h1>
            <p class="text-muted mb-0">Visualiza categorías, opciones y gestiona características</p>
        </div>
        <div>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#nuevaCaracteristicaModal">
                <i class="fas fa-plus me-1"></i>Nueva Característica
            </button>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Categorías</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $categorias->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Opciones</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $opciones->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Características</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $caracteristicas->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Productos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProductos }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs de Navegación -->
    <ul class="nav nav-tabs mb-4" id="configTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="categorias-tab" data-bs-toggle="tab" data-bs-target="#categorias" type="button" role="tab">
                <i class="fas fa-folder me-2"></i>Categorías
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="opciones-tab" data-bs-toggle="tab" data-bs-target="#opciones" type="button" role="tab">
                <i class="fas fa-cogs me-2"></i>Opciones
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="caracteristicas-tab" data-bs-toggle="tab" data-bs-target="#caracteristicas" type="button" role="tab">
                <i class="fas fa-tags me-2"></i>Características
            </button>
        </li>
    </ul>

    <!-- Contenido de Tabs -->
    <div class="tab-content" id="configTabsContent">
        
        <!-- Tab Categorías -->
        <div class="tab-pane fade show active" id="categorias" role="tabpanel">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-folder me-2"></i>Categorías (Solo Visualización)
                    </h6>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm" id="buscarCategoria" placeholder="Buscar categoría...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="categoriasContainer">
                        @foreach($categorias as $categoria)
                        <div class="col-lg-4 col-md-6 mb-3 categoria-card">
                            <div class="card h-100 categoria-item">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-folder me-2 text-primary"></i>
                                            {{ $categoria->nombreCategoria }}
                                        </h6>
                                    </div>
                                    
                                    <p class="card-text text-muted small mb-3">
                                        {{ $categoria->descripcion ?: 'Sin descripción' }}
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $categoria->created_at ? $categoria->created_at->format('d/m/Y') : 'Sin fecha' }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-box me-1"></i>
                                            {{ $categoria->productos_count ?? 0 }} productos
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Opciones -->
        <div class="tab-pane fade" id="opciones" role="tabpanel">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-cogs me-2"></i>Opciones (Solo Visualización)
                    </h6>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm" id="buscarOpcion" placeholder="Buscar opción...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="opcionesContainer">
                        @foreach($opciones as $opcion)
                        <div class="col-lg-6 col-md-12 mb-3 opcion-card">
                            <div class="card h-100 opcion-item">
                                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-cog me-2"></i>{{ $opcion->nombre }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text text-muted small mb-3">
                                        {{ $opcion->descripcion ?: 'Sin descripción' }}
                                    </p>
                                    
                                    <!-- Características asociadas -->
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-2">
                                            <i class="fas fa-tags me-1"></i>Características ({{ $opcion->caracteristicas->count() }})
                                        </h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            @forelse($opcion->caracteristicas->take(6) as $caracteristica)
                                                <span class="badge bg-primary badge-sm">
                                                    {{ $caracteristica->nombre }}
                                                </span>
                                            @empty
                                                <small class="text-muted">Sin características</small>
                                            @endforelse
                                            
                                            @if($opcion->caracteristicas->count() > 6)
                                                <span class="badge bg-info">+{{ $opcion->caracteristicas->count() - 6 }} más</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $opcion->created_at ? $opcion->created_at->format('d/m/Y') : 'Sin fecha' }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-tags me-1"></i>
                                            {{ $opcion->caracteristicas->count() }} características
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Características -->
        <div class="tab-pane fade" id="caracteristicas" role="tabpanel">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-tags me-2"></i>Gestión Simple de Características
                    </h6>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="filtroOpcionCaracteristica" style="width: auto;">
                            <option value="">Todas las opciones</option>
                            @foreach($opciones as $opcion)
                            <option value="{{ $opcion->idOpcion }}">{{ $opcion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="card-body">
                    @foreach($opciones as $opcion)
                    @if($opcion->caracteristicas->count() > 0)
                    <div class="mb-4 opcion-grupo" data-opcion-id="{{ $opcion->idOpcion }}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-success mb-0">
                                <i class="fas fa-cog me-2"></i>{{ $opcion->nombre }}
                            </h6>
                            <button type="button" class="btn btn-sm btn-outline-info" 
                                    data-bs-toggle="modal" data-bs-target="#nuevaCaracteristicaModal"
                                    data-opcion-id="{{ $opcion->idOpcion }}"
                                    onclick="preseleccionarOpcion(this.dataset.opcionId)">
                                <i class="fas fa-plus me-1"></i>Agregar
                            </button>
                        </div>
                        
                        <!-- Características como tags simples -->
                        <div class="caracteristicas-tags" data-opcion-id="{{ $opcion->idOpcion }}">
                            @foreach($opcion->caracteristicas as $caracteristica)
                            <div class="caracteristica-tag d-inline-block me-2 mb-2" 
                                 data-caracteristica-id="{{ $caracteristica->idCaracteristica }}">
                                <span class="badge bg-primary fs-6 py-2 px-3 position-relative">
                                    {{ $caracteristica->nombre }}
                                    @if($caracteristica->descripcion)
                                    <small class="d-block text-white-50" style="font-size: 0.7em;">{{ $caracteristica->descripcion }}</small>
                                    @endif
                                    
                                    <!-- Botón de eliminar en la esquina -->
                                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 me-1 mt-1" 
                                            style="font-size: 0.6em; opacity: 0.7;"
                                            data-caracteristica-id="{{ $caracteristica->idCaracteristica }}"
                                            onclick="eliminarCaracteristicaSimple(this.dataset.caracteristicaId)"
                                            title="Eliminar característica"></button>
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endforeach
                    
                    @if($caracteristicas->count() == 0)
                    <div class="text-center py-5">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay características registradas</h5>
                        <p class="text-muted">Agrega la primera característica usando el botón "Nueva Característica"</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// SISTEMA ULTRA SIMPLIFICADO - SOLO CREAR Y ELIMINAR CARACTERÍSTICAS

// Función para eliminar característica simple
function eliminarCaracteristicaSimple(id) {
    if (confirm('¿Estás seguro de eliminar esta característica?')) {
        $.ajax({
            url: `/caracteristicas/${id}`,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                console.log('✅ Característica eliminada exitosamente');
                
                // Actualizar visualmente
                $(`.caracteristica-tag[data-caracteristica-id="${id}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
                
                if (typeof showNotification !== 'undefined') {
                    showNotification('success', 'Característica eliminada correctamente');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error al eliminar característica:', xhr);
                
                let errorMessage = 'No se pudo eliminar la característica';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                if (typeof showNotification !== 'undefined') {
                    showNotification('error', errorMessage);
                } else {
                    alert('Error: ' + errorMessage);
                }
            }
        });
    }
}

// Función para preseleccionar opción en el modal
function preseleccionarOpcion(opcionId) {
    console.log('🎯 Preseleccionando opción:', opcionId);
    
    // Esperar a que el modal se abra completamente
    $('#nuevaCaracteristicaModal').on('shown.bs.modal', function() {
        $('#opcionCaracteristica').val(opcionId);
        $(this).off('shown.bs.modal'); // Remover el evento después de usarlo
    });
}

// Configuración al cargar la página
$(document).ready(function() {
    console.log('✅ Sistema de configuración con CRUD de características listo');
    
    // Filtros para características
    $('#filtroOpcionCaracteristica').on('change', function() {
        const opcionId = this.value;
        $('.opcion-grupo').each(function() {
            const grupoOpcionId = $(this).data('opcion-id').toString();
            if (opcionId === '' || grupoOpcionId === opcionId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Manejo del formulario de nueva característica
    $('#formNuevaCaracteristica').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Agregar token CSRF al FormData
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#nuevaCaracteristicaModal').modal('hide');
                
                if (typeof showNotification !== 'undefined') {
                    showNotification('success', 'Característica creada correctamente');
                }
                
                // Recargar página para mostrar nueva característica
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                let errorMessage = 'Error al crear la característica';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                }
                
                if (typeof showNotification !== 'undefined') {
                    showNotification('error', errorMessage);
                } else {
                    alert(errorMessage);
                }
            }
        });
    });
    
    // Limpiar formularios al cerrar modales
    $('#nuevaCaracteristicaModal').on('hidden.bs.modal', function() {
        $('#formNuevaCaracteristica')[0].reset();
        $('#contadorNombreCaracteristica').text('0');
        $('#contadorDescripcionCaracteristica').text('0');
    });
    
    // Contadores de caracteres para nueva característica
    $('#nombreCaracteristica').on('input', function() {
        $('#contadorNombreCaracteristica').text($(this).val().length);
    });
    
    $('#descripcionCaracteristica').on('input', function() {
        $('#contadorDescripcionCaracteristica').text($(this).val().length);
    });
});

console.log('✅ Sistema completo de gestión de características cargado');
</script>
@endpush

<!-- Modales -->
@include('configuracion.modals.caracteristica')

@endsection
