@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Crear Nuevo Producto</h4>
                            <small class="text-muted">Agrega un nuevo producto al inventario</small>
                        </div>
                        <div>
                            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Mensajes de éxito o error --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
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

                    {{-- Errores de validación --}}
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Por favor corrige los siguientes errores:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Información básica -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="SKU" class="form-label">SKU *</label>
                                            <input type="text" class="form-control @error('SKU') is-invalid @enderror" 
                                                   id="SKU" name="SKU" value="{{ old('SKU') }}" 
                                                   placeholder="Ej: PROD-001" required>
                                            @error('SKU')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre *</label>
                                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                                   id="nombre" name="nombre" value="{{ old('nombre') }}" 
                                                   placeholder="Nombre del producto" required>
                                            @error('nombre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                              id="descripcion" name="descripcion" rows="3" 
                                              placeholder="Descripción del producto">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="idCategoria" class="form-label">Categoría *</label>
                                            <select class="form-select @error('idCategoria') is-invalid @enderror" 
                                                    id="idCategoria" name="idCategoria" required>
                                                <option value="">Seleccionar categoría</option>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->idCategoria }}" 
                                                            {{ old('idCategoria') == $categoria->idCategoria ? 'selected' : '' }}>
                                                        {{ $categoria->nombreCategoria }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('idCategoria')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="idVariante" class="form-label">Variante</label>
                                            <select class="form-select @error('idVariante') is-invalid @enderror" 
                                                    id="idVariante" name="idVariante">
                                                <option value="">Sin variante</option>
                                                @if(isset($variantes))
                                                    @foreach($variantes as $variante)
                                                        <option value="{{ $variante->idVariante }}" 
                                                                {{ old('idVariante') == $variante->idVariante ? 'selected' : '' }}>
                                                            {{ $variante->nombre }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('idVariante')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="estado" class="form-label">Estado *</label>
                                            <select class="form-select @error('estado') is-invalid @enderror" 
                                                    id="estado" name="estado" required>
                                                <option value="1" {{ old('estado', 1) == 1 ? 'selected' : '' }}>Activo</option>
                                                <option value="0" {{ old('estado') == 0 ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                            @error('estado')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="precioVenta" class="form-label">Precio de Venta *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('precioVenta') is-invalid @enderror" 
                                                       id="precioVenta" name="precioVenta" min="0" 
                                                       value="{{ old('precioVenta') }}" placeholder="0" required>
                                            </div>
                                            @error('precioVenta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="precioProduccion" class="form-label">Precio de Producción</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('precioProduccion') is-invalid @enderror" 
                                                       id="precioProduccion" name="precioProduccion" min="0" 
                                                       value="{{ old('precioProduccion') }}" placeholder="0">
                                            </div>
                                            @error('precioProduccion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="cantidad" class="form-label">Cantidad/Stock</label>
                                            <input type="number" class="form-control @error('cantidad') is-invalid @enderror" 
                                                   id="cantidad" name="cantidad" min="0" 
                                                   value="{{ old('cantidad', 0) }}" placeholder="0">
                                            @error('cantidad')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="pedidoMinimo" class="form-label">Pedido Mínimo</label>
                                            <input type="number" class="form-control @error('pedidoMinimo') is-invalid @enderror" 
                                                   id="pedidoMinimo" name="pedidoMinimo" min="1" 
                                                   value="{{ old('pedidoMinimo', 1) }}" placeholder="1">
                                            @error('pedidoMinimo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Imagen del producto -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="foto" class="form-label">Imagen del Producto</label>
                                    <input type="file" class="form-control @error('foto') is-invalid @enderror" 
                                           id="foto" name="foto" accept="image/*">
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB.</small>
                                </div>

                                <!-- Vista previa de imagen -->
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <label class="form-label">Vista Previa</label>
                                    <div class="text-center">
                                        <img id="preview" src="" alt="Preview" class="img-thumbnail" 
                                             style="max-width: 200px; max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i>Cancelar
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Crear Producto
                                        </button>
                                    </div>
                                </div>
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
// Vista previa de imagen
document.getElementById('foto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
});

// Validación básica del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        const precio = document.getElementById('precio').value;
        
        if (parseFloat(precio) < 0) {
            e.preventDefault();
            alert('El precio no puede ser negativo');
            return false;
        }
    });
    
    console.log('Formulario de creación de producto cargado correctamente');
});
</script>
@endpush