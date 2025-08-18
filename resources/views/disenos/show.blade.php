@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalles del Diseño #{{ $diseno->idDiseno }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('disenos.edit', $diseno) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('disenos.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID Diseño:</th>
                                    <td>{{ $diseno->idDiseno }}</td>
                                </tr>
                                <tr>
                                    <th>Comentario:</th>
                                    <td>{{ $diseno->comentario ?? 'Sin comentario' }}</td>
                                </tr>
                                <tr>
                                    <th>Estado del Diseño:</th>
                                    <td>
                                        <span class="badge badge-{{ $diseno->estadoDiseño == 'terminado' ? 'success' : 'warning' }}">
                                            {{ ucfirst($diseno->estadoDiseño) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>ID Diseñador:</th>
                                    <td>{{ $diseno->idDiseñador }}</td>
                                </tr>
                                <tr>
                                    <th>Empleado:</th>
                                    <td>{{ $diseno->empleado->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        <span class="badge badge-{{ $diseno->estado ? 'success' : 'danger' }}">
                                            {{ $diseno->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>ID Detalle Venta:</th>
                                    <td>{{ $diseno->iddetalleVenta ?? 'No asignado' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha de Creación:</th>
                                    <td>{{ $diseno->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Última Actualización:</th>
                                    <td>{{ $diseno->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Archivo de Diseño</h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($diseno->archivo)
                                        @php
                                            $extension = pathinfo($diseno->archivo, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                        @endphp
                                        
                                        @if($isImage)
                                            <img src="{{ Storage::url($diseno->archivo) }}" 
                                                 alt="Diseño" 
                                                 class="img-fluid mb-3" 
                                                 style="max-height: 300px;">
                                        @else
                                            <div class="mb-3">
                                                <i class="fas fa-file fa-5x text-muted"></i>
                                                <p class="mt-2">Archivo: {{ basename($diseno->archivo) }}</p>
                                                <p class="text-muted">Tipo: {{ strtoupper($extension) }}</p>
                                            </div>
                                        @endif
                                        
                                        <div>
                                            <a href="{{ Storage::url($diseno->archivo) }}" 
                                               target="_blank" 
                                               class="btn btn-primary">
                                                <i class="fas fa-download"></i> Descargar Archivo
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-muted">
                                            <i class="fas fa-file-slash fa-3x"></i>
                                            <p class="mt-2">No hay archivo asociado</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('disenos.edit', $diseno) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar Diseño
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <form action="{{ route('disenos.destroy', $diseno) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('¿Está seguro de eliminar este diseño? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Eliminar Diseño
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
