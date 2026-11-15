@extends('layouts.app')

@section('title', 'Vehículos')
@section('subtitle', 'Vehículos registrados por cliente')
@section('page', 'admin-vehiculos')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form action="{{ route('admin.vehiculos') }}" method="get" class="d-flex gap-2">
            <input type="search" name="buscar" value="{{ $buscar }}" class="form-control w-auto" placeholder="Placa, documento o propietario" style="min-width: 260px">
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </form>

        <button class="btn btn-primary" id="btnNuevoVehiculo" data-bs-toggle="modal" data-bs-target="#modalVehiculo">
            <i class="bi bi-plus-lg me-1"></i> Nuevo vehículo
        </button>
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
                        <th class="text-end">Acciones</th>
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
                                @unless($vehiculo->activo)
                                    <span class="badge text-bg-danger">Inactivo</span>
                                @endunless
                            </td>
                            <td class="text-end">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalVehiculo"
                                    data-placa="{{ $vehiculo->placa }}"
                                    data-tipo="{{ $vehiculo->tipo_vehiculo_id }}"
                                    data-propietario="{{ $vehiculo->propietario_id }}"
                                    data-color="{{ $vehiculo->color }}"
                                    data-marca="{{ $vehiculo->marca }}"
                                    data-modelo="{{ $vehiculo->modelo }}"
                                    data-activo="{{ $vehiculo->activo ? '1' : '0' }}"
                                    data-action="{{ route('admin.vehiculos.update', $vehiculo) }}"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state icon="bi-car-front" title="Sin vehículos" text="Registra un vehículo y asígnalo a un cliente." />
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

    <div class="modal fade" id="modalVehiculo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formVehiculo" method="post" action="{{ route('admin.vehiculos.store') }}" data-store-action="{{ route('admin.vehiculos.store') }}">
                    @csrf
                    <div id="vehiculoMethod"></div>

                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloVehiculo">Nuevo vehículo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="vehiculoPlaca">Placa *</label>
                                <input type="text" class="form-control text-uppercase" id="vehiculoPlaca" name="placa" required maxlength="10">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="vehiculoTipo">Tipo *</label>
                                <select class="form-select" id="vehiculoTipo" name="tipo_vehiculo_id" required>
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="vehiculoPropietario">Propietario *</label>
                                <select class="form-select" id="vehiculoPropietario" name="propietario_id" required>
                                    <option value="">Seleccione un cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }} · {{ $cliente->id }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="vehiculoColor">Color</label>
                                <input type="text" class="form-control" id="vehiculoColor" name="color">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="vehiculoMarca">Marca</label>
                                <input type="text" class="form-control" id="vehiculoMarca" name="marca">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="vehiculoModelo">Modelo</label>
                                <input type="text" class="form-control" id="vehiculoModelo" name="modelo">
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="vehiculoActivo">Estado</label>
                                <select class="form-select" id="vehiculoActivo" name="activo">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
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
