<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\EntradaSalida;
use App\Models\Vehiculo;
use App\Services\ParqueoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VigilanteController extends Controller
{
    public function __construct(private readonly ParqueoService $parqueo) {}

    public function dashboard(): View
    {
        $ocupacion = $this->parqueo->ocupacionPorTipo();

        $stats = [
            'capacidad' => $ocupacion->sum('capacidad'),
            'ocupados' => $ocupacion->sum('ocupados'),
            'libres' => $ocupacion->sum('libres'),
            'entradas_hoy' => EntradaSalida::whereDate('fecha_entrada', today())->count(),
            'salidas_hoy' => EntradaSalida::whereDate('fecha_salida', today())->count(),
        ];

        $dentro = EntradaSalida::with(['cliente', 'vehiculo.tipo'])
            ->abiertas()
            ->orderByDesc('fecha_entrada')
            ->limit(8)
            ->get();

        return view('vigilante.dashboard', compact('ocupacion', 'stats', 'dentro'));
    }

    public function entradasSalidas(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar'));

        $query = EntradaSalida::with(['cliente', 'vehiculo.tipo'])->orderByDesc('fecha_entrada');

        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('vehiculo_id', 'like', "%{$buscar}%")
                    ->orWhere('ticket', 'like', "%{$buscar}%")
                    ->orWhereHas('cliente', function ($c) use ($buscar) {
                        $c->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('id', 'like', "%{$buscar}%");
                    });
            });
        }

        $entradas = $query->paginate(15)->withQueryString();

        return view('vigilante.entradas.index', compact('entradas', 'buscar'));
    }

    public function verificarCliente(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'propietario_id' => ['required', 'string', 'max:15'],
        ]);

        $cliente = Cliente::with(['vehiculos' => fn ($q) => $q->where('activo', true), 'vehiculos.tipo'])
            ->find($datos['propietario_id']);

        if (! $cliente) {
            return response()->json(['existe' => false, 'mensaje' => 'Cliente no encontrado.']);
        }

        return response()->json([
            'existe' => true,
            'cliente' => [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'activo' => $cliente->activo,
                'rol_id' => $cliente->rol_id,
            ],
            'vehiculos' => $cliente->vehiculos->map(fn ($vehiculo) => [
                'placa' => $vehiculo->placa,
                'tipo' => $vehiculo->tipo?->nombre,
                'etiqueta' => $vehiculo->placa.' · '.$vehiculo->tipo->nombre,
            ])->values(),
        ]);
    }

    public function verificarContrato(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'vehiculo_id' => ['required', 'string', 'max:10', 'exists:vehiculos,placa'],
        ]);

        $existe = Contrato::where('vehiculo_id', $datos['vehiculo_id'])->vigentes()->exists();

        return response()->json(['existe' => $existe]);
    }

    public function registrarIngreso(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'propietario_id' => ['required', 'string', 'max:15', 'exists:clientes,id'],
            'vehiculo_id' => ['required', 'string', 'max:10', 'exists:vehiculos,placa'],
        ]);

        $entrada = $this->parqueo->registrarIngreso(
            Cliente::findOrFail($datos['propietario_id']),
            Vehiculo::findOrFail($datos['vehiculo_id']),
            $request->user()
        );

        return redirect()
            ->route('vigilante.entradas')
            ->with('success', 'Ingreso registrado. Ticket: '.$entrada->ticket);
    }

    public function registrarSalida(Request $request, EntradaSalida $entrada): RedirectResponse
    {
        $datos = $request->validate([
            'ticket' => ['nullable', 'string', 'max:20'],
        ]);

        $this->parqueo->registrarSalida($entrada, $datos['ticket'] ?? null, $request->user());

        return redirect()
            ->route('vigilante.entradas')
            ->with('success', 'Salida registrada correctamente.');
    }

    public function vehiculos(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar'));

        $query = Vehiculo::with(['tipo', 'propietario'])->orderBy('placa');

        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('placa', 'like', "%{$buscar}%")
                    ->orWhere('propietario_id', 'like', "%{$buscar}%")
                    ->orWhereHas('propietario', fn ($p) => $p->where('nombre', 'like', "%{$buscar}%"));
            });
        }

        $vehiculos = $query->paginate(15)->withQueryString();

        return view('vigilante.vehiculos.index', compact('vehiculos', 'buscar'));
    }
}
