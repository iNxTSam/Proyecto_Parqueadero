<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Bahia;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\EntradaSalida;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ParqueoService
{
    /**
     * Registra el ingreso de un vehículo aplicando las reglas de cupo y contrato.
     */
    public function registrarIngreso(Cliente $cliente, Vehiculo $vehiculo, Usuario $vigilante): EntradaSalida
    {
        return DB::transaction(function () use ($cliente, $vehiculo, $vigilante) {
            $vehiculo = Vehiculo::whereKey($vehiculo->placa)->lockForUpdate()->firstOrFail();
            $cliente = Cliente::whereKey($cliente->id)->firstOrFail();

            if ((string) $vehiculo->propietario_id !== (string) $cliente->id) {
                throw new BusinessRuleException('El vehículo no pertenece al cliente seleccionado.');
            }

            if (! $cliente->activo) {
                throw new BusinessRuleException('El cliente está inactivo y no puede ingresar vehículos.');
            }

            if (! $vehiculo->activo) {
                throw new BusinessRuleException('El vehículo está inactivo.');
            }

            if ($vehiculo->estado_parqueo || EntradaSalida::where('vehiculo_id', $vehiculo->placa)->whereNull('fecha_salida')->exists()) {
                throw new BusinessRuleException('El vehículo ya se encuentra dentro del parqueadero.');
            }

            if ($cliente->esAprendiz() && ! Contrato::where('vehiculo_id', $vehiculo->placa)->vigentes()->exists()) {
                throw new BusinessRuleException('El aprendiz no tiene un contrato activo para este vehículo.');
            }

            $bahias = Bahia::where('tipo_vehiculo_id', $vehiculo->tipo_vehiculo_id)
                ->where('activa', true)
                ->lockForUpdate()
                ->get();

            $capacidad = (int) $bahias->sum('capacidad_maxima');

            if ($capacidad <= 0) {
                throw new BusinessRuleException('No hay bahías habilitadas para este tipo de vehículo.');
            }

            $ocupados = Vehiculo::where('tipo_vehiculo_id', $vehiculo->tipo_vehiculo_id)
                ->where('estado_parqueo', true)
                ->lockForUpdate()
                ->count();

            if ($ocupados >= $capacidad) {
                throw new BusinessRuleException('No hay cupo disponible para este tipo de vehículo.');
            }

            $entrada = EntradaSalida::create([
                'propietario_id' => $cliente->id,
                'vehiculo_id' => $vehiculo->placa,
                'ticket' => $this->generarTicket(),
                'fecha_entrada' => now(),
                'marcar_salida' => false,
                'registrado_por_id' => $vigilante->id,
            ]);

            $vehiculo->update(['estado_parqueo' => true]);

            return $entrada;
        });
    }

    /**
     * Registra la salida. Un aprendiz debe presentar el ticket entregado al ingresar.
     */
    public function registrarSalida(EntradaSalida $entrada, ?string $ticket, Usuario $vigilante): EntradaSalida
    {
        return DB::transaction(function () use ($entrada, $ticket, $vigilante) {
            $entrada = EntradaSalida::with('cliente')->whereKey($entrada->id)->lockForUpdate()->firstOrFail();

            if (! $entrada->estaAbierta()) {
                throw new BusinessRuleException('La salida de este vehículo ya fue registrada.');
            }

            if ($entrada->cliente?->esAprendiz()) {
                $esperado = strtoupper(trim((string) $entrada->ticket));
                $recibido = strtoupper(trim((string) $ticket));

                if ($recibido === '' || ! hash_equals($esperado, $recibido)) {
                    throw new BusinessRuleException('El ticket no coincide con el registrado al ingreso.');
                }
            }

            $entrada->update([
                'fecha_salida' => now(),
                'marcar_salida' => true,
                'salida_registrada_por_id' => $vigilante->id,
            ]);

            Vehiculo::whereKey($entrada->vehiculo_id)->update(['estado_parqueo' => false]);

            return $entrada->fresh(['cliente', 'vehiculo']);
        });
    }

    /**
     * Ocupación actual agrupada por tipo de vehículo.
     *
     * @return Collection<int, array{tipo: string, bahias: int, capacidad: int, ocupados: int, libres: int, porcentaje: int}>
     */
    public function ocupacionPorTipo(): Collection
    {
        $ocupados = Vehiculo::where('estado_parqueo', true)
            ->selectRaw('tipo_vehiculo_id, COUNT(*) as total')
            ->groupBy('tipo_vehiculo_id')
            ->pluck('total', 'tipo_vehiculo_id');

        return Bahia::with('tipoVehiculo')
            ->where('activa', true)
            ->get()
            ->groupBy('tipo_vehiculo_id')
            ->map(function (Collection $grupo, $tipoId) use ($ocupados) {
                $tipo = $grupo->first()->tipoVehiculo;
                $capacidad = (int) $grupo->sum('capacidad_maxima');
                $cantidad = (int) ($ocupados[$tipoId] ?? 0);

                return [
                    'tipo' => $tipo->nombre,
                    'bahias' => $grupo->count(),
                    'capacidad' => $capacidad,
                    'ocupados' => $cantidad,
                    'libres' => max(0, $capacidad - $cantidad),
                    'porcentaje' => $capacidad > 0 ? min(100, (int) round($cantidad / $capacidad * 100)) : 0,
                ];
            })
            ->values();
    }

    /**
     * Genera un ticket legible y único para la entrada.
     */
    public function generarTicket(): string
    {
        foreach (range(1, 10) as $intento) {
            $ticket = 'T-'.now()->format('ymd').'-'.Str::upper(Str::random(5));

            if (! EntradaSalida::where('ticket', $ticket)->exists()) {
                return $ticket;
            }
        }

        throw new BusinessRuleException('No fue posible generar el ticket. Intenta de nuevo.');
    }
}
