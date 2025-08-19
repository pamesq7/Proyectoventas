@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Producto</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
        <li class="breadcrumb-item active">{{ $producto->nombre }}</li>
    </ol>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-cogs me-2"></i>
                Gestión Avanzada: {{ $producto->nombre }}
            </h5>
        </div>
        <div class="card-body">
            <!-- Pestañas de Navegación -->
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="datos-basicos-tab" data-bs-toggle="tab" data-bs-target="#datos-basicos" type="button" role="tab">
                        <i class="fas fa-info-circle me-1"></i>Datos Básicos
                    </button>
                </li>
                
            </ul>

            <!-- Contenido de las Pestañas -->
            <div class="tab-content" id="productTabsContent">
                <!-- PESTAÑA 1: DATOS BÁSICOS -->
                <div class="tab-pane fade show active" id="datos-basicos" role="tabpanel">
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <form action="{{ route('productos.update', $producto->idProducto) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                {{-- Mostrar errores de validación --}}
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <h6><i class="fas fa-exclamation-triangle me-1"></i>Errores de validación:</h6>
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="SKU" class="form-label">SKU *</label>
                                            <input type="text" class="form-control" id="SKU" name="SKU" value="{{ old('SKU', $producto->SKU) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="idCategoria" class="form-label">Categoría *</label>
                                            <select class="form-select" id="idCategoria" name="idCategoria" required>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->idCategoria }}" {{ old('idCategoria', $producto->idCategoria) == $categoria->idCategoria ? 'selected' : '' }}>
                                                        {{ $categoria->nombreCategoria }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="idVariante" class="form-label">Variante</label>
                                            <select class="form-select @error('idVariante') is-invalid @enderror" 
                                                    id="idVariante" name="idVariante">
                                                <option value="">Sin variante</option>
                                                @foreach($variantes as $variante)
                                                    <option value="{{ $variante->idVariante }}" 
                                                            {{ old('idVariante', $producto->idVariante) == $variante->idVariante ? 'selected' : '' }}>
                                                        {{ $variante->nombre }}
                                                        @if($variante->varianteCaracteristicas->count() > 0)
                                                            ({{ $variante->varianteCaracteristicas->count() }} características)
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('idVariante')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <select class="form-select @error('estado') is-invalid @enderror" 
                                                    id="estado" name="estado" required>
                                                <option value="1" {{ old('estado', $producto->estado ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
                                                <option value="0" {{ old('estado', $producto->estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="precioVenta" class="form-label">Precio de Venta *</label>
                                            <input type="number" class="form-control" id="precioVenta" name="precioVenta" value="{{ old('precioVenta', $producto->precioVenta) }}" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="precioProduccion" class="form-label">Precio de Producción</label>
                                            <input type="number" class="form-control" id="precioProduccion" name="precioProduccion" value="{{ old('precioProduccion', $producto->precioProduccion) }}" step="0.01">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="foto" class="form-label">Foto Principal</label>
                                    @if($producto->foto && file_exists(public_path('storage/' . $producto->foto)))
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $producto->foto) }}" alt="{{ $producto->nombre }}" class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    @elseif($producto->foto)
                                        <div class="mb-2">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Imagen no encontrada: {{ $producto->foto }}
                                            </div>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                </div>

                                <!-- Galería de Diseños -->
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
                                        <div id="noDisenosMessage" class="text-center text-muted py-3" style="display: none;">
                                            <i class="fas fa-images fa-2x mb-2"></i>
                                            <p class="mb-0">No hay diseños seleccionados</p>
                                            <small>Haz clic en "Agregar Diseños" para vincular diseños terminados</small>
                                        </div>
                                        <div id="disenosGrid" class="row g-2"></div>
                                    </div>
                                    
                                    <!-- Input hidden para enviar los IDs de diseños seleccionados -->
                                    <input type="hidden" name="disenos_vinculados" id="disenosVinculados" value="">
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Guardar Cambios
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Información del Sistema</h6>
                                    <p><strong>ID:</strong> #{{ $producto->idProducto }}</p>
                                    <p><strong>Creado:</strong> {{ $producto->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Actualizado:</strong> {{ $producto->updated_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Estado:</strong> 
                                        <span class="badge {{ $producto->estado ? 'bg-success' : 'bg-danger' }}">
                                            {{ $producto->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Variante -->
<div class="modal fade" id="modalNuevaVariante" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Variante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaVariante">
                    <input type="hidden" name="producto_id" value="{{ $producto->idProducto }}">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="variante_nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="variante_nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="variante_descripcion" class="form-label">Descripción *</label>
                                <input type="text" class="form-control" id="variante_descripcion" name="descripcion" required>
                            </div>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarVariante()">Guardar Variante</button>
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

// Cargar diseño existente del producto al inicializar
document.addEventListener('DOMContentLoaded', function() {
    const idDisenoExistente = {{ $producto->idDiseno ?? 'null' }};
    
    if (idDisenoExistente) {
        // Buscar el diseño en la API y agregarlo
        fetch('/api/disenos/terminados')
            .then(response => response.json())
            .then(data => {
                const disenoExistente = data.find(d => d.idDiseno === idDisenoExistente);
                if (disenoExistente) {
                    disenosSeleccionados.push({
                        id: disenoExistente.idDiseno,
                        comentario: disenoExistente.comentario,
                        archivo: disenoExistente.archivo,
                        empleado: disenoExistente.empleado
                    });
                    actualizarGaleriaDisenosSeleccionados();
                }
            })
            .catch(error => console.error('Error cargando diseño existente:', error));
    } else {
        // Mostrar mensaje de no diseños si no hay ninguno
        document.getElementById('noDisenosMessage').style.display = 'block';
    }
});

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

// Cargar diseños ya vinculados al producto
document.addEventListener('DOMContentLoaded', function() {
    // Aquí cargarías los diseños ya vinculados al producto desde el servidor
    // Por ahora lo dejamos vacío, pero se puede implementar más tarde
});

// Habilitar/deshabilitar input de precio según checkbox
document.querySelectorAll('.caracteristica-check').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const precioInput = this.closest('.form-check').querySelector('.precio-adicional');
        precioInput.disabled = !this.checked;
        if (!this.checked) {
            precioInput.value = '0';
        }
    });
});

// Guardar nueva variante
function guardarVariante() {
    const form = document.getElementById('formNuevaVariante');
    const formData = new FormData(form);
    
    // Convertir a objeto para envío JSON
    const data = {
        producto_id: formData.get('producto_id'),
        nombre: formData.get('nombre'),
        descripcion: formData.get('descripcion'),
        caracteristicas: []
    };
    
    // Recopilar características seleccionadas
    document.querySelectorAll('.caracteristica-check:checked').forEach(function(checkbox) {
        const caracteristicaId = checkbox.value;
        const precioInput = checkbox.closest('.form-check').querySelector('.precio-adicional');
        
        data.caracteristicas.push({
            id: caracteristicaId,
            precio_adicional: parseFloat(precioInput.value) || 0
        });
    });
    
    if (data.caracteristicas.length === 0) {
        alert('Debe seleccionar al menos una característica');
        return;
    }
    
    // Enviar datos
    fetch('{{ route("productos.storeVariante") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar la variante');
    });
}


</script>
@endpush