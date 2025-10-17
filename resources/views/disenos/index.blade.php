@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestión de Diseños</h3>
                    <div class="card-tools">
                        <a href="{{ route('disenos.create') }}" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-plus"></i> Nuevo Diseño
                        </a>
                        <a href="{{ route('export.disenos.pdf') }}" class="btn btn-success btn-sm" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i>
                            Exportar PDF
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('disenos.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="estadoDiseno" class="form-control">
                                    <option value="">Todos los estados</option>
                                    @if(isset($estadosDiseno) && count($estadosDiseno) > 0)
                                    @foreach($estadosDiseno as $estado)
                                    <option value="{{ $estado }}" {{ request('estadoDiseno') == $estado ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $estado)) }}
                                    </option>
                                    @endforeach
                                    @else
                                    <option value="no realizado">No Realizado</option>
                                    <option value="en proceso">En Proceso</option>
                                    <option value="terminado">Terminado</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="estado" class="form-control">
                                    <option value="">Activo/Inactivo</option>
                                    <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="buscar" class="form-control" placeholder="Buscar por comentario..." value="{{ request('buscar') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary">Filtrar</button>
                                <a href="{{ route('disenos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de diseños -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Imagen</th>
                                    <th>Comentario</th>
                                    <th>Estado Diseño</th>
                                    <th>Diseñador</th>
                                    <th>Clientes</th>
                                    <th>Estado</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $contador = ($disenos->currentPage() - 1) * $disenos->perPage() + 1; @endphp
                                @forelse($disenos as $diseno)
                                <tr>
                                    <td>{{ $contador++ }}</td>
                                    <td style="width: 120px;">
                                        @if($diseno->archivo)
                                        @php
                                        $extension = pathinfo($diseno->archivo, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $imagePath = asset('storage/' . $diseno->archivo);
                                        @endphp

                                        @if($isImage)
                                        <div class="text-center">
                                            <img src="{{ $imagePath }}"
                                                alt="Diseño"
                                                class="img-thumbnail"
                                                style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                                onclick="window.open('{{ $imagePath }}', '_blank')"
                                                onerror="this.style.display='none';">
                                            <div class="image-fallback" style="display: none;">
                                                <i class="fas fa-image text-muted"></i>
                                                <br>
                                                <small class="text-muted">Imagen no disponible</small>
                                            </div>
                                        </div>
                                        @else
                                        <div class="text-center">
                                            <i class="fas fa-file fa-2x text-muted"></i>
                                            <br>
                                            <small class="text-muted">{{ strtoupper($extension) }}</small>
                                            <br>
                                            <a href="{{ $imagePath }}" target="_blank" class="btn btn-xs btn-outline-info mt-1">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                        @endif
                                        @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-image fa-2x"></i>
                                            <br>
                                            <small>Sin archivo</small>
                                        </div>
                                        @endif
                                    </td>
                                    <td>{{ $diseno->comentario ?? 'Sin comentario' }}</td>
                                    <td>
                                        @php
                                        $badgeClass = 'secondary';
                                        if ($diseno->estadoDiseño == 'terminado') {
                                        $badgeClass = 'success';
                                        } elseif ($diseno->estadoDiseño == 'en proceso') {
                                        $badgeClass = 'warning';
                                        } elseif ($diseno->estadoDiseño == 'no realizado') {
                                        $badgeClass = 'secondary';
                                        }
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}" style="color: black;">
                                            {{ ucfirst($diseno->estadoDiseño) }}
                                        </span>
                                    </td>
                                    <td>{{ $diseno->idDiseñador ?? 'N/A' }}</td>

                                    <td>
                                        @if($diseno->detalleVenta && $diseno->detalleVenta->venta)
                                        @if($diseno->detalleVenta->venta->clienteNatural && $diseno->detalleVenta->venta->clienteNatural->user)
                                        {{ $diseno->detalleVenta->venta->clienteNatural->user->name }}
                                        {{ $diseno->detalleVenta->venta->clienteNatural->user->primerApellido }}
                                        @elseif($diseno->detalleVenta->venta->clienteEstablecimiento)
                                        {{ $diseno->detalleVenta->venta->clienteEstablecimiento->razonSocial }}
                                        @else
                                        <span class="text-muted">Cliente no especificado</span>
                                        @endif
                                        @else
                                        <span class="text-muted">Sin venta asociada</span>
                                        @endif
                                    </td>


                                    <td>
                                        <span class="badge badge-{{ $diseno->estado ? 'success' : 'danger' }}" style="color: black;">
                                            {{ $diseno->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>{{ $diseno->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('disenos.show', $diseno) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <!-- Se eliminaron los botones de editar y eliminar -->
                                        </div>
                                    </td>
                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No se encontraron diseños</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $disenos->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection