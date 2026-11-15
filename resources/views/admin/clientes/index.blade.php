@extends('layouts.app')

@section('title', 'Clientes')
@section('subtitle', 'Propietarios de vehículos')
@section('page', 'admin-clientes')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form action="{{ route('admin.clientes') }}" method="get" class="d-flex gap-2">
            <input type="search" name="buscar" value="{{ $buscar }}" class="form-control w-auto" placeholder="Documento, nombre o correo" style="min-width: 260px">
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </form>

        <button class="btn btn-primary" id="btnNuevoCliente" data-bs-toggle="modal" data-bs-target="#modalCliente">
            <i class="bi bi-plus-lg me-1"></i> Nuevo cliente
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->id }}</td>
                            <td class="fw-semibold">{{ $cliente->nombre }}</td>
                            <td><span class="badge text-bg-light border">{{ $cliente->rol?->nombre }}</span></td>
                            <td>
                                <div>{{ $cliente->telefono ?? '—' }}</div>
                                <div class="text-muted small">{{ $cliente->correo ?? 'Sin correo' }}</div>
                            </td>
                            <td>
                                @if($cliente->activo)
                                    <span class="badge text-bg-success">Activo</span>
                                @else
                                    <span class="badge text-bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalCliente"
                                    data-id="{{ $cliente->id }}"
                                    data-nombre="{{ $cliente->nombre }}"
                                    data-telefono="{{ $cliente->telefono }}"
                                    data-correo="{{ $cliente->correo }}"
                                    data-rol="{{ $cliente->rol_id }}"
                                    data-activo="{{ $cliente->activo ? '1' : '0' }}"
                                    data-action="{{ route('admin.clientes.update', $cliente) }}"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="bi-person-vcard" title="Sin clientes" text="Registra el primer cliente para asignarle vehículos." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clientes->hasPages())
            <div class="card-footer bg-white border-top-0">
                {{ $clientes->links() }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formCliente" method="post" action="{{ route('admin.clientes.store') }}" data-store-action="{{ route('admin.clientes.store') }}">
                    @csrf
                    <div id="clienteMethod"></div>

                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloCliente">Nuevo cliente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="clienteId">Documento *</label>
                                <input type="text" class="form-control" id="clienteId" name="id" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clienteRol">Tipo de cliente *</label>
                                <select class="form-select" id="clienteRol" name="rol_id" required>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="clienteNombre">Nombre completo *</label>
                                <input type="text" class="form-control" id="clienteNombre" name="nombre" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clienteTelefono">Teléfono</label>
                                <input type="text" class="form-control" id="clienteTelefono" name="telefono">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clienteCorreo">Correo</label>
                                <input type="email" class="form-control" id="clienteCorreo" name="correo">
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="clienteActivo">Estado</label>
                                <select class="form-select" id="clienteActivo" name="activo">
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
