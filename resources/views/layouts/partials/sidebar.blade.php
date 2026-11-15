@php($usuario = auth()->user())

<aside class="sidebar">
    <div class="brand">
        <span class="brand-mark"><i class="bi bi-p-square-fill"></i></span>
        <div>
            <div class="brand-name">Parqueadero</div>
            <div class="brand-sub">Sistema de gestión</div>
        </div>
    </div>

    <nav class="nav flex-column gap-1">
        @if($usuario->esSuperAdmin())
            <div class="sidebar-section">Superadministración</div>
            <a class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" href="{{ route('superadmin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('superadmin.tarifas*') ? 'active' : '' }}" href="{{ route('superadmin.tarifas') }}">
                <i class="bi bi-cash-coin"></i> Tarifas
            </a>
            <a class="nav-link {{ request()->routeIs('superadmin.bahias*') ? 'active' : '' }}" href="{{ route('superadmin.bahias') }}">
                <i class="bi bi-grid-3x3-gap"></i> Bahías
            </a>
            <a class="nav-link {{ request()->routeIs('superadmin.reportes*') ? 'active' : '' }}" href="{{ route('superadmin.reportes') }}">
                <i class="bi bi-file-earmark-bar-graph"></i> Reportes
            </a>
            <a class="nav-link {{ request()->routeIs('superadmin.configuracion*') ? 'active' : '' }}" href="{{ route('superadmin.configuracion') }}">
                <i class="bi bi-sliders"></i> Configuración
            </a>
        @elseif($usuario->esAdmin())
            <div class="sidebar-section">Administración</div>
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}" href="{{ route('admin.usuarios') }}">
                <i class="bi bi-people"></i> Usuarios
            </a>
            <a class="nav-link {{ request()->routeIs('admin.clientes*') ? 'active' : '' }}" href="{{ route('admin.clientes') }}">
                <i class="bi bi-person-vcard"></i> Clientes
            </a>
            <a class="nav-link {{ request()->routeIs('admin.vehiculos*') ? 'active' : '' }}" href="{{ route('admin.vehiculos') }}">
                <i class="bi bi-car-front"></i> Vehículos
            </a>
            <a class="nav-link {{ request()->routeIs('admin.contratos*') ? 'active' : '' }}" href="{{ route('admin.contratos') }}">
                <i class="bi bi-file-earmark-text"></i> Contratos
            </a>
        @else
            <div class="sidebar-section">Operación</div>
            <a class="nav-link {{ request()->routeIs('vigilante.dashboard') ? 'active' : '' }}" href="{{ route('vigilante.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('vigilante.entradas*') ? 'active' : '' }}" href="{{ route('vigilante.entradas') }}">
                <i class="bi bi-arrow-left-right"></i> Entradas y salidas
            </a>
            <a class="nav-link {{ request()->routeIs('vigilante.vehiculos*') ? 'active' : '' }}" href="{{ route('vigilante.vehiculos') }}">
                <i class="bi bi-car-front"></i> Vehículos
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="avatar">{{ mb_substr($usuario->nombre, 0, 1) }}</div>
            <div class="min-w-0">
                <div class="user-name text-truncate">{{ $usuario->nombre }}</div>
                <div class="user-role text-truncate">{{ $usuario->email }}</div>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="post" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light w-100">
                <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
            </button>
        </form>
    </div>
</aside>
