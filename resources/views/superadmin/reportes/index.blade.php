@extends('layouts.app')

@section('title', 'Reportes')
@section('subtitle', 'Exportación de información en CSV y PDF')
@section('page', 'superadmin-reportes')

@section('content')
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><i class="bi bi-file-earmark-bar-graph me-1"></i> Generar reporte</div>
                <div class="card-body">
                    <form action="{{ route('superadmin.reportes.generar') }}" method="post">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="tipoReporte">Tipo de reporte *</label>
                            <select class="form-select" id="tipoReporte" name="tipo_reporte" required>
                                @foreach($tipos as $clave => $nombre)
                                    <option value="{{ $clave }}">{{ $nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="formatoReporte">Formato *</label>
                            <select class="form-select" id="formatoReporte" name="formato" required>
                                <option value="csv">CSV (Excel)</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label" for="fechaInicio">Desde</label>
                                <input type="date" class="form-control" id="fechaInicio" name="fecha_inicio">
                            </div>

                            <div class="col-6">
                                <label class="form-label" for="fechaFin">Hasta</label>
                                <input type="date" class="form-control" id="fechaFin" name="fecha_fin">
                            </div>
                        </div>

                        <div class="form-text mt-2">
                            Las fechas aplican a ingresos y contratos. Ocupación y usuarios se exportan completos.
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-download me-1"></i> Descargar reporte
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-clock-history me-1"></i> Reportes recientes</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Reporte</th>
                                <th>Formato</th>
                                <th>Generado por</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recientes as $reporte)
                                <tr>
                                    <td class="fw-semibold">{{ $reporte->nombre }}</td>
                                    <td><span class="badge text-bg-light border">{{ $reporte->formato }}</span></td>
                                    <td>{{ $reporte->generadoPor?->nombre }}</td>
                                    <td>{{ $reporte->created_at?->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <x-empty-state icon="bi-file-earmark-bar-graph" title="Sin reportes" text="Genera tu primer reporte con el formulario." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
