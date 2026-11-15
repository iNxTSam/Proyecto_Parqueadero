@extends('layouts.app')

@section('title', 'Bahías')
@section('subtitle', 'Capacidad instalada del parqueadero')
@section('page', 'superadmin-bahias')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge text-bg-light border">{{ $bahias->total() }} bahías</span>
        <button class="btn btn-primary" id="btnNuevaBahia" data-bs-toggle="modal" data-bs-target="#modalBahia">
            <i class="bi bi-plus-lg me-1"></i> Nueva bahía
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Tipo de vehículo</th>
                        <th class="text-end">Capacidad</th>
                        <th class="text-end">Vehículos del tipo</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bahias as $bahia)
                        <tr>
                            <td class="fw-semibold">{{ $bahia->numero }}</td>
                            <td>{{ $bahia->tipoVehiculo?->nombre }}</td>
                            <td class="text-end">{{ $bahia->capacidad_maxima }}</td>
                            <td class="text-end">{{ (int) ($ocupados[$bahia->tipo_vehiculo_id] ?? 0) }}</td>
                            <td>{{ $bahia->ubicacion ?? '—' }}</td>
                            <td>
                                @if($bahia->activa)
                                    <span class="badge text-bg-success">Activa</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactiva</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalBahia"
                                    data-numero="{{ $bahia->numero }}"
                                    data-tipo="{{ $bahia->tipo_vehiculo_id }}"
                                    data-capacidad="{{ $bahia->capacidad_maxima }}"
                                    data-ubicacion="{{ $bahia->ubicacion }}"
                                    data-activa="{{ $bahia->activa ? '1' : '0' }}"
                                    data-action="{{ route('superadmin.bahias.update', $bahia) }}"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('superadmin.bahias.delete', $bahia) }}" method="post" class="d-inline" data-confirm="¿Eliminar la bahía {{ $bahia->numero }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state icon="bi-grid-3x3-gap" title="Sin bahías" text="Crea la primera bahía del parqueadero." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bahias->hasPages())
            <div class="card-footer bg-white border-top-0">
                {{ $bahias->links() }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalBahia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formBahia" method="post" action="{{ route('superadmin.bahias.store') }}" data-store-action="{{ route('superadmin.bahias.store') }}">
                    @csrf
                    <div id="bahiaMethod"></div>

                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloBahia">Nueva bahía</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="bahiaNumero">Número *</label>
                                <input type="text" class="form-control" id="bahiaNumero" name="numero" required maxlength="10">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="bahiaTipo">Tipo de vehículo *</label>
                                <select class="form-select" id="bahiaTipo" name="tipo_vehiculo_id" required>
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="bahiaCapacidad">Capacidad *</label>
                                <input type="number" min="1" max="500" class="form-control" id="bahiaCapacidad" name="capacidad_maxima" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="bahiaActiva">Estado</label>
                                <select class="form-select" id="bahiaActiva" name="activa">
                                    <option value="1">Activa</option>
                                    <option value="0">Inactiva</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="bahiaUbicacion">Ubicación</label>
                                <input type="text" class="form-control" id="bahiaUbicacion" name="ubicacion" maxlength="100">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
