@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Diseño #{{ $diseno->idDiseno }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('disenos.show', $diseno) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="{{ route('disenos.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <form action="{{ route('disenos.update', $diseno) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <!-- Comentario -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="comentario">Comentario</label>
                                    <input type="text" class="form-control @error('comentario') is-invalid @enderror" 
                                           id="comentario" name="comentario" value="{{ old('comentario', $diseno->comentario) }}" 
                                           maxlength="45" placeholder="Descripción del diseño">
                                    @error('comentario')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- ID Diseñador -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="idDiseñador">ID Diseñador</label>
                                    <input type="number" class="form-control @error('idDiseñador') is-invalid @enderror" 
                                           id="idDiseñador" name="idDiseñador" value="{{ old('idDiseñador', $diseno->idDiseñador) }}" 
                                           min="1">
                                    @error('idDiseñador')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Estado del Diseño -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estadoDiseño">Estado del Diseño <span class="text-danger">*</span></label>
                                    <select class="form-control @error('estadoDiseño') is-invalid @enderror" 
                                            id="estadoDiseño" name="estadoDiseño" required>
                                        <option value="">Seleccione un estado</option>
                                        <option value="no realizado" {{ old('estadoDiseño', $diseno->estadoDiseño) == 'no realizado' ? 'selected' : '' }}>
                                            No Realizado
                                        </option>
                                        <option value="en proceso" {{ old('estadoDiseño', $diseno->estadoDiseño) == 'en proceso' ? 'selected' : '' }}>
                                            En Proceso
                                        </option>
                                        <option value="terminado" {{ old('estadoDiseño', $diseno->estadoDiseño) == 'terminado' ? 'selected' : '' }}>
                                            Terminado
                                        </option>
                                    </select>
                                    @error('estadoDiseño')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Empleado -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="idEmpleado">Empleado</label>
                                    <select class="form-control @error('idEmpleado') is-invalid @enderror" 
                                            id="idEmpleado" name="idEmpleado">
                                        <option value="">Seleccione un empleado</option>
                                        @foreach($empleados as $empleado)
                                            <option value="{{ $empleado->idEmpleado }}" {{ old('idEmpleado', $diseno->idEmpleado) == $empleado->idEmpleado ? 'selected' : '' }}>
                                                {{ $empleado->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('idEmpleado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Estado Activo/Inactivo -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estado">Estado <span class="text-danger">*</span></label>
                                    <select class="form-control @error('estado') is-invalid @enderror" 
                                            id="estado" name="estado" required>
                                        <option value="1" {{ old('estado', $diseno->estado) == '1' ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('estado', $diseno->estado) == '0' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- ID Detalle Venta (opcional) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="iddetalleVenta">ID Detalle Venta (opcional)</label>
                                    <input type="number" class="form-control @error('iddetalleVenta') is-invalid @enderror" 
                                           id="iddetalleVenta" name="iddetalleVenta" value="{{ old('iddetalleVenta', $diseno->iddetalleVenta) }}" 
                                           min="1">
                                    @error('iddetalleVenta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Opcional: vincular con un detalle de venta específico</small>
                                </div>
                            </div>



                            <!-- Nuevo archivo -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="archivo">{{ $diseno->archivo ? 'Reemplazar Archivo' : 'Archivo de Diseño' }}</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('archivo') is-invalid @enderror" 
                                               id="archivo" name="archivo" accept=".svg,.ai,.psd,.pdf,.zip,.jpg,.png">
                                        <label class="custom-file-label" for="archivo">
                                            {{ $diseno->archivo ? 'Seleccionar nuevo archivo...' : 'Seleccionar archivo...' }}
                                        </label>
                                    </div>
                                    @error('archivo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Formatos permitidos: SVG, AI, PSD, PDF, ZIP, JPG, PNG. Tamaño máximo: 10MB
                                        @if($diseno->archivo)
                                            <br><strong>Nota:</strong> Si selecciona un nuevo archivo, reemplazará el archivo actual.
                                        @endif
                                    </small>
                                </div>
                                
                                <!-- Vista previa del nuevo archivo -->
                                <div id="preview-container" class="mt-3" style="display: none;">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Vista Previa del Nuevo Archivo</h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <div id="image-preview" style="display: none;">
                                                <img id="preview-image" src="" alt="Vista previa" class="img-fluid" style="max-height: 200px;">
                                            </div>
                                            <div id="file-preview" style="display: none;">
                                                <i class="fas fa-file fa-3x text-muted"></i>
                                                <p class="mt-2 mb-0" id="file-name"></p>
                                                <small class="text-muted" id="file-type"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Diseño
                        </button>
                        <a href="{{ route('disenos.show', $diseno) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Ver Diseño
                        </a>
                        <a href="{{ route('disenos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Actualizar el nombre del archivo seleccionado y mostrar vista previa
$(document).ready(function() {
    $('#archivo').on('change', function() {
        var file = this.files[0];
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Seleccionar archivo...');
        
        if (file) {
            var fileType = file.type;
            var fileExtension = fileName.split('.').pop().toLowerCase();
            
            // Mostrar el contenedor de vista previa
            $('#preview-container').show();
            
            // Verificar si es una imagen
            if (fileType.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                // Mostrar vista previa de imagen
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview-image').attr('src', e.target.result);
                    $('#image-preview').show();
                    $('#file-preview').hide();
                };
                reader.readAsDataURL(file);
            } else {
                // Mostrar vista previa de archivo no imagen
                $('#file-name').text(fileName);
                $('#file-type').text(fileExtension.toUpperCase());
                $('#image-preview').hide();
                $('#file-preview').show();
            }
        } else {
            // Ocultar vista previa si no hay archivo
            $('#preview-container').hide();
        }
    });
});
</script>
@endpush
@endsection
