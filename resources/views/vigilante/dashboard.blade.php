@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Operación del parqueadero en tiempo real')
@section('page', 'vigilante-dashboard')

@section('content')
    <div class="row g-3 mb-4">
        <x-stat-card label="Vehículos dentro" :value="$stats['ocupados']" icon="bi-car-front-fill" tone="primary" :hint="'Capacidad total: '.$stats['capacidad']" />
        <x-stat-card label="Cupos libres" :value="$stats['libres']" icon="bi-p-circle" tone="success" hint="Según bahías activas" />
        <x-stat-card label="Entradas hoy" :value="$stats['entradas_hoy']" icon="bi-box-arrow-in-right" tone="info" :hint="now()->translatedFormat('d M Y')" />
        <x-stat-card label="Salidas hoy" :value="$stats['salidas_hoy']" icon="bi-box-arrow-right" tone="warning" :hint="now()->translatedFormat('d M Y')" />
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-grid-3x3-gap me-1"></i> Ocupación por tipo</span>
                    <a href="{{ route('vigilante.entradas') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Registrar entrada
                    </a>
                </div>
                <div class="card-body">
                    @forelse($ocupacion as $item)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold">{{ $item['tipo'] }}</span>
                                <span class="text-muted small">{{ $item['ocupados'] }} / {{ $item['capacidad'] }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar {{ $item['porcentaje'] >= 90 ? 'bg-danger' : ($item['porcentaje'] >= 70 ? 'bg-warning' : 'bg-success') }}"
                                     role="progressbar"
                                     style="width: {{ $item['porcentaje'] }}%"
                                     aria-valuenow="{{ $item['porcentaje'] }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                            <div class="text-muted small mt-1">{{ $item['libres'] }} cupos libres · {{ $item['bahias'] }} bahías activas</div>
                        </div>
                    @empty
                        <x-empty-state icon="bi-grid-3x3-gap" title="Sin bahías activas" text="El superadministrador debe configurar las bahías." />
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-1"></i> Últimos vehículos dentro</span>
                    <a href="{{ route('vigilante.entradas') }}" class="small">Ver todos</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Placa</th>
                                <th>Tipo</th>
                                <th>Propietario</th>
                                <th>Ingreso</th>
                                <th>Ticket</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dentro as $entrada)
                                <tr>
                                    <td><span class="plate">{{ $entrada->vehiculo_id }}</span></td>
                                    <td>{{ $entrada->vehiculo?->tipo?->nombre }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $entrada->cliente?->nombre }}</div>
                                        <div class="text-muted small">{{ $entrada->propietario_id }}</div>
                                    </td>
                                    <td class="text-nowrap">{{ $entrada->fecha_entrada?->format('d/m/Y H:i') }}</td>
                                    <td><code>{{ $entrada->ticket }}</code></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <x-empty-state icon="bi-car-front" title="No hay vehículos dentro" text="Registra una entrada para comenzar." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
