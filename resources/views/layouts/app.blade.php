<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Parqueadero') }} · @yield('title', 'Panel')</title>
    @include('layouts.partials.assets')
</head>
<body data-page="@yield('page')">
    <div class="app-shell">
        @include('layouts.partials.sidebar')

        <div class="sidebar-backdrop" data-sidebar-close></div>

        <div class="app-main">
            @include('layouts.partials.topbar')

            <main class="content">
                @include('layouts.partials.flash')
                @yield('content')
            </main>

            <footer class="content-footer d-flex justify-content-between flex-wrap gap-2">
                <span>Sistema de Parqueadero · {{ now()->year }}</span>
                <span>Entradas, contratos y reportes</span>
            </footer>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
