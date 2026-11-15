<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte {{ \App\Services\ReporteService::TIPOS[$tipo] ?? $tipo }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #0f172a; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .muted { color: #64748b; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: left; }
        th { background: #eef2f9; font-size: 9px; text-transform: uppercase; }
        tr:nth-child(even) td { background: #f8fafc; }
        .footer { margin-top: 12px; }
    </style>
</head>
<body>
    <h1>Reporte de {{ mb_strtolower(\App\Services\ReporteService::TIPOS[$tipo] ?? $tipo) }}</h1>
    <div class="muted">Generado el {{ now()->format('d/m/Y H:i') }} · {{ $filas->count() }} registros</div>

    <table>
        <thead>
            <tr>
                @if($filas->isNotEmpty())
                    @foreach(array_keys((array) $filas->first()) as $columna)
                        <th>{{ ucfirst(str_replace('_', ' ', $columna)) }}</th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($filas as $fila)
                <tr>
                    @foreach((array) $fila as $valor)
                        <td>{{ $valor }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td>Sin datos para el periodo seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer muted">SI Parqueadero · reporte generado automáticamente</div>
</body>
</html>
