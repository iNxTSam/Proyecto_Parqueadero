@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Resumen administrativo')
@section('page', 'admin-dashboard')

@section('content')
    <div class="row g-3 mb-4">
        <x-stat-card label="Clientes" :value="$stats['clientes']" icon="bi-person-vcard" tone="primary" hint="Registrados en el sistema" />
        <x-stat-card label="Vehículos" :value="$stats['vehiculos']" icon="bi-car-front" tone="info" hint="Activos e inactivos" />
        <x-stat-card label="Contratos activos" :value="$stats['contratos_activos']" icon="bi-file-earmark-check" tone="success" hint="Vigentes hoy" />
        <x-stat-card label="Por vencer (7 días)" :value="$stats['por_vencer']" icon="bi-alarm" tone="warning" hint="Requieren renovación" />
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-alarm me-1"></i> Próximos vencimientos</span>
                    <a href="{{ route('admin.contratos') }}" class="small">Ver contratos</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Placa</th>
                                <th>Propietario</th>
                                <th>Tarifa</th>
                                <th>Vence</th>
                                <th class="text-end">Días</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proximos as $contrato)
                                <tr>
                                    <td><span class="plate">{{ $contrato->vehiculo_id }}</span></td>
                                    <td>{{ $contrato->propietario?->nombre }}</td>
                                    <td>{{ $contrato->tarifa?->nombre }}</td>
                                    <td>{{ $contrato->fecha_fin?->format('d/m/Y') }}</td>
                                    <td class="text-end">
                                        @php($dias = today()->diffInDays($contrato->fecha_fin, false))
                                        <span class="badge {{ $dias <= 2 ? 'text-bg-danger' : 'text-bg-warning' }}">{{ $dias }} días</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <x-empty-state icon="bi-check2-circle" title="Sin vencimientos próximos" text="No hay contratos que venzan en los próximos 7 días." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-1"></i> Contratos recientes</span>
                    <a href="{{ route('admin.contratos') }}" class="small">Ver todos</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recientes as $contrato)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">{{ $contrato->vehiculo_id }} · {{ $contrato->propietario?->nombre }}</div>
                                <div class="text-muted small">{{ $contrato->fecha_inicio?->format('d/m/Y') }} → {{ $contrato->fecha_fin?->format('d/m/Y') }}</div>
                            </div>
                            <span class="badge text-bg-light border">{{ $contrato->estado?->nombre }}</span>
                        </div>
                    @empty
                        <div class="list-group-item">
                            <x-empty-state icon="bi-file-earmark-text" title="Sin contratos" text="Crea el primer contrato desde el módulo de contratos." />
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.usuarios') }}" class="btn btn-outline-primary"><i class="bi bi-people me-1"></i> Gestionar usuarios</a>
                    <a href="{{ route('admin.clientes') }}" class="btn btn-outline-primary"><i class="bi bi-person-vcard me-1"></i> Gestionar clientes</a>
                    <a href="{{ route('admin.vehiculos') }}" class="btn btn-outline-primary"><i class="bi bi-car-front me-1"></i> Gestionar vehículos</a>
                    <a href="{{ route('admin.contratos') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Nuevo contrato</a>
                </div>
            </div>
        </div>
    </div>
@endsection
