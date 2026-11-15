@extends('layouts.app')

@section('title', 'Contratos')
@section('subtitle', 'Planes de parqueadero por vehículo')
@section('page', 'admin-contratos')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form action="{{ route('admin.contratos') }}" method="get" class="d-flex flex-wrap gap-2">
            <input type="search" name="buscar" value="{{ $buscar }}" class="form-control w-auto" placeholder="Placa o propietario" style="min-width: 220px">
            <select name="estado" class="form-select w-auto" style="min-width: 170px">
                <option value="">Todos los estados</option>
                @foreach($estados as $item)
                    <option value="{{ $item->id }}" @selected((int) $estado === (int) $item->id)>{{ $item->nombre }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-funnel"></i></button>
        </form>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalContrato">
            <i class="bi bi-plus-lg me-1"></i> Nuevo contrato
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vehículo</th>
                        <th>Propietario</th>
                        <th>Tarifa</th>
                        <th>Vigencia</th>
                        <th class="text-end">Valor</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contratos as $contrato)
                        <tr>
                            <td class="text-muted">#{{ $contrato->id }}</td>
                            <td><span class="plate">{{ $contrato->vehiculo_id }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $contrato->propietario?->nombre }}</div>
                                <div class="text-muted small">{{ $contrato->propietario_id }}</div>
                            </td>
                            <td>{{ $contrato->tarifa?->nombre }}</td>
                            <td class="text-nowrap">
                                {{ $contrato->fecha_inicio?->format('d/m/Y') }}
                                <i class="bi bi-arrow-right text-muted mx-1"></i>
                                {{ $contrato->fecha_fin?->format('d/m/Y') }}
                            </td>
                            <td class="text-end">${{ number_format((float) $contrato->valor, 0, ',', '.') }}</td>
                            <td>
                                @php($estadoNombre = $contrato->estado?->nombre)
                                <span class="badge {{ $estadoNombre === 'Activo' ? 'text-bg-success' : ($estadoNombre === 'Cancelado' ? 'text-bg-danger' : 'text-bg-secondary') }}">
                                    {{ $estadoNombre }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditarContrato"
                                    data-action="{{ route('admin.contratos.update', $contrato) }}"
                                    data-valor="{{ $contrato->valor }}"
                                    data-estado="{{ $contrato->estado_id }}"
                                    data-observaciones="{{ $contrato->observaciones }}"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalRenovarContrato"
                                    data-action="{{ route('admin.contratos.renovar', $contrato) }}"
                                    data-placa="{{ $contrato->vehiculo_id }}"
                                    data-tipo="{{ $contrato->vehiculo?->tipo_vehiculo_id }}"
                                >
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-empty-state icon="bi-file-earmark-text" title="Sin contratos" text="Crea un contrato para un vehículo registrado." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contratos->hasPages())
            <div class="card-footer bg-white border-top-0">
                {{ $contratos->links() }}
            </div>
        @endif
    </div>

    {{-- Nuevo contrato --}}
    <div class="modal fade" id="modalContrato" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.contratos.store') }}" id="formContrato">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-file-earmark-plus me-1"></i> Nuevo contrato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="contratoVehiculo">Vehículo *</label>
                            <select class="form-select" id="contratoVehiculo" name="vehiculo_id" required>
                                <option value="">Seleccione un vehículo</option>
                                @foreach($vehiculos as $vehiculo)
                                    <option value="{{ $vehiculo->placa }}" data-tipo="{{ $vehiculo->tipo_vehiculo_id }}">
                                        {{ $vehiculo->placa }} · {{ $vehiculo->propietario?->nombre }} ({{ $vehiculo->tipo?->nombre }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="contratoTarifa">Tarifa *</label>
                            <select class="form-select" id="contratoTarifa" name="tarifa_id" required>
                                <option value="">Seleccione una tarifa</option>
                                @foreach($tarifas as $tarifa)
                                    <option value="{{ $tarifa->id }}" data-tipo="{{ $tarifa->tipo_vehiculo_id }}" data-dias="{{ $tarifa->dias }}">
                                        {{ $tarifa->nombre }} · ${{ number_format((float) $tarifa->valor, 0, ',', '.') }} · {{ $tarifa->dias }} días
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text" id="contratoAyuda">Las tarifas se filtran según el tipo de vehículo.</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label" for="contratoObservaciones">Observaciones</label>
                            <textarea class="form-control" id="contratoObservaciones" name="observaciones" rows="2" maxlength="1000"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear contrato</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Editar contrato --}}
    <div class="modal fade" id="modalEditarContrato" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" id="formEditarContrato">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil me-1"></i> Editar contrato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="editarValor">Valor *</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="editarValor" name="valor" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="editarEstado">Estado *</label>
                            <select class="form-select" id="editarEstado" name="estado_id" required>
                                @foreach($estados as $item)
                                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label" for="editarObservaciones">Observaciones</label>
                            <textarea class="form-control" id="editarObservaciones" name="observaciones" rows="2" maxlength="1000"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Renovar contrato --}}
    <div class="modal fade" id="modalRenovarContrato" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" id="formRenovarContrato">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-arrow-repeat me-1"></i> Renovar contrato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-2">Vehículo <span class="plate" id="renovarPlaca"></span></p>
                        <p class="text-muted small">El contrato actual se finaliza y se crea uno nuevo con la tarifa seleccionada.</p>

                        <label class="form-label" for="renovarTarifa">Nueva tarifa *</label>
                        <select class="form-select" id="renovarTarifa" name="tarifa_id" required>
                            <option value="">Seleccione una tarifa</option>
                            @foreach($tarifas as $tarifa)
                                <option value="{{ $tarifa->id }}" data-tipo="{{ $tarifa->tipo_vehiculo_id }}">
                                    {{ $tarifa->nombre }} · ${{ number_format((float) $tarifa->valor, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Renovar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
