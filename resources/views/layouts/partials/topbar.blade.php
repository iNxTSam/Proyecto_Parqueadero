@php($usuario = auth()->user())

<header class="topbar">
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="btn btn-light border d-lg-none" data-sidebar-toggle aria-label="Abrir menú">
            <i class="bi bi-list"></i>
        </button>

        <div>
            <div class="topbar-title">@yield('title', 'Panel')</div>
            <div class="topbar-sub d-none d-sm-block">@yield('subtitle', $usuario?->rol?->nombre)</div>
        </div>
    </div>

    <div class="ms-auto d-flex align-items-center gap-2 gap-md-3">
        <span class="badge rounded-pill text-bg-light border d-none d-md-inline">
            <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('d \d\e F \d\e Y') }}
        </span>
        <span class="badge rounded-pill text-bg-primary">{{ $usuario?->rol?->nombre }}</span>
    </div>
</header>
