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

                            <!-- Galería de Diseños -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Galería de Diseños</label>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Selecciona diseños terminados para vincular con este producto</small>
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#disenosModal">
                                            <i class="fas fa-plus"></i> Agregar Diseños
                                        </button>
                                    </div>
                                    
                                    <!-- Diseños seleccionados -->
                                    <div id="disenosSeleccionados" class="border rounded p-3 bg-light">
                                        <div id="noDisenosMessage" class="text-center text-muted py-3">
                                            <i class="fas fa-images fa-2x mb-2"></i>
                                            <p class="mb-0">No hay diseños seleccionados</p>
                                            <small>Haz clic en "Agregar Diseños" para vincular diseños terminados</small>
                                        </div>
                                        <div id="disenosGrid" class="row g-2" style="display: none;"></div>
                                    </div>
                                    
                                    <!-- Input hidden para enviar los IDs de diseños seleccionados -->
                                    <input type="hidden" name="disenos_vinculados" id="disenosVinculados" value="">
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

<!-- Modal para seleccionar diseños -->
<div class="modal fade" id="disenosModal" tabindex="-1" aria-labelledby="disenosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disenosModalLabel">
                    <i class="fas fa-palette me-2"></i>Seleccionar Diseños Terminados
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="buscarDisenos" class="form-control" placeholder="Buscar por comentario...">
                    </div>
                    <div class="col-md-6">
                        <div class="text-end">
                            <small class="text-muted">Selecciona los diseños que quieres vincular</small>
                        </div>
                    </div>
                </div>
                
                <div id="disenosLoader" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando diseños terminados...</p>
                </div>
                
                <div id="disenosTable" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAllDisenos" class="form-check-input">
                                    </th>
                                    <th width="100">Imagen</th>
                                    <th>Comentario</th>
                                    <th>Empleado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody id="disenosTableBody">
                                <!-- Los diseños se cargarán aquí via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div id="noDisenosFound" class="text-center py-4" style="display: none;">
                    <i class="fas fa-search fa-2x text-muted mb-2"></i>
                    <p class="text-muted">No se encontraron diseños terminados</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmarSeleccion">
                    <i class="fas fa-check me-1"></i>Confirmar Selección (<span id="contadorSeleccionados">0</span>)
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Variables globales para diseños
let disenosSeleccionados = [];
let disenosDisponibles = [];

// Cargar diseños terminados cuando se abre el modal
document.getElementById('disenosModal').addEventListener('show.bs.modal', function() {
    cargarDisenosTerminados();
});

// Función para cargar diseños terminados
function cargarDisenosTerminados() {
    const loader = document.getElementById('disenosLoader');
    const table = document.getElementById('disenosTable');
    const noFound = document.getElementById('noDisenosFound');
    
    loader.style.display = 'block';
    table.style.display = 'none';
    noFound.style.display = 'none';
    
    fetch('/api/disenos/terminados')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            disenosDisponibles = data;
            mostrarDisenosEnTabla(data);
            loader.style.display = 'none';
            
            if (data.length > 0) {
                table.style.display = 'block';
            } else {
                noFound.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error al cargar diseños:', error);
            loader.style.display = 'none';
            noFound.style.display = 'block';
            
            // Mostrar mensaje de error más específico
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger mt-2';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Error: ${error.message}`;
            noFound.appendChild(errorDiv);
        });
}

