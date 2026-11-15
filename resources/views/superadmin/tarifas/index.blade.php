@extends('layouts.app')

@section('title', 'Tarifas')
@section('subtitle', 'Precios por tipo de vehículo y periodo')
@section('page', 'superadmin-tarifas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge text-bg-light border">{{ $tarifas->total() }} tarifas</span>
        <button class="btn btn-primary" id="btnNuevaTarifa" data-bs-toggle="modal" data-bs-target="#modalTarifa">
            <i class="bi bi-plus-lg me-1"></i> Nueva tarifa
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Tipo de vehículo</th>
                        <th class="text-end">Valor</th>
                        <th class="text-end">Días</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarifas as $tarifa)
                        <tr>
                            <td class="fw-semibold">{{ $tarifa->nombre }}</td>
                            <td><span class="badge text-bg-light border">{{ $tarifa->tipo }}</span></td>
                            <td>{{ $tarifa->tipoVehiculo?->nombre }}</td>
                            <td class="text-end">${{ number_format((float) $tarifa->valor, 0, ',', '.') }}</td>
                            <td class="text-end">{{ $tarifa->dias }}</td>
                            <td>
                                @if($tarifa->activa)
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
                                    data-bs-target="#modalTarifa"
                                    data-id="{{ $tarifa->id }}"
                                    data-nombre="{{ $tarifa->nombre }}"
                                    data-tipo="{{ $tarifa->tipo }}"
                                    data-tipo-vehiculo="{{ $tarifa->tipo_vehiculo_id }}"
                                    data-valor="{{ $tarifa->valor }}"
                                    data-dias="{{ $tarifa->dias }}"
                                    data-activa="{{ $tarifa->activa ? '1' : '0' }}"
                                    data-action="{{ route('superadmin.tarifas.update', $tarifa) }}"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('superadmin.tarifas.delete', $tarifa) }}" method="post" class="d-inline" data-confirm="¿Eliminar la tarifa {{ $tarifa->nombre }}?">
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
                                <x-empty-state icon="bi-cash-coin" title="Sin tarifas" text="Crea la primera tarifa del parqueadero." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tarifas->hasPages())
            <div class="card-footer bg-white border-top-0">
                {{ $tarifas->links() }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalTarifa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formTarifa" method="post" action="{{ route('superadmin.tarifas.store') }}" data-store-action="{{ route('superadmin.tarifas.store') }}">
                    @csrf
                    <div id="tarifaMethod"></div>

                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloTarifa">Nueva tarifa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="tarifaNombre">Nombre *</label>
                                <input type="text" class="form-control" id="tarifaNombre" name="nombre" required maxlength="50">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="tarifaTipo">Tipo *</label>
                                <select class="form-select" id="tarifaTipo" name="tipo" required>
                                    <option value="Dia">Día</option>
                                    <option value="Semana">Semana</option>
                                    <option value="Quincena">Quincena</option>
                                    <option value="Mes">Mes</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="tarifaTipoVehiculo">Tipo de vehículo *</label>
                                <select class="form-select" id="tarifaTipoVehiculo" name="tipo_vehiculo_id" required>
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="tarifaValor">Valor *</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="tarifaValor" name="valor" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="tarifaDias">Duración (días) *</label>
                                <input type="number" min="1" max="365" class="form-control" id="tarifaDias" name="dias" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="tarifaActiva">Estado</label>
                                <select class="form-select" id="tarifaActiva" name="activa">
                                    <option value="1">Activa</option>
                                    <option value="0">Inactiva</option>
                                </select>
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
