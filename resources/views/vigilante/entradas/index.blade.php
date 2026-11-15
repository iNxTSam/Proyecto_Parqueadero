@extends('layouts.app')

@section('title', 'Entradas y salidas')
@section('subtitle', 'Registro y control de vehículos')
@section('page', 'vigilante-entradas')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form action="{{ route('vigilante.entradas') }}" method="get" class="d-flex gap-2">
            <input type="search" name="buscar" value="{{ $buscar }}" class="form-control w-auto" placeholder="Placa, ticket o documento" style="min-width: 240px">
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </form>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalIngreso">
            <i class="bi bi-plus-lg me-1"></i> Registrar entrada
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Propietario</th>
                        <th>Tipo</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Ticket</th>
                        <th>Estado</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entradas as $entrada)
                        <tr>
                            <td><span class="plate">{{ $entrada->vehiculo_id }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $entrada->cliente?->nombre }}</div>
                                <div class="text-muted small">{{ $entrada->propietario_id }}</div>
                            </td>
                            <td>{{ $entrada->vehiculo?->tipo?->nombre }}</td>
                            <td class="text-nowrap">{{ $entrada->fecha_entrada?->format('d/m/Y H:i') }}</td>
                            <td class="text-nowrap">{{ $entrada->fecha_salida?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td><code>{{ $entrada->ticket }}</code></td>
                            <td>
                                @if($entrada->estaAbierta())
                                    <span class="badge text-bg-success">Dentro</span>
                                @else
                                    <span class="badge text-bg-secondary">Salió</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($entrada->estaAbierta())
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalSalida"
                                        data-action="{{ route('vigilante.entradas.salida', $entrada) }}"
                                        data-placa="{{ $entrada->vehiculo_id }}"
                                        data-propietario="{{ $entrada->cliente?->nombre }}"
                                        data-ticket-requerido="{{ $entrada->cliente?->esAprendiz() ? '1' : '0' }}"
                                    >
                                        <i class="bi bi-box-arrow-right"></i> Marcar salida
                                    </button>
                                @else
                                    <span class="text-muted small">Cerrada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-empty-state icon="bi-arrow-left-right" title="Sin registros" text="Aún no hay entradas registradas." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($entradas->hasPages())
            <div class="card-footer bg-white border-top-0">
                {{ $entradas->links() }}
            </div>
        @endif
    </div>

    {{-- Modal de ingreso --}}
    <div class="modal fade" id="modalIngreso" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formIngreso" method="post" action="{{ route('vigilante.entradas.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-box-arrow-in-right me-1"></i> Registrar entrada</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="ingresoPropietario">Documento del propietario</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="ingresoPropietario" name="propietario_id" placeholder="Ej. 900000001" required>
                                <button type="button" class="btn btn-outline-primary" id="btnVerificarCliente">Verificar</button>
                            </div>
                            <div class="form-text">El cliente debe estar registrado por el administrador.</div>
                        </div>

                        <div id="ingresoMensaje" class="alert d-none" role="alert"></div>

                        <div class="mb-3">
                            <label class="form-label" for="ingresoVehiculo">Vehículo</label>
                            <select class="form-select" id="ingresoVehiculo" name="vehiculo_id" disabled required>
                                <option value="">Primero verifica el cliente</option>
                            </select>
                        </div>

                        <div id="ingresoContrato" class="alert d-none" role="alert"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnRegistrarIngreso" disabled>
                            Registrar ingreso
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal de salida --}}
    <div class="modal fade" id="modalSalida" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formSalida" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-box-arrow-right me-1"></i> Marcar salida</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-2">Vehículo <span class="plate" id="salidaPlaca"></span></p>
                        <p class="text-muted small mb-3" id="salidaPropietario"></p>

                        <div class="mb-3 d-none" id="salidaTicketGroup">
                            <label class="form-label" for="salidaTicket">Ticket de salida</label>
                            <input type="text" class="form-control" id="salidaTicket" name="ticket" placeholder="T-XXXXXX-XXXXX">
                            <div class="form-text">El aprendiz debe presentar el ticket entregado al ingresar.</div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Confirmar salida</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.PQ_RUTAS = {
            verificarCliente: @json(route('vigilante.verificarCliente')),
            verificarContrato: @json(route('vigilante.verificarContrato')),
        };
    </script>
@endpush
