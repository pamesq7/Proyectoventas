<!-- Modal Nueva Característica -->
<div class="modal fade" id="nuevaCaracteristicaModal" tabindex="-1" aria-labelledby="nuevaCaracteristicaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="nuevaCaracteristicaModalLabel">
                    <i class="fas fa-tag me-2"></i>Nueva Característica
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevaCaracteristica" action="{{ route('caracteristicas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="opcionCaracteristica" class="form-label">
                            Opción <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="opcionCaracteristica" name="idOpcion" required>
                            <option value="">Selecciona una opción</option>
                            @foreach($opciones as $opcion)
                            @if($opcion->estado)
                            <option value="{{ $opcion->idOpcion }}">{{ $opcion->nombre }}</option>
                            @endif
                            @endforeach
                        </select>
                        <div class="form-text">
                            Selecciona la opción a la que pertenece esta característica
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nombreCaracteristica" class="form-label">
                            Nombre <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nombreCaracteristica" name="nombre" required maxlength="100" placeholder="Ej: Pequeño, Rojo, Algodón">
                        <div class="form-text">
                            <span id="contadorNombreCaracteristica">0</span>/100 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcionCaracteristica" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcionCaracteristica" name="descripcion" rows="3" maxlength="255" placeholder="Descripción opcional de la característica..."></textarea>
                        <div class="form-text">
                            <span id="contadorDescripcionCaracteristica">0</span>/255 caracteres
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save me-1"></i>Guardar Característica
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Característica -->
<div class="modal fade" id="editarCaracteristicaModal" tabindex="-1" aria-labelledby="editarCaracteristicaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editarCaracteristicaModalLabel">
                    <i class="fas fa-edit me-2"></i>Editar Característica
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarCaracteristica" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editCaracteristicaId" name="idCaracteristica">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editOpcionCaracteristica" class="form-label">
                            Opción <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="editOpcionCaracteristica" name="idOpcion" required>
                            <option value="">Selecciona una opción</option>
                            @foreach($opciones as $opcion)
                            <option value="{{ $opcion->idOpcion }}">{{ $opcion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editNombreCaracteristica" class="form-label">
                            Nombre <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="editNombreCaracteristica" name="nombre" required maxlength="100">
                        <div class="form-text">
                            <span id="editContadorNombreCaracteristica">0</span>/100 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editDescripcionCaracteristica" class="form-label">Descripción</label>
                        <textarea class="form-control" id="editDescripcionCaracteristica" name="descripcion" rows="3" maxlength="255"></textarea>
                        <div class="form-text">
                            <span id="editContadorDescripcionCaracteristica">0</span>/255 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="editEstadoCaracteristica" name="estado">
                            <label class="form-check-label" for="editEstadoCaracteristica">
                                Característica activa
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atención:</strong> Si cambias la opción de esta característica, puede afectar las variantes de productos existentes.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Actualizar Característica
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
