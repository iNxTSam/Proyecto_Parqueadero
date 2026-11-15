<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessRuleException;
use App\Http\Requests\RenovarContratoRequest;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\StoreContratoRequest;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\StoreVehiculoRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Http\Requests\UpdateContratoRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Requests\UpdateVehiculoRequest;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\EstadoContrato;
use App\Models\Rol;
use App\Models\RolCliente;
use App\Models\Tarifa;
use App\Models\TipoVehiculo;
use App\Models\Usuario;
use App\Models\Vehiculo;
use App\Services\ContratoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(private readonly ContratoService $contratos) {}

    public function dashboard(): View
    {
        $this->contratos->marcarVencidos();

        $stats = [
            'clientes' => Cliente::count(),
            'vehiculos' => Vehiculo::count(),
            'contratos_activos' => Contrato::vigentes()->count(),
            'por_vencer' => Contrato::vigentes()->whereDate('fecha_fin', '<=', today()->addDays(7))->count(),
        ];

        $proximos = Contrato::with(['vehiculo', 'propietario', 'tarifa'])
            ->vigentes()
            ->whereDate('fecha_fin', '<=', today()->addDays(7))
            ->orderBy('fecha_fin')
            ->limit(8)
            ->get();

        $recientes = Contrato::with(['vehiculo', 'propietario', 'estado'])
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'proximos', 'recientes'));
    }

    public function usuarios(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar'));

        $query = Usuario::with('rol')->whereIn('rol_id', [Usuario::ADMIN, Usuario::VIGILANTE])->orderBy('nombre');

        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('cedula', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%")
                    ->orWhere('carnet', 'like', "%{$buscar}%");
            });
        }

        $usuarios = $query->paginate(12)->withQueryString();
        $roles = Rol::whereIn('id', [Usuario::ADMIN, Usuario::VIGILANTE])->orderBy('id')->get();

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'buscar'));
    }

    public function storeUsuario(StoreUsuarioRequest $request): RedirectResponse
    {
        Usuario::create($request->validated() + ['activo' => $request->boolean('activo', true)]);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario registrado correctamente.');
    }

    public function updateUsuario(UpdateUsuarioRequest $request, Usuario $usuario): RedirectResponse
    {
        $this->protegerSuperadmin($usuario);

        $datos = $request->validated();
        $password = $datos['password'] ?? null;
        unset($datos['password']);

        if ($usuario->id === Auth::id()) {
            if ((int) $datos['rol_id'] !== (int) $usuario->rol_id) {
                throw new BusinessRuleException('No puedes cambiar tu propio rol.');
            }

            if (! $request->boolean('activo')) {
                throw new BusinessRuleException('No puedes desactivar tu propia cuenta.');
            }
        }

        $usuario->fill($datos);

        if (! empty($password)) {
            $usuario->password = $password;
        }

        $usuario->activo = $request->has('activo') ? $request->boolean('activo') : $usuario->activo;
        $usuario->save();

        return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleUsuario(Usuario $usuario): RedirectResponse
    {
        $this->protegerSuperadmin($usuario);

        if ($usuario->id === Auth::id()) {
            throw new BusinessRuleException('No puedes desactivar tu propia cuenta.');
        }

        $usuario->update(['activo' => ! $usuario->activo]);

        return redirect()->route('admin.usuarios')->with('success', 'Estado del usuario actualizado.');
    }

    public function contratos(Request $request): View
    {
        $this->contratos->marcarVencidos();

        $buscar = trim((string) $request->input('buscar'));
        $estado = $request->integer('estado');

        $query = Contrato::with(['vehiculo', 'propietario', 'tarifa', 'estado'])->latest();

        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('vehiculo_id', 'like', "%{$buscar}%")
                    ->orWhereHas('propietario', fn ($p) => $p->where('nombre', 'like', "%{$buscar}%")->orWhere('id', 'like', "%{$buscar}%"));
            });
        }

        if ($estado > 0) {
            $query->where('estado_id', $estado);
        }

        $contratos = $query->paginate(12)->withQueryString();
        $estados = EstadoContrato::orderBy('id')->get();
        $vehiculos = Vehiculo::with(['propietario', 'tipo'])->where('activo', true)->orderBy('placa')->get();
        $tarifas = Tarifa::with('tipoVehiculo')->where('activa', true)->orderBy('nombre')->get();

        return view('admin.contratos.index', compact('contratos', 'estados', 'vehiculos', 'tarifas', 'buscar', 'estado'));
    }

    public function storeContrato(StoreContratoRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $this->contratos->crear(
            Vehiculo::findOrFail($datos['vehiculo_id']),
            Tarifa::findOrFail($datos['tarifa_id']),
            $request->user(),
            $datos['observaciones'] ?? null
        );

        return redirect()->route('admin.contratos')->with('success', 'Contrato creado correctamente.');
    }

    public function updateContrato(UpdateContratoRequest $request, Contrato $contrato): RedirectResponse
    {
        $this->contratos->actualizar($contrato, $request->validated());

        return redirect()->route('admin.contratos')->with('success', 'Contrato actualizado correctamente.');
    }

    public function renovarContrato(RenovarContratoRequest $request, Contrato $contrato): RedirectResponse
    {
        $this->contratos->renovar(
            $contrato,
            Tarifa::findOrFail($request->integer('tarifa_id')),
            $request->user()
        );

        return redirect()->route('admin.contratos')->with('success', 'Contrato renovado correctamente.');
    }

    public function verificarUsuario(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'propietario_id' => ['required', 'string', 'max:15'],
        ]);

        $cliente = Cliente::with(['vehiculos' => fn ($q) => $q->where('activo', true), 'vehiculos.tipo'])
            ->find($datos['propietario_id']);

        if (! $cliente) {
            return response()->json(['existe' => false]);
        }

        return response()->json([
            'existe' => true,
            'cliente' => ['id' => $cliente->id, 'nombre' => $cliente->nombre],
            'vehiculos' => $cliente->vehiculos->map(fn ($vehiculo) => [
                'placa' => $vehiculo->placa,
                'etiqueta' => $vehiculo->placa.' · '.$vehiculo->tipo->nombre,
            ])->values(),
        ]);
    }

    public function clientes(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar'));

        $query = Cliente::with('rol')->orderBy('nombre');

        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('id', 'like', "%{$buscar}%")
                    ->orWhere('nombre', 'like', "%{$buscar}%")
                    ->orWhere('correo', 'like', "%{$buscar}%");
            });
        }

        $clientes = $query->paginate(12)->withQueryString();
        $roles = RolCliente::orderBy('id')->get();

        return view('admin.clientes.index', compact('clientes', 'roles', 'buscar'));
    }

    public function storeCliente(StoreClienteRequest $request): RedirectResponse
    {
        Cliente::create($request->validated() + ['activo' => $request->boolean('activo', true)]);

        return redirect()->route('admin.clientes')->with('success', 'Cliente registrado correctamente.');
    }

    public function updateCliente(UpdateClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $datos = $request->validated();
        $datos['activo'] = $request->boolean('activo');

        $cliente->update($datos);

        return redirect()->route('admin.clientes')->with('success', 'Cliente actualizado correctamente.');
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

        $vehiculos = $query->paginate(12)->withQueryString();
        $tipos = TipoVehiculo::orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.vehiculos.index', compact('vehiculos', 'tipos', 'clientes', 'buscar'));
    }

    public function storeVehiculo(StoreVehiculoRequest $request): RedirectResponse
    {
        Vehiculo::create($request->validated() + [
            'autorizado_por_id' => Auth::id(),
            'activo' => $request->boolean('activo', true),
        ]);

        return redirect()->route('admin.vehiculos')->with('success', 'Vehículo registrado correctamente.');
    }

    public function updateVehiculo(UpdateVehiculoRequest $request, Vehiculo $vehiculo): RedirectResponse
    {
        $datos = $request->validated();
        $datos['activo'] = $request->boolean('activo');

        $vehiculo->update($datos);

        return redirect()->route('admin.vehiculos')->with('success', 'Vehículo actualizado correctamente.');
    }

    private function protegerSuperadmin(Usuario $usuario): void
    {
        if ($usuario->esSuperAdmin()) {
            throw new BusinessRuleException('No tienes permiso para modificar a un superadministrador.');
        }
    }
}
