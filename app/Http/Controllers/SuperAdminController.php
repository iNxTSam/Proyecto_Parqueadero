<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessRuleException;
use App\Http\Requests\ReporteRequest;
use App\Http\Requests\StoreBahiaRequest;
use App\Http\Requests\StoreTarifaRequest;
use App\Http\Requests\TipoVehiculoRequest;
use App\Http\Requests\UpdateBahiaRequest;
use App\Http\Requests\UpdateTarifaRequest;
use App\Models\Bahia;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Recibo;
use App\Models\Reporte;
use App\Models\Tarifa;
use App\Models\TipoVehiculo;
use App\Models\Usuario;
use App\Models\Vehiculo;
use App\Services\ParqueoService;
use App\Services\ReporteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuperAdminController extends Controller
{
    public function __construct(
        private readonly ParqueoService $parqueo,
        private readonly ReporteService $reportes
    ) {}

    public function dashboard(): View
    {
        $stats = [
            'usuarios' => Usuario::count(),
            'clientes' => Cliente::count(),
            'vehiculos' => Vehiculo::count(),
            'contratos_activos' => Contrato::vigentes()->count(),
            'facturado_mes' => Recibo::whereMonth('fecha_emision', today()->month)
                ->whereYear('fecha_emision', today()->year)
                ->sum('valor'),
            'pendiente' => Recibo::where('estado', 'pendiente')->sum('valor'),
        ];

        $ocupacion = $this->parqueo->ocupacionPorTipo();
        $reportes = Reporte::with('generadoPor')->latest()->limit(5)->get();

        return view('superadmin.dashboard', compact('stats', 'ocupacion', 'reportes'));
    }

    public function tarifas(): View
    {
        $tarifas = Tarifa::with('tipoVehiculo')->orderBy('nombre')->paginate(15);
        $tipos = TipoVehiculo::orderBy('nombre')->get();

        return view('superadmin.tarifas.index', compact('tarifas', 'tipos'));
    }

    public function storeTarifa(StoreTarifaRequest $request): RedirectResponse
    {
        Tarifa::create($request->validated() + ['activa' => $request->boolean('activa', true)]);

        return redirect()->route('superadmin.tarifas')->with('success', 'Tarifa creada correctamente.');
    }

    public function updateTarifa(UpdateTarifaRequest $request, Tarifa $tarifa): RedirectResponse
    {
        $tarifa->update($request->validated() + ['activa' => $request->boolean('activa')]);

        return redirect()->route('superadmin.tarifas')->with('success', 'Tarifa actualizada correctamente.');
    }

    public function deleteTarifa(Tarifa $tarifa): RedirectResponse
    {
        if (Contrato::where('tarifa_id', $tarifa->id)->exists()) {
            throw new BusinessRuleException('No puedes eliminar una tarifa que ya tiene contratos. Desactívala en su lugar.');
        }

        $tarifa->delete();

        return redirect()->route('superadmin.tarifas')->with('success', 'Tarifa eliminada correctamente.');
    }

    public function bahias(): View
    {
        $bahias = Bahia::with('tipoVehiculo')->orderBy('numero')->paginate(15);
        $tipos = TipoVehiculo::orderBy('nombre')->get();

        $ocupados = Vehiculo::where('estado_parqueo', true)
            ->selectRaw('tipo_vehiculo_id, COUNT(*) as total')
            ->groupBy('tipo_vehiculo_id')
            ->pluck('total', 'tipo_vehiculo_id');

        return view('superadmin.bahias.index', compact('bahias', 'tipos', 'ocupados'));
    }

    public function storeBahia(StoreBahiaRequest $request): RedirectResponse
    {
        Bahia::create($request->validated() + ['activa' => $request->boolean('activa', true)]);

        return redirect()->route('superadmin.bahias')->with('success', 'Bahía creada correctamente.');
    }

    public function updateBahia(UpdateBahiaRequest $request, Bahia $bahia): RedirectResponse
    {
        $bahia->update($request->validated() + ['activa' => $request->boolean('activa')]);

        return redirect()->route('superadmin.bahias')->with('success', 'Bahía actualizada correctamente.');
    }

    public function deleteBahia(Bahia $bahia): RedirectResponse
    {
        $hayParqueados = Vehiculo::where('tipo_vehiculo_id', $bahia->tipo_vehiculo_id)
            ->where('estado_parqueo', true)
            ->exists();

        if ($hayParqueados) {
            throw new BusinessRuleException('No puedes eliminar una bahía con vehículos dentro. Espera a que salgan o desactívala.');
        }

        $bahia->delete();

        return redirect()->route('superadmin.bahias')->with('success', 'Bahía eliminada correctamente.');
    }

    public function reportes(): View
    {
        return view('superadmin.reportes.index', [
            'tipos' => ReporteService::TIPOS,
            'recientes' => Reporte::with('generadoPor')->latest()->limit(8)->get(),
        ]);
    }

    public function generarReporte(ReporteRequest $request): StreamedResponse|Response
    {
        $datos = $request->validated();
        $tipo = $datos['tipo_reporte'];
        $formato = $datos['formato'];
        $desde = $datos['fecha_inicio'] ?? null;
        $hasta = $datos['fecha_fin'] ?? null;

        $filas = $this->reportes->datos($tipo, $desde, $hasta);
        $nombre = 'reporte_'.$tipo.'_'.now()->format('Ymd_His').'.'.$formato;

        Reporte::create([
            'nombre' => 'Reporte de '.mb_strtolower(ReporteService::TIPOS[$tipo]),
            'tipo' => $tipo,
            'formato' => strtoupper($formato),
            'parametros' => ['fecha_inicio' => $desde, 'fecha_fin' => $hasta, 'filas' => $filas->count()],
            'generado_por_id' => Auth::id(),
        ]);

        if ($formato === 'csv') {
            return $this->exportarCsv($filas, $tipo, $nombre);
        }

        return Pdf::loadView('superadmin.reportes.pdf', ['filas' => $filas, 'tipo' => $tipo])
            ->download($nombre);
    }

    public function configuracion(): View
    {
        $tipos = TipoVehiculo::withCount(['vehiculos', 'bahias', 'tarifas'])->orderBy('nombre')->get();

        $resumen = [
            'capacidad_total' => Bahia::where('activa', true)->sum('capacidad_maxima'),
            'tarifas_activas' => Tarifa::where('activa', true)->count(),
            'usuarios_por_rol' => Usuario::selectRaw('rol_id, COUNT(*) as total')
                ->groupBy('rol_id')
                ->with('rol')
                ->get(),
        ];

        return view('superadmin.configuracion.index', compact('tipos', 'resumen'));
    }

    public function storeTipoVehiculo(TipoVehiculoRequest $request): RedirectResponse
    {
        TipoVehiculo::create($request->validated());

        return redirect()->route('superadmin.configuracion')->with('success', 'Tipo de vehículo creado correctamente.');
    }

    public function updateTipoVehiculo(TipoVehiculoRequest $request, TipoVehiculo $tipoVehiculo): RedirectResponse
    {
        $tipoVehiculo->update($request->validated());

        return redirect()->route('superadmin.configuracion')->with('success', 'Tipo de vehículo actualizado correctamente.');
    }

    public function deleteTipoVehiculo(TipoVehiculo $tipoVehiculo): RedirectResponse
    {
        $enUso = $tipoVehiculo->vehiculos()->exists()
            || $tipoVehiculo->bahias()->exists()
            || $tipoVehiculo->tarifas()->exists();

        if ($enUso) {
            throw new BusinessRuleException('No puedes eliminar un tipo de vehículo que tiene vehículos, bahías o tarifas asociadas.');
        }

        $tipoVehiculo->delete();

        return redirect()->route('superadmin.configuracion')->with('success', 'Tipo de vehículo eliminado correctamente.');
    }

    /**
     * @param  Collection<int, \stdClass>  $filas
     */
    private function exportarCsv(Collection $filas, string $tipo, string $nombre): StreamedResponse
    {
        $encabezados = $this->reportes->encabezados($tipo);

        return response()->streamDownload(function () use ($filas, $encabezados) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, $encabezados);

            foreach ($filas as $fila) {
                fputcsv($salida, array_values((array) $fila));
            }

            fclose($salida);
        }, $nombre, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
