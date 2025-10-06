@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Panel del Diseñador</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Diseños Pendientes</h5>
                                    <p class="card-text display-4">{{ App\Models\Diseno::where('estado', 'pendiente')->count() }}</p>
                                    <a href="{{ route('disenos.index', ['estado' => 'pendiente']) }}" class="btn btn-light btn-sm">Ver pendientes</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Diseños Completados</h5>
                                    <p class="card-text display-4">{{ App\Models\Diseno::where('estado', 'completado')->count() }}</p>
                                    <a href="{{ route('disenos.index', ['estado' => 'completado']) }}" class="btn btn-light btn-sm">Ver completados</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Mis Diseños</h5>
                                    <p class="card-text display-4">{{ App\Models\Diseno::where('idEmpleado', auth()->user()->empleado->idEmpleado)->count() }}</p>
                                    <a href="{{ route('disenos.mis-disenos') }}" class="btn btn-dark btn-sm">Ver mis diseños</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Diseños Recientes</h5>
                                    <a href="{{ route('disenos.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nuevo Diseño
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Cliente</th>
                                                    <th>Fecha Creación</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse (App\Models\Diseno::latest()->take(5)->get() as $diseno)
                                                    <tr>
                                                        <td>{{ $diseno->idDiseno }}</td>
                                                        <td>{{ $diseno->nombre }}</td>
                                                        <td>{{ $diseno->cliente->nombre ?? 'N/A' }}</td>
                                                        <td>{{ $diseno->fecha_creacion->format('d/m/Y') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $diseno->estado == 'completado' ? 'success' : 'warning' }}">
                                                                {{ ucfirst($diseno->estado) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('disenos.show', $diseno->idDiseno) }}" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i> Ver
                                                            </a>
                                                            @if($diseno->estado == 'pendiente' && $diseno->idEmpleado == auth()->user()->empleado->idEmpleado)
                                                                <a href="{{ route('disenos.edit', $diseno->idDiseno) }}" class="btn btn-sm btn-warning">
                                                                    <i class="fas fa-edit"></i> Editar
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">No hay diseños registrados</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('disenos.index') }}" class="btn btn-outline-secondary">
                                            Ver todos los diseños
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
