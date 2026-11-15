@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Resumen general del parqueadero')
@section('page', 'superadmin-dashboard')

@section('content')
    <div class="row g-3 mb-4">
        <x-stat-card label="Usuarios" :value="$stats['usuarios']" icon="bi-people" tone="primary" hint="Con acceso al sistema" />
        <x-stat-card label="Clientes" :value="$stats['clientes']" icon="bi-person-vcard" tone="info" hint="Registrados" />
        <x-stat-card label="Vehículos" :value="$stats['vehiculos']" icon="bi-car-front" tone="success" hint="En el sistema" />
        <x-stat-card label="Contratos activos" :value="$stats['contratos_activos']" icon="bi-file-earmark-check" tone="warning" hint="Vigentes hoy" />
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-cash-stack me-1"></i> Facturación</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="stat-label">Facturado este mes</div>
                            <div class="fs-4 fw-bold">${{ number_format((float) $stats['facturado_mes'], 0, ',', '.') }}</div>
                        </div>
                        <span class="stat-icon bg-success-subtle text-success"><i class="bi bi-receipt"></i></span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Pendiente por cobrar</div>
                            <div class="fs-5 fw-semibold">${{ number_format((float) $stats['pendiente'], 0, ',', '.') }}</div>
                        </div>
                        <span class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-hourglass-split"></i></span>
                    </div>

                    <a href="{{ route('superadmin.reportes') }}" class="btn btn-outline-primary btn-sm mt-4">
                        <i class="bi bi-file-earmark-bar-graph me-1"></i> Generar reporte de ingresos
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-grid-3x3-gap me-1"></i> Ocupación por tipo</div>
                <div class="card-body">
                    @forelse($ocupacion as $item)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold">{{ $item['tipo'] }}</span>
                                <span class="text-muted small">{{ $item['ocupados'] }} / {{ $item['capacidad'] }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar {{ $item['porcentaje'] >= 90 ? 'bg-danger' : ($item['porcentaje'] >= 70 ? 'bg-warning' : 'bg-success') }}"
                                     style="width: {{ $item['porcentaje'] }}%"
                                     role="progressbar"
                                     aria-valuenow="{{ $item['porcentaje'] }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                            <div class="text-muted small mt-1">{{ $item['libres'] }} cupos libres · {{ $item['bahias'] }} bahías</div>
                        </div>
                    @empty
                        <x-empty-state icon="bi-grid-3x3-gap" title="Sin bahías activas" text="Configura las bahías del parqueadero." />
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clock-history me-1"></i> Reportes recientes</span>
            <a href="{{ route('superadmin.reportes') }}" class="small">Ir a reportes</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Reporte</th>
                        <th>Formato</th>
                        <th>Generado por</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportes as $reporte)
                        <tr>
                            <td class="fw-semibold">{{ $reporte->nombre }}</td>
                            <td><span class="badge text-bg-light border">{{ $reporte->formato }}</span></td>
                            <td>{{ $reporte->generadoPor?->nombre }}</td>
                            <td>{{ $reporte->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state icon="bi-file-earmark-bar-graph" title="Sin reportes" text="Genera el primer reporte desde el módulo de reportes." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
