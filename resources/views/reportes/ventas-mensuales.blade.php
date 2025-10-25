@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-chart-bar me-2"></i>
        REPORTE DE VENTAS MENSUALES
    </h1>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.ventas-mensuales') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="year" class="form-label">Seleccionar Año</label>
                    <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                        @foreach($añosDisponibles as $año)
                            <option value="{{ $año }}" {{ $año == $year ? 'selected' : '' }}>
                                {{ $año }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen Estadístico -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small">Total Anual</div>
                            <div class="h5 mb-0">Bs. {{ number_format($totalAnual, 2) }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small">Promedio Mensual</div>
                            <div class="h5 mb-0">Bs. {{ number_format($promedioMensual, 2) }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small">Mejor Mes</div>
                            <div class="h6 mb-0">{{ $mesMayorVenta['mes'] ?? 'N/A' }}</div>
                            <small>Bs. {{ number_format($mesMayorVenta['total'] ?? 0, 2) }}</small>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small">Peor Mes</div>
                            <div class="h6 mb-0">{{ $mesMenorVenta['mes'] ?? 'N/A' }}</div>
                            <small>Bs. {{ number_format($mesMenorVenta['total'] ?? 0, 2) }}</small>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Ventas Mensuales -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>
                VENTAS POR MESES DEL {{ $year }}
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="60%">MES</th>
                            <th width="40%">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventasCompletas as $venta)
                        <tr>
                            <td class="fw-bold">{{ $venta['mes'] }}</td>
                            <td class="text-end">
                                <span class="badge bg-success fs-6">
                                    Bs. {{ number_format($venta['total'], 2) }}
                                </span>
                                @if($venta['cantidad_ventas'] > 0)
                                <br>
                                <small class="text-muted">
                                    {{ $venta['cantidad_ventas'] }} venta(s)
                                </small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-active">
                        <tr>
                            <td class="fw-bold">TOTAL {{ $year }}</td>
                            <td class="text-end fw-bold fs-5 text-primary">
                                Bs. {{ number_format($totalAnual, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <a href="{{ route('reportes.ventas-rango') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-calendar-alt me-1"></i>Reporte por Rango
                    </a>

                    <button onclick="window.print()" class="btn btn-outline-danger">
                        <i class="fas fa-print me-1"></i>Imprimir Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .card-header, .btn, .breadcrumb, .fa-2x {
        display: none !important;
    }
    .table {
        border: 2px solid #000 !important;
    }
    .table th {
        background-color: #f8f9fa !important;
        color: #000 !important;
    }
}
</style>
@endsection