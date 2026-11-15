@extends('layouts.app')

@section('title', 'Vehículos')
@section('subtitle', 'Consulta de vehículos registrados')
@section('page', 'vigilante-vehiculos')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form action="{{ route('vigilante.vehiculos') }}" method="get" class="d-flex gap-2">
            <input type="search" name="buscar" value="{{ $buscar }}" class="form-control w-auto" placeholder="Placa, documento o propietario" style="min-width: 260px">
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </form>
        <span class="badge text-bg-light border">{{ $vehiculos->total() }} vehículos</span>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Tipo</th>
                        <th>Propietario</th>
                        <th>Color</th>
                        <th>Marca / modelo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehiculos as $vehiculo)
                        <tr>
                            <td><span class="plate">{{ $vehiculo->placa }}</span></td>
                            <td>{{ $vehiculo->tipo?->nombre }}</td>
                            <td>
                                <div class="fw-semibold">{{ $vehiculo->propietario?->nombre }}</div>
                                <div class="text-muted small">{{ $vehiculo->propietario_id }}</div>
                            </td>
                            <td>{{ $vehiculo->color ?? '—' }}</td>
                            <td>{{ trim(($vehiculo->marca ?? '').' '.($vehiculo->modelo ?? '')) ?: '—' }}</td>
                            <td>
                                @if($vehiculo->estado_parqueo)
                                    <span class="badge text-bg-success">Dentro</span>
                                @else
                                    <span class="badge text-bg-secondary">Fuera</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="bi-car-front" title="Sin vehículos" text="No se encontraron vehículos con ese criterio." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vehiculos->hasPages())
            <div class="card-footer bg-white border-top-0">
                {{ $vehiculos->links() }}
            </div>
        @endif
    </div>
@endsection
