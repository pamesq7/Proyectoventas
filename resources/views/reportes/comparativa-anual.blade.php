@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Comparativa Anual de Ventas</h1>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <label>Seleccionar Años</label>
                    <select name="years[]" class="form-select" multiple size="4">
                        @foreach($añosDisponibles as $año)
                            <option value="{{ $año }}" {{ in_array($año, $selectedYears) ? 'selected' : '' }}>
                                {{ $año }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Mantén Ctrl (Windows) o Cmd (Mac) para seleccionar múltiples años</small>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-chart-line me-1"></i>Comparar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>
                Comparativa de Ventas por Año
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th width="20%">MES</th>
                            @foreach($selectedYears as $year)
                                <th class="text-center" width="{{ 80 / count($selectedYears) }}%">{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $meses = [
                                1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL',
                                5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO',
                                9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
                            ];
                            
                            $totalesPorAño = [];
                            foreach ($selectedYears as $year) {
                                $totalesPorAño[$year] = 0;
                            }
                        @endphp
                        
                        @for($mes = 1; $mes <= 12; $mes++)
                        <tr>
                            <td class="fw-bold">{{ $meses[$mes] }}</td>
                            @foreach($selectedYears as $year)
                                @php
                                    $monto = $comparativa[$year][$mes] ?? 0;
                                    $totalesPorAño[$year] += $monto;
                                @endphp
                                <td class="text-end">
                                    <span class="badge bg-success">
                                        Bs. {{ number_format($monto, 2) }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                        @endfor
                    </tbody>
                    <tfoot class="table-active">
                        <tr>
                            <td class="fw-bold">TOTAL ANUAL</td>
                            @foreach($selectedYears as $year)
                                <td class="text-end fw-bold fs-6 text-primary">
                                    Bs. {{ number_format($totalesPorAño[$year], 2) }}
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Botones de Navegación -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="{{ route('reportes.ventas-mensuales') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-chart-bar me-1"></i>Reporte Mensual
            </a>
            <a href="{{ route('reportes.ventas-rango') }}" class="btn btn-outline-success me-2">
                <i class="fas fa-calendar-alt me-1"></i>Reporte por Rango
            </a>
            <button onclick="window.print()" class="btn btn-outline-danger">
                <i class="fas fa-print me-1"></i>Imprimir
            </button>
        </div>
    </div>
</div>

<style>
@media print {
    .card-header, .btn, .breadcrumb {
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