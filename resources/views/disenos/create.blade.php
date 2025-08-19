@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Crear Nuevo Diseño</h3>
                    <div class="card-tools">
                        <a href="{{ route('disenos.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <form action="{{ route('disenos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Comentario -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="comentario">Comentario</label>
                                    <input type="text" class="form-control @error('comentario') is-invalid @enderror" 
                                           id="comentario" name="comentario" value="{{ old('comentario') }}" 
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
                                           id="idDiseñador" name="idDiseñador" value="{{ old('idDiseñador') }}" 
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
                                        <option value="no realizado" {{ old('estadoDiseño') == 'no realizado' ? 'selected' : '' }}>
                                            No Realizado
                                        </option>
                                        <option value="en proceso" {{ old('estadoDiseño') == 'en proceso' ? 'selected' : '' }}>
                                            En Proceso
                                        </option>
                                        <option value="terminado" {{ old('estadoDiseño') == 'terminado' ? 'selected' : '' }}>
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
                                            <option value="{{ $empleado->idEmpleado }}" {{ old('idEmpleado') == $empleado->idEmpleado ? 'selected' : '' }}>
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
                                        <option value="1" {{ old('estado', '1') == '1' ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
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
                                           id="iddetalleVenta" name="iddetalleVenta" value="{{ old('iddetalleVenta') }}" 
                                           min="1">
                                    @error('iddetalleVenta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Opcional: vincular con un detalle de venta específico</small>
                                </div>
                            </div>

                            <!-- Archivo -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="archivo">Archivo de Diseño</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('archivo') is-invalid @enderror" 
                                               id="archivo" name="archivo" accept=".svg,.ai,.psd,.pdf,.zip,.jpg,.png">
                                        <label class="custom-file-label" for="archivo">Seleccionar archivo...</label>
                                    </div>
                                    @error('archivo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Formatos permitidos: SVG, AI, PSD, PDF, ZIP, JPG, PNG. Tamaño máximo: 10MB
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Diseño
                        </button>
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
// Actualizar el nombre del archivo seleccionado
$('#archivo').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName || 'Seleccionar archivo...');
});
</script>
@endpush
@endsection
