<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Parqueadero') }} · Iniciar sesión</title>
    @include('layouts.partials.assets')
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-brand">
                <span class="auth-brand-mark"><i class="bi bi-p-square-fill"></i></span>
                <div>
                    <div class="auth-brand-name">Sistema de Parqueadero</div>
                    <div class="auth-brand-sub">Sistema de gestión</div>
                </div>
            </div>

            <h1 class="h6 fw-semibold mb-1">Iniciar sesión</h1>
            <p class="text-muted small mb-3">Ingresa con tu número de documento y contraseña.</p>

            @if($errors->any())
                <div class="alert alert-danger py-2 small" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="post" novalidate>
                @csrf

                <div class="mb-2">
                    <label for="numeroDocumento" class="form-label">Número de documento</label>
                    <input
                        type="text"
                        class="form-control @error('numeroDocumento') is-invalid @enderror"
                        id="numeroDocumento"
                        name="numeroDocumento"
                        value="{{ old('numeroDocumento') }}"
                        placeholder="Ej. 1000000001"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-2">
                    <label for="clave" class="form-label">Contraseña</label>
                    <input
                        type="password"
                        class="form-control @error('clave') is-invalid @enderror"
                        id="clave"
                        name="clave"
                        placeholder="Contraseña"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                    <label class="form-check-label small" for="remember">Recordarme</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>

            <div class="demo-box">
                <div class="fw-semibold text-dark mb-1">Usuarios de demostración</div>
                <table>
                    <tr><td>Superadministrador</td><td><strong>1000000001</strong></td></tr>
                    <tr><td>Administrador</td><td><strong>1000000002</strong></td></tr>
                    <tr><td>Vigilante</td><td><strong>1000000003</strong></td></tr>
                </table>
                <div class="mt-1">Contraseña: <strong>Demo12345!</strong></div>
            </div>

            <div class="auth-footer">
                © {{ now()->year }} Sistema de Parqueadero
            </div>
        </div>
    </div>
</body>
</html>
