<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Error') · Parqueadero</title>
    <style>
        * { box-sizing: border-box; }
        body {
            align-items: center;
            background: #0b1220;
            color: #e2e8f0;
            display: flex;
            font-family: 'Segoe UI', system-ui, sans-serif;
            justify-content: center;
            margin: 0;
            min-height: 100vh;
            padding: 2rem;
        }
        .card {
            background: #111c33;
            border: 1px solid #1e293b;
            border-radius: 1.2rem;
            max-width: 520px;
            padding: 2.5rem;
            text-align: center;
            width: 100%;
        }
        .code { color: #38bdf8; font-size: 4rem; font-weight: 800; line-height: 1; }
        h1 { font-size: 1.3rem; margin: 1rem 0 .5rem; }
        p { color: #94a3b8; font-size: .92rem; line-height: 1.6; margin: 0 0 1.5rem; }
        a {
            background: #1d4ed8;
            border-radius: .6rem;
            color: #fff;
            display: inline-block;
            font-weight: 600;
            padding: .7rem 1.2rem;
            text-decoration: none;
        }
        a:hover { background: #1740b6; }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <a href="{{ url('/') }}">Volver al inicio</a>
    </div>
</body>
</html>
