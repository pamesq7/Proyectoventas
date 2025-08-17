@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Categoría</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}">Categorías</a></li>
        <li class="breadcrumb-item active">Editar: {{ $categoria->nombreCategoria }}</li>
    </ol>

    {{-- Mostrar errores de validación --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Por favor, corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Mostrar mensaje de error general --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Editar Categoría: <strong>{{ $categoria->nombreCategoria }}</strong>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('categorias.update', $categoria->idCategoria) }}" method="POST" id="categoriaForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            {{-- Nombre de la categoría --}}
                            <div class="col-md-12 mb-3">
                                <label for="nombreCategoria" class="form-label">
                                    <i class="fas fa-tag me-1"></i>
                                    Nombre de la Categoría <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nombreCategoria') is-invalid @enderror" 
                                       id="nombreCategoria" 
                                       name="nombreCategoria" 
                                       value="{{ old('nombreCategoria', $categoria->nombreCategoria) }}"
                                       placeholder="Ej: Ropa Deportiva, Electrónicos, etc."
                                       maxlength="100"
                                       required>
                                @error('nombreCategoria')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    Máximo 100 caracteres. Debe ser único.
                                </div>
                            </div>

                            {{-- Descripción --}}
                            <div class="col-md-12 mb-3">
                                <label for="descripcion" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>
                                    Descripción
                                </label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                          id="descripcion" 
                                          name="descripcion" 
                                          rows="4"
                                          placeholder="Describe brevemente esta categoría..."
                                          maxlength="500">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    Máximo 500 caracteres. Campo opcional.
                                </div>
                            </div>

                            {{-- Estado --}}
                            <div class="col-md-12 mb-4">
                                <label for="estado" class="form-label">
                                    <i class="fas fa-toggle-on me-1"></i>
                                    Estado <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('estado') is-invalid @enderror" 
                                        id="estado" 
                                        name="estado" 
                                        required>
                                    <option value="">Selecciona el estado</option>
                                    <option value="1" {{ old('estado', $categoria->estado) == '1' ? 'selected' : '' }}>
                                        <i class="fas fa-check"></i> Activo
                                    </option>
                                    <option value="0" {{ old('estado', $categoria->estado) == '0' ? 'selected' : '' }}>
                                        <i class="fas fa-times"></i> Inactivo
                                    </option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    Solo las categorías activas estarán disponibles para asignar a productos.
                                </div>
                            </div>
                        </div>

                        {{-- Información adicional --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Información de la Categoría
                                        </h6>
                                        <p class="card-text mb-1">
                                            <strong>ID:</strong> {{ $categoria->idCategoria }}
                                        </p>
                                        <p class="card-text mb-1">
                                            <strong>Productos asociados:</strong> 
                                            <span class="badge bg-info">{{ $categoria->productos->count() }}</span>
                                        </p>
                                        <p class="card-text mb-1">
                                            <strong>Creada:</strong> {{ $categoria->created_at ? $categoria->created_at->format('d/m/Y H:i') : 'No disponible' }}
                                        </p>
                                        <p class="card-text mb-0">
                                            <strong>Última actualización:</strong> {{ $categoria->updated_at ? $categoria->updated_at->format('d/m/Y H:i') : 'No disponible' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if($categoria->productos->count() > 0)
                                    <div class="card bg-warning">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Advertencia
                                            </h6>
                                            <p class="card-text mb-0">
                                                Esta categoría tiene <strong>{{ $categoria->productos->count() }} productos</strong> asociados. 
                                                Si cambias el estado a "Inactivo", podría afectar la disponibilidad de estos productos.
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Botones de acción --}}
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Volver a la Lista
                                </a>
                                <a href="{{ route('categorias.show', $categoria->idCategoria) }}" class="btn btn-info">
                                    <i class="fas fa-eye me-1"></i>
                                    Ver Detalles
                                </a>
                            </div>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-1"></i>
                                    Restaurar
                                </button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i>
                                    Actualizar Categoría
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Validación en tiempo real del nombre
        $('#nombreCategoria').on('input', function() {
            const maxLength = 100;
            const currentLength = $(this).val().length;
            const remaining = maxLength - currentLength;
            
            if (remaining < 20) {
                $(this).next().next('.form-text').html(`Caracteres restantes: ${remaining}`);
            }
        });

        // Validación en tiempo real de la descripción
        $('#descripcion').on('input', function() {
            const maxLength = 500;
            const currentLength = $(this).val().length;
            const remaining = maxLength - currentLength;
            
            $(this).next().next('.form-text').html(`Caracteres restantes: ${remaining}`);
        });

        // Confirmación antes de restaurar el formulario
        $('button[type="reset"]').on('click', function(e) {
            if (!confirm('¿Estás seguro de que deseas restaurar los valores originales?')) {
                e.preventDefault();
            }
        });

        // Validación antes de enviar
        $('#categoriaForm').on('submit', function(e) {
            const nombre = $('#nombreCategoria').val().trim();
            const estado = $('#estado').val();
            
            if (nombre === '') {
                e.preventDefault();
                alert('El nombre de la categoría es obligatorio.');
                $('#nombreCategoria').focus();
                return false;
            }
            
            if (estado === '') {
                e.preventDefault();
                alert('Debes seleccionar un estado para la categoría.');
                $('#estado').focus();
                return false;
            }
        });
    });
</script>
@endpush