@extends('layouts.app')

@section('title', 'Usuarios')
@section('subtitle', 'Administradores y vigilantes')
@section('page', 'admin-usuarios')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form action="{{ route('admin.usuarios') }}" method="get" class="d-flex gap-2">
            <input type="search" name="buscar" value="{{ $buscar }}" class="form-control w-auto" placeholder="Nombre, cédula o correo" style="min-width: 260px">
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </form>

        <button class="btn btn-primary" id="btnNuevoUsuario" data-bs-toggle="modal" data-bs-target="#modalUsuario">
            <i class="bi bi-plus-lg me-1"></i> Nuevo usuario
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Rol</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar-sm">{{ mb_substr($usuario->nombre, 0, 1) }}</span>
                                    <div>
                                        <div class="fw-semibold">{{ $usuario->nombre }}</div>
                                        <div class="text-muted small">{{ $usuario->email ?? 'Sin correo' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $usuario->cedula }}</td>
                            <td><span class="badge text-bg-light border">{{ $usuario->rol?->nombre }}</span></td>
                            <td>{{ $usuario->contacto ?? '—' }}</td>
                            <td>
                                @if($usuario->activo)
                                    <span class="badge text-bg-success">Activo</span>
                                @else
                                    <span class="badge text-bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalUsuario"
                                    data-id="{{ $usuario->id }}"
                                    data-nombre="{{ $usuario->nombre }}"
                                    data-cedula="{{ $usuario->cedula }}"
                                    data-carnet="{{ $usuario->carnet }}"
                                    data-email="{{ $usuario->email }}"
                                    data-contacto="{{ $usuario->contacto }}"
                                    data-rol="{{ $usuario->rol_id }}"
                                    data-activo="{{ $usuario->activo ? '1' : '0' }}"
                                    data-action="{{ route('admin.usuarios.update', $usuario) }}"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('admin.usuarios.toggle', $usuario) }}" method="post" class="d-inline" data-confirm="¿Cambiar el estado de {{ $usuario->nombre }}?">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $usuario->activo ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                        <i class="bi {{ $usuario->activo ? 'bi-person-x' : 'bi-person-check' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="bi-people" title="Sin usuarios" text="Crea el primer administrador o vigilante." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($usuarios->hasPages())
            <div class="card-footer bg-white border-top-0">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formUsuario" method="post" action="{{ route('admin.usuarios.store') }}" data-store-action="{{ route('admin.usuarios.store') }}">
                    @csrf
                    <div id="usuarioMethod"></div>

                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloUsuario">Nuevo usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="usuarioNombre">Nombre completo *</label>
                                <input type="text" class="form-control" id="usuarioNombre" name="nombre" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="usuarioCedula">Documento *</label>
                                <input type="text" class="form-control" id="usuarioCedula" name="cedula" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="usuarioCarnet">Carnet</label>
                                <input type="text" class="form-control" id="usuarioCarnet" name="carnet">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="usuarioEmail">Correo</label>
                                <input type="email" class="form-control" id="usuarioEmail" name="email">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="usuarioContacto">Contacto</label>
                                <input type="text" class="form-control" id="usuarioContacto" name="contacto">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="usuarioRol">Rol *</label>
                                <select class="form-select" id="usuarioRol" name="rol_id" required>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="usuarioActivo">Estado</label>
                                <select class="form-select" id="usuarioActivo" name="activo">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="usuarioPassword">Contraseña <span id="passwordHint" class="text-muted fw-normal"></span></label>
                                <input type="password" class="form-control" id="usuarioPassword" name="password" autocomplete="new-password">
                                <div class="form-text">Mínimo 8 caracteres.</div>
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
