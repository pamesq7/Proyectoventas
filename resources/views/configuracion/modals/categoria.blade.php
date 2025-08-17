<!-- Modal Nueva Categoría -->
<div class="modal fade" id="nuevaCategoriaModal" tabindex="-1" aria-labelledby="nuevaCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="nuevaCategoriaModalLabel">
                    <i class="fas fa-folder me-2"></i>Nueva Categoría
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevaCategoria" action="{{ route('categorias.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombreCategoria" class="form-label">
                            Nombre <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nombreCategoria" name="nombreCategoria" required maxlength="100">
                        <div class="form-text">
                            <span id="contadorNombreCategoria">0</span>/100 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcionCategoria" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcionCategoria" name="descripcion" rows="3" maxlength="255"></textarea>
                        <div class="form-text">
                            <span id="contadorDescripcionCategoria">0</span>/255 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <!-- Campo oculto para asegurar que siempre se envíe un valor -->
                            <input type="hidden" name="estado" value="0">
                            <input class="form-check-input" type="checkbox" id="estadoCategoria" name="estado" value="1" checked>
                            <label class="form-check-label" for="estadoCategoria">
                                Categoría activa
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Información:</strong> Las categorías se utilizan para organizar los productos en tu sistema.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Crear Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