// Función para mostrar diseños en la tabla
function mostrarDisenosEnTabla(disenos) {
    const tbody = document.getElementById('disenosTableBody');
    tbody.innerHTML = '';
    
    disenos.forEach(diseno => {
        const isSelected = disenosSeleccionados.some(d => d.id === diseno.idDiseno);
        const imagenSrc = diseno.archivo ? `/storage/${diseno.archivo}` : '/images/no-image.png';
        
        const row = `
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input diseno-checkbox" 
                           value="${diseno.idDiseno}" ${isSelected ? 'checked' : ''}>
                </td>
                <td>
                    <img src="${imagenSrc}" alt="Diseño" class="img-thumbnail" 
                         style="width: 60px; height: 60px; object-fit: cover;"
                         onerror="this.src='/images/no-image.png'">
                </td>
                <td>${diseno.comentario || 'Sin comentario'}</td>
                <td>${diseno.empleado ? diseno.empleado.nombre : 'N/A'}</td>
                <td>${new Date(diseno.created_at).toLocaleDateString()}</td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
    
    // Agregar event listeners a los checkboxes
    document.querySelectorAll('.diseno-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', actualizarSeleccion);
    });
    
    actualizarContador();
}

// Función para actualizar selección
function actualizarSeleccion() {
    const checkboxes = document.querySelectorAll('.diseno-checkbox:checked');
    disenosSeleccionados = [];
    
    checkboxes.forEach(checkbox => {
        const disenoId = parseInt(checkbox.value);
        const diseno = disenosDisponibles.find(d => d.idDiseno === disenoId);
        if (diseno) {
            disenosSeleccionados.push({
                id: diseno.idDiseno,
                comentario: diseno.comentario,
                archivo: diseno.archivo,
                empleado: diseno.empleado
            });
        }
    });
    
    actualizarContador();
}

// Función para actualizar contador
function actualizarContador() {
    document.getElementById('contadorSeleccionados').textContent = disenosSeleccionados.length;
}

// Seleccionar/deseleccionar todos
document.getElementById('selectAllDisenos').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.diseno-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    actualizarSeleccion();
});

// Confirmar selección
document.getElementById('confirmarSeleccion').addEventListener('click', function() {
    actualizarGaleriaDisenosSeleccionados();
    const modalElement = document.getElementById('disenosModal');
    const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
    modal.hide();
    
    // Limpieza inmediata y forzada
    limpiarModal();
});

// Función para limpiar completamente el modal
function limpiarModal() {
    // Remover backdrop inmediatamente
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => backdrop.remove());
    
    // Limpiar clases y estilos del body
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');
    document.body.style.removeProperty('margin-right');
    
    // Restaurar scroll completo
    document.documentElement.style.removeProperty('overflow');
    document.documentElement.style.removeProperty('padding-right');
    
    // Forzar reflow para asegurar que los cambios se apliquen
    document.body.offsetHeight;
    
    // Limpieza adicional después de un breve delay
    setTimeout(() => {
        const remainingBackdrops = document.querySelectorAll('.modal-backdrop');
        remainingBackdrops.forEach(backdrop => backdrop.remove());
        
        // Asegurar que no queden estilos residuales
        if (document.body.classList.contains('modal-open')) {
            document.body.classList.remove('modal-open');
        }
    }, 100);
}

// Función para actualizar la galería de diseños seleccionados
function actualizarGaleriaDisenosSeleccionados() {
    const noDisenosMessage = document.getElementById('noDisenosMessage');
    const disenosGrid = document.getElementById('disenosGrid');
    const disenosVinculados = document.getElementById('disenosVinculados');
    
    if (disenosSeleccionados.length === 0) {
        noDisenosMessage.style.display = 'block';
        disenosGrid.style.display = 'none';
        disenosVinculados.value = '';
        return;
    }
    
    noDisenosMessage.style.display = 'none';
    disenosGrid.style.display = 'block';
    
    // Actualizar grid visual
    disenosGrid.innerHTML = '';
    disenosSeleccionados.forEach(diseno => {
        const imagenSrc = diseno.archivo ? `/storage/${diseno.archivo}` : '/images/no-image.png';
        const disenoCard = `
            <div class="col-md-3 col-sm-4 col-6">
                <div class="card">
                    <img src="${imagenSrc}" class="card-img-top" style="height: 120px; object-fit: cover;"
                         onerror="this.src='/images/no-image.png'">
                    <div class="card-body p-2">
                        <small class="text-muted">${diseno.comentario || 'Sin comentario'}</small>
                        <button type="button" class="btn btn-sm btn-outline-danger float-end" 
                                onclick="removerDiseno(${diseno.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        disenosGrid.innerHTML += disenoCard;
    });
    
    // Actualizar input hidden con IDs
    const ids = disenosSeleccionados.map(d => d.id).join(',');
    disenosVinculados.value = ids;
}

// Función para remover diseño
function removerDiseno(disenoId) {
    disenosSeleccionados = disenosSeleccionados.filter(d => d.id !== disenoId);
    actualizarGaleriaDisenosSeleccionados();
}

// Búsqueda en tiempo real
document.getElementById('buscarDisenos').addEventListener('input', function() {
    const termino = this.value.toLowerCase();
    const disenosFiltrados = disenosDisponibles.filter(diseno => 
        (diseno.comentario || '').toLowerCase().includes(termino) ||
        (diseno.empleado && diseno.empleado.nombre.toLowerCase().includes(termino))
    );
    mostrarDisenosEnTabla(disenosFiltrados);
});

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