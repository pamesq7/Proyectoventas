@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">✏️ Editar Opción</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('opciones.index') }}">Opciones</a></li>
        <li class="breadcrumb-item active">Editar: {{ $opcion->nombre }}</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Editar Opción: <strong>{{ $opcion->nombre }}</strong>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Información del sistema -->
                    <div class="alert alert-light border mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    <strong>Creado:</strong> {{ $opcion->created_at ? $opcion->created_at->format('d/m/Y H:i') : 'No disponible' }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-edit me-1"></i>
                                    <strong>Actualizado:</strong> {{ $opcion->updated_at ? $opcion->updated_at->format('d/m/Y H:i') : 'No disponible' }}
                                </small>
                            </div>
                        </div>
                        @if($opcion->caracteristicas->count() > 0)
                        <div class="mt-2">
                            <small class="text-info">
                                <i class="fas fa-info-circle me-1"></i>
                                Esta opción tiene <strong>{{ $opcion->caracteristicas->count() }}</strong> características asociadas
                            </small>
                        </div>
                        @endif
                    </div>

                    <form action="{{ route('opciones.update', ['opcion' => $opcion->idOpcion]) }}" method="POST" id="editOpcionForm">
                        @csrf
                        @method('PUT')
                        
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
                                           value="{{ old('nombre', $opcion->nombre) }}" 
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
                                        <option value="1" {{ old('estado', $opcion->estado) == 1 ? 'selected' : '' }}>
                                            <i class="fas fa-check"></i> Activo
                                        </option>
                                        <option value="0" {{ old('estado', $opcion->estado) == 0 ? 'selected' : '' }}>
                                            <i class="fas fa-pause"></i> Inactivo
                                        </option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($opcion->caracteristicas->count() > 0 && $opcion->estado == 1)
                                    <div class="form-text text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Al desactivar esta opción, también se desactivarán sus características
                                    </div>
                                    @endif
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
                                      required>{{ old('descripcion', $opcion->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Explica brevemente qué personalización agrupa esta opción
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('opciones.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Volver a la Lista
                                </a>
                                <a href="{{ route('opciones.show', ['opcion' => $opcion->idOpcion]) }}" class="btn btn-info">
                                    <i class="fas fa-eye me-1"></i>
                                    Ver Detalles
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Características asociadas -->
            @if($opcion->caracteristicas->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Características Asociadas ({{ $opcion->caracteristicas->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($opcion->caracteristicas as $caracteristica)
                        <div class="col-md-4 mb-2">
                            <div class="d-flex align-items-center">
                                <span class="badge {{ $caracteristica->estado ? 'bg-success' : 'bg-secondary' }} me-2">
                                    {{ $caracteristica->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <span>{{ $caracteristica->nombre }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Para gestionar estas características, ve al módulo de Características
                        </small>
                    </div>
                </div>
            </div>
            @endif

            <!-- Advertencias importantes -->
            @if($opcion->caracteristicas->count() > 0)
            <div class="alert alert-warning mt-4">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Importante</h6>
                <ul class="mb-0">
                    <li>Esta opción tiene características asociadas. Los cambios pueden afectar productos existentes.</li>
                    <li>Si desactivas esta opción, también se desactivarán automáticamente todas sus características.</li>
                    <li>Cambiar el nombre puede afectar la organización de productos y variantes.</li>
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
