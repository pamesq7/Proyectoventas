<!-- Modal para seleccionar diseños -->
<div class="modal fade" id="disenosModal" tabindex="-1" aria-labelledby="disenosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disenosModalLabel">
                    <i class="fas fa-palette me-2"></i>Seleccionar Diseños
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="buscarDisenos" class="form-control" placeholder="Buscar por nombre o descripción...">
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
                    <p class="mt-2">Cargando diseños disponibles...</p>
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
                                    <th>Nombre</th>
                                    <th>Descripción</th>
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
                    <p class="text-muted">No se encontraron diseños</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="confirmarSeleccion">
                    <i class="fas fa-check me-1"></i>Confirmar Selección (<span id="contadorSeleccionados">0</span>)
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Variables globales para diseños
let disenosSeleccionados = [];
let disenosDisponibles = [];

// Cargar diseños cuando se abre el modal
document.getElementById('disenosModal').addEventListener('show.bs.modal', function() {
    cargarDisenosDisponibles();
});

// Función para cargar diseños disponibles
function cargarDisenosDisponibles() {
    const loader = document.getElementById('disenosLoader');
    const table = document.getElementById('disenosTable');
    const noFound = document.getElementById('noDisenosFound');

    // Mostrar loader y ocultar otros elementos
    loader.style.display = 'block';
    table.style.display = 'none';
    noFound.style.display = 'none';

    // Simular carga de datos (reemplazar con llamada AJAX real)
    setTimeout(() => {
        // Aquí iría la llamada AJAX real
        // Por ahora simulamos datos
        disenosDisponibles = []; // Reemplazar con datos reales
        
        if (disenosDisponibles.length > 0) {
            mostrarDisenosEnTabla(disenosDisponibles);
            loader.style.display = 'none';
            table.style.display = 'block';
        } else {
            loader.style.display = 'none';
            noFound.style.display = 'block';
        }
    }, 500);
}

// Función para mostrar diseños en la tabla
function mostrarDisenosEnTabla(disenos) {
    const tbody = document.getElementById('disenosTableBody');
    tbody.innerHTML = '';

    disenos.forEach(diseno => {
        const isSelected = disenosSeleccionados.some(d => d.id === diseno.id);
        const imagenSrc = diseno.imagen ? `/storage/${diseno.imagen}` : '{{ asset("img/no-image.png") }}';

        const row = `
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input diseno-checkbox" 
                           value="${diseno.id}" ${isSelected ? 'checked' : ''}>
                </td>
                <td>
                    <img src="${imagenSrc}" alt="Diseño" class="img-thumbnail" 
                         style="width: 60px; height: 60px; object-fit: cover;"
                         onerror="this.src='{{ asset('img/no-image.png') }}'">
                </td>
                <td>${diseno.nombre || 'Sin nombre'}</td>
                <td>${diseno.descripcion || 'Sin descripción'}</td>
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

// Actualizar selección de diseños
function actualizarSeleccion() {
    const checkboxes = document.querySelectorAll('.diseno-checkbox:checked');
    disenosSeleccionados = [];

    checkboxes.forEach(checkbox => {
        const disenoId = parseInt(checkbox.value);
        const diseno = disenosDisponibles.find(d => d.id === disenoId);
        if (diseno) {
            disenosSeleccionados.push({
                id: diseno.id,
                nombre: diseno.nombre,
                imagen: diseno.imagen
            });
        }
    });

    actualizarContador();
}

// Actualizar contador de seleccionados
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
    const modal = bootstrap.Modal.getInstance(document.getElementById('disenosModal'));
    modal.hide();
});

// Actualizar la galería de diseños seleccionados
function actualizarGaleriaDisenosSeleccionados() {
    const disenosContainer = document.getElementById('disenosSeleccionados');
    
    if (disenosSeleccionados.length === 0) {
        disenosContainer.innerHTML = '<p class="text-muted text-center">Sin diseños seleccionados</p>';
        document.getElementById('disenosVinculados').value = '';
        return;
    }

    let html = '<div class="row g-2">';
    const ids = [];

    disenosSeleccionados.forEach(diseno => {
        const imagenSrc = diseno.imagen ? `/storage/${diseno.imagen}` : '{{ asset("img/no-image.png") }}';
        
        html += `
            <div class="col-md-3 col-6">
                <div class="card h-100">
                    <img src="${imagenSrc}" class="card-img-top" style="height: 100px; object-fit: cover;"
                         onerror="this.src='{{ asset('img/no-image.png') }}'"
                         alt="${diseno.nombre || 'Diseño'}">
                    <div class="card-body p-2 text-center">
                        <p class="card-text small mb-0">${diseno.nombre || 'Sin nombre'}</p>
                    </div>
                </div>
            </div>
        `;
        
        ids.push(diseno.id);
    });

    html += '</div>';
    disenosContainer.innerHTML = html;
    document.getElementById('disenosVinculados').value = JSON.stringify(disenosSeleccionados);
}

// Búsqueda en tiempo real
document.getElementById('buscarDisenos').addEventListener('input', function() {
    const termino = this.value.toLowerCase();
    if (termino.length === 0) {
        mostrarDisenosEnTabla(disenosDisponibles);
        return;
    }

    const disenosFiltrados = disenosDisponibles.filter(diseno => 
        (diseno.nombre || '').toLowerCase().includes(termino) ||
        (diseno.descripcion || '').toLowerCase().includes(termino)
    );

    mostrarDisenosEnTabla(disenosFiltrados);
    
    // Mostrar mensaje si no hay resultados
    const noResults = disenosFiltrados.length === 0;
    document.getElementById('noDisenosFound').style.display = noResults ? 'block' : 'none';
    document.getElementById('disenosTable').style.display = noResults ? 'none' : 'block';
});
</script>
@endpush
