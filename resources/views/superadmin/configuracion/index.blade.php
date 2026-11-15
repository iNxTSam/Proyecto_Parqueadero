@extends('layouts.app')

@section('title', 'Configuración')
@section('subtitle', 'Tipos de vehículo y parámetros generales')
@section('page', 'superadmin-configuracion')

@section('content')
    <div class="row g-3 mb-4">
        <x-stat-card label="Capacidad total" :value="$resumen['capacidad_total']" icon="bi-grid-3x3-gap" tone="primary" hint="Cupos en bahías activas" />
        <x-stat-card label="Tarifas activas" :value="$resumen['tarifas_activas']" icon="bi-cash-coin" tone="success" hint="Disponibles para contratos" />
        @foreach($resumen['usuarios_por_rol'] as $fila)
            <div class="col-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="stat-label">{{ $fila->rol?->nombre ?? 'Rol '.$fila->rol_id }}</div>
                        <div class="stat-value">{{ $fila->total }}</div>
                        <div class="stat-hint">usuarios</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge text-bg-light border">{{ $tipos->count() }} tipos de vehículo</span>
        <button class="btn btn-primary" id="btnNuevoTipo" data-bs-toggle="modal" data-bs-target="#modalTipo">
            <i class="bi bi-plus-lg me-1"></i> Nuevo tipo
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th class="text-end">Vehículos</th>
                        <th class="text-end">Bahías</th>
                        <th class="text-end">Tarifas</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tipos as $tipo)
                        <tr>
                            <td class="fw-semibold">{{ $tipo->nombre }}</td>
                            <td class="text-muted">{{ $tipo->descripcion ?? '—' }}</td>
                            <td class="text-end">{{ $tipo->vehiculos_count }}</td>
                            <td class="text-end">{{ $tipo->bahias_count }}</td>
                            <td class="text-end">{{ $tipo->tarifas_count }}</td>
                            <td class="text-end text-nowrap">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalTipo"
                                    data-nombre="{{ $tipo->nombre }}"
                                    data-descripcion="{{ $tipo->descripcion }}"
                                    data-action="{{ route('superadmin.configuracion.tipoVehiculo.update', $tipo) }}"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('superadmin.configuracion.tipoVehiculo.delete', $tipo) }}" method="post" class="d-inline" data-confirm="¿Eliminar el tipo {{ $tipo->nombre }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" @disabled($tipo->vehiculos_count || $tipo->bahias_count || $tipo->tarifas_count)>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalTipo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formTipo" method="post" action="{{ route('superadmin.configuracion.tipoVehiculo.store') }}" data-store-action="{{ route('superadmin.configuracion.tipoVehiculo.store') }}">
                    @csrf
                    <div id="tipoMethod"></div>

                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloTipo">Nuevo tipo de vehículo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="tipoNombre">Nombre *</label>
                            <input type="text" class="form-control" id="tipoNombre" name="nombre" required maxlength="50">
                        </div>

                        <div class="mb-0">
                            <label class="form-label" for="tipoDescripcion">Descripción</label>
                            <textarea class="form-control" id="tipoDescripcion" name="descripcion" rows="2" maxlength="255"></textarea>
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
