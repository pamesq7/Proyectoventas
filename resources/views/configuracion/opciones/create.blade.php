@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">🛠️ Nueva Opción</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('opciones.index') }}">Opciones</a></li>
        <li class="breadcrumb-item active">Nueva Opción</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Crear Nueva Opción Maestra
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Información explicativa -->
                    <div class="alert alert-info mb-4">
                        <h6><i class="fas fa-lightbulb me-2"></i>¿Qué es una Opción?</h6>
                        <p class="mb-2">Una <strong>Opción</strong> es un grupo principal de personalización que contendrá múltiples características específicas.</p>
                        <p class="mb-0"><strong>Ejemplos:</strong></p>
                        <ul class="mb-0">
                            <li><strong>Cuello</strong> → Contendrá: V, Redondo, Polo, Capucha</li>
                            <li><strong>Manga</strong> → Contendrá: Corta, Larga, 3/4, Sin manga</li>
                            <li><strong>Material</strong> → Contendrá: Algodón, Poliéster, Dri-Fit</li>
                        </ul>
                    </div>

                    <form action="{{ route('opciones.store') }}" method="POST" id="createOpcionForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-tag me-1"></i>
                                        Nombre de la Opción <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nombre') is-invalid @enderror" 
                                           id="nombre" 
                                           name="nombre" 
                                           value="{{ old('nombre') }}" 
                                           placeholder="Ej: Cuello, Manga, Material"
                                           required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Nombre único que identifica el grupo de características
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estado" class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        Estado <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('estado') is-invalid @enderror" 
                                            id="estado" 
                                            name="estado" 
                                            required>
                                        <option value="1" {{ old('estado', 1) == 1 ? 'selected' : '' }}>
                                            <i class="fas fa-check"></i> Activo
                                        </option>
                                        <option value="0" {{ old('estado') == 0 ? 'selected' : '' }}>
                                            <i class="fas fa-pause"></i> Inactivo
                                        </option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left me-1"></i>
                                Descripción <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="3" 
                                      placeholder="Describe qué tipo de características contendrá esta opción"
                                      required>{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Explica brevemente qué personalización agrupa esta opción
                            </div>
                        </div>

                        <!-- Preview de ejemplos -->
                        <div class="card bg-light mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-eye me-2"></i>
                                    Vista Previa
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Nombre:</strong> <span id="preview-nombre" class="text-muted">Ingrese el nombre...</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Estado:</strong> 
                                        <span id="preview-estado" class="badge bg-success">Activo</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <strong>Descripción:</strong> 
                                    <p id="preview-descripcion" class="text-muted mb-0">Ingrese la descripción...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('opciones.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Volver a la Lista
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Crear Opción
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ejemplos de opciones comunes -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Ejemplos de Opciones Comunes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Para Prendas Deportivas:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-caret-right text-primary me-2"></i><strong>Cuello:</strong> V, Redondo, Polo, Capucha</li>
                                <li><i class="fas fa-caret-right text-primary me-2"></i><strong>Manga:</strong> Corta, Larga, 3/4, Sin manga</li>
                                <li><i class="fas fa-caret-right text-primary me-2"></i><strong>Material:</strong> Algodón, Poliéster, Dri-Fit</li>
                                <li><i class="fas fa-caret-right text-primary me-2"></i><strong>Talla:</strong> XS, S, M, L, XL, XXL</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">Para Personalización:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-caret-right text-success me-2"></i><strong>Sublimado:</strong> Completo, Parcial, Solo logo</li>
                                <li><i class="fas fa-caret-right text-success me-2"></i><strong>Color Base:</strong> Blanco, Negro, Azul, Rojo</li>
                                <li><i class="fas fa-caret-right text-success me-2"></i><strong>Acabado:</strong> Mate, Brillante, Texturizado</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Preview en tiempo real
    function updatePreview() {
        const nombre = $('#nombre').val() || 'Ingrese el nombre...';
        const descripcion = $('#descripcion').val() || 'Ingrese la descripción...';
        const estado = $('#estado').val();
        
        $('#preview-nombre').text(nombre);
        $('#preview-descripcion').text(descripcion);
        
        const estadoBadge = $('#preview-estado');
        if (estado == '1') {
            estadoBadge.removeClass('bg-secondary').addClass('bg-success').text('Activo');
        } else {
            estadoBadge.removeClass('bg-success').addClass('bg-secondary').text('Inactivo');
        }
    }
    
    // Actualizar preview en tiempo real
    $('#nombre, #descripcion, #estado').on('input change', updatePreview);
    
    // Validación del formulario
    $('#createOpcionForm').on('submit', function(e) {
        let valid = true;
        
        // Validar nombre
        const nombre = $('#nombre').val().trim();
        if (nombre.length < 2) {
            alert('El nombre debe tener al menos 2 caracteres');
            valid = false;
        }
        
        // Validar descripción
        const descripcion = $('#descripcion').val().trim();
        if (descripcion.length < 10) {
            alert('La descripción debe tener al menos 10 caracteres');
            valid = false;
        }
        
        if (!valid) {
            e.preventDefault();
        }
    });
    
    // Sugerencias automáticas
    const sugerencias = [
        { nombre: 'Cuello', descripcion: 'Tipos de cuello para prendas deportivas' },
        { nombre: 'Manga', descripcion: 'Longitud y estilo de mangas' },
        { nombre: 'Material', descripcion: 'Tipos de tela y material de fabricación' },
        { nombre: 'Sublimado', descripcion: 'Opciones de personalización por sublimación' },
        { nombre: 'Color Base', descripcion: 'Colores base disponibles para la prenda' },
        { nombre: 'Talla', descripcion: 'Tallas disponibles para la prenda' }
    ];
    
    // Autocompletar al escribir
    $('#nombre').on('input', function() {
        const valor = $(this).val().toLowerCase();
        const sugerencia = sugerencias.find(s => s.nombre.toLowerCase().includes(valor));
        
        if (sugerencia && valor.length > 2 && $('#descripcion').val().trim() === '') {
            $('#descripcion').val(sugerencia.descripcion);
            updatePreview();
        }
    });
});
</script>
@endpush
