<!-- Modal Nueva Opción -->
<div class="modal fade" id="nuevaOpcionModal" tabindex="-1" aria-labelledby="nuevaOpcionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="nuevaOpcionModalLabel">
                    <i class="fas fa-cog me-2"></i>Nueva Opción
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevaOpcion" action="{{ route('opciones.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombreOpcion" class="form-label">
                            Nombre <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nombreOpcion" name="nombre" required maxlength="100" placeholder="Ej: Talla, Color, Material">
                        <div class="form-text">
                            <span id="contadorNombreOpcion">0</span>/100 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcionOpcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcionOpcion" name="descripcion" rows="3" maxlength="255" placeholder="Describe para qué sirve esta opción..."></textarea>
                        <div class="form-text">
                            <span id="contadorDescripcionOpcion">0</span>/255 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="estadoOpcion" name="estado" checked>
                            <label class="form-check-label" for="estadoOpcion">
                                Opción activa
                            </label>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Tip:</strong> Después de crear la opción, podrás agregar características específicas como "S, M, L" para Talla o "Rojo, Azul, Verde" para Color.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Guardar Opción
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Opción -->
<div class="modal fade" id="editarOpcionModal" tabindex="-1" aria-labelledby="editarOpcionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editarOpcionModalLabel">
                    <i class="fas fa-edit me-2"></i>Editar Opción
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarOpcion" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editOpcionId" name="idOpcion">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editNombreOpcion" class="form-label">
                            Nombre <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="editNombreOpcion" name="nombre" required maxlength="100">
                        <div class="form-text">
                            <span id="editContadorNombreOpcion">0</span>/100 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editDescripcionOpcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="editDescripcionOpcion" name="descripcion" rows="3" maxlength="255"></textarea>
                        <div class="form-text">
                            <span id="editContadorDescripcionOpcion">0</span>/255 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="editEstadoOpcion" name="estado">
                            <label class="form-check-label" for="editEstadoOpcion">
                                Opción activa
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Características asociadas:</strong> <span id="caracteristicasAsociadasCount">0</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Actualizar Opción
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Agregar Característica a Opción -->
<div class="modal fade" id="agregarCaracteristicaOpcionModal" tabindex="-1" aria-labelledby="agregarCaracteristicaOpcionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="agregarCaracteristicaOpcionModalLabel">
                    <i class="fas fa-plus me-2"></i>Agregar Característica
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAgregarCaracteristicaOpcion" action="{{ route('caracteristicas.store') }}" method="POST">
                @csrf
                <input type="hidden" id="opcionIdCaracteristica" name="idOpcion">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Agregando característica a la opción: <strong id="nombreOpcionCaracteristica"></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nombreCaracteristicaOpcion" class="form-label">
                            Nombre de la Característica <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nombreCaracteristicaOpcion" name="nombre" required maxlength="100" placeholder="Ej: Pequeño, Rojo, Algodón">
                        <div class="form-text">
                            <span id="contadorNombreCaracteristicaOpcion">0</span>/100 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcionCaracteristicaOpcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcionCaracteristicaOpcion" name="descripcion" rows="2" maxlength="255" placeholder="Descripción opcional..."></textarea>
                        <div class="form-text">
                            <span id="contadorDescripcionCaracteristicaOpcion">0</span>/255 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="estadoCaracteristicaOpcion" name="estado" checked>
                            <label class="form-check-label" for="estadoCaracteristicaOpcion">
                                Característica activa
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save me-1"></i>Agregar Característica
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
