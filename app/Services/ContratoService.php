<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Contrato;
use App\Models\EstadoContrato;
use App\Models\Recibo;
use App\Models\Tarifa;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;

class ContratoService
{
    public function crear(Vehiculo $vehiculo, Tarifa $tarifa, Usuario $usuario, ?string $observaciones = null): Contrato
    {
        if (! $tarifa->activa) {
            throw new BusinessRuleException('La tarifa seleccionada está inactiva.');
        }

        if ((int) $tarifa->tipo_vehiculo_id !== (int) $vehiculo->tipo_vehiculo_id) {
            throw new BusinessRuleException('La tarifa no corresponde al tipo de vehículo.');
        }

        return DB::transaction(function () use ($vehiculo, $tarifa, $usuario, $observaciones) {
            $vehiculo = Vehiculo::whereKey($vehiculo->placa)->lockForUpdate()->firstOrFail();

            $vigente = Contrato::where('vehiculo_id', $vehiculo->placa)
                ->where('estado_id', EstadoContrato::ACTIVO)
                ->whereDate('fecha_fin', '>=', today())
                ->lockForUpdate()
                ->exists();

            if ($vigente) {
                throw new BusinessRuleException('El vehículo ya tiene un contrato activo.');
            }

            $inicio = today();
            $fin = today()->copy()->addDays(max(1, (int) $tarifa->dias));

            $contrato = Contrato::create([
                'vehiculo_id' => $vehiculo->placa,
                'propietario_id' => $vehiculo->propietario_id,
                'tarifa_id' => $tarifa->id,
                'fecha_inicio' => $inicio,
                'fecha_fin' => $fin,
                'estado_id' => EstadoContrato::ACTIVO,
                'valor' => $tarifa->valor,
                'observaciones' => $observaciones,
                'created_by' => $usuario->id,
            ]);

            $this->emitirRecibo($contrato, $usuario);

            return $contrato;
        });
    }

    /**
     * Finaliza el contrato actual y crea uno nuevo para el mismo vehículo.
     */
    public function renovar(Contrato $contrato, Tarifa $tarifa, Usuario $usuario): Contrato
    {
        if (! $tarifa->activa) {
            throw new BusinessRuleException('La tarifa seleccionada está inactiva.');
        }

        return DB::transaction(function () use ($contrato, $tarifa, $usuario) {
            $contrato = Contrato::whereKey($contrato->id)->lockForUpdate()->firstOrFail();

            if ((int) $contrato->estado_id === EstadoContrato::CANCELADO) {
                throw new BusinessRuleException('No se puede renovar un contrato cancelado.');
            }

            if ((int) $tarifa->tipo_vehiculo_id !== (int) $contrato->vehiculo->tipo_vehiculo_id) {
                throw new BusinessRuleException('La tarifa no corresponde al tipo de vehículo.');
            }

            $inicio = $contrato->fecha_fin->isFuture()
                ? $contrato->fecha_fin->copy()->addDay()
                : today();

            $contrato->update(['estado_id' => EstadoContrato::FINALIZADO]);

            $nuevo = Contrato::create([
                'vehiculo_id' => $contrato->vehiculo_id,
                'propietario_id' => $contrato->propietario_id,
                'tarifa_id' => $tarifa->id,
                'fecha_inicio' => $inicio,
                'fecha_fin' => $inicio->copy()->addDays(max(1, (int) $tarifa->dias)),
                'estado_id' => EstadoContrato::ACTIVO,
                'valor' => $tarifa->valor,
                'observaciones' => 'Renovación del contrato #'.$contrato->id,
                'created_by' => $usuario->id,
            ]);

            $this->emitirRecibo($nuevo, $usuario);

            return $nuevo;
        });
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function actualizar(Contrato $contrato, array $datos): Contrato
    {
        return DB::transaction(function () use ($contrato, $datos) {
            $contrato = Contrato::whereKey($contrato->id)->lockForUpdate()->firstOrFail();

            $nuevoEstado = (int) ($datos['estado_id'] ?? $contrato->estado_id);

            if ($nuevoEstado === EstadoContrato::ACTIVO) {
                $otroActivo = Contrato::where('vehiculo_id', $contrato->vehiculo_id)
                    ->where('estado_id', EstadoContrato::ACTIVO)
                    ->whereKeyNot($contrato->id)
                    ->exists();

                if ($otroActivo) {
                    throw new BusinessRuleException('El vehículo ya tiene otro contrato activo.');
                }
            }

            $contrato->update([
                'valor' => $datos['valor'] ?? $contrato->valor,
                'estado_id' => $nuevoEstado,
                'observaciones' => $datos['observaciones'] ?? $contrato->observaciones,
            ]);

            return $contrato;
        });
    }

    /**
     * Marca como finalizados los contratos cuya fecha de fin ya pasó.
     */
    public function marcarVencidos(): int
    {
        return Contrato::where('estado_id', EstadoContrato::ACTIVO)
            ->whereDate('fecha_fin', '<', today())
            ->update(['estado_id' => EstadoContrato::FINALIZADO]);
    }

    private function emitirRecibo(Contrato $contrato, Usuario $usuario): Recibo
    {
        return Recibo::create([
            'numero' => $this->numeroRecibo(),
            'contrato_id' => $contrato->id,
            'fecha_emision' => today(),
            'valor' => $contrato->valor,
            'concepto' => 'Contrato #'.$contrato->id.' - '.$contrato->tarifa?->nombre,
            'estado' => 'pendiente',
            'emitido_por_id' => $usuario->id,
        ]);
    }

    private function numeroRecibo(): string
    {
        foreach (range(1, 10) as $intento) {
            $numero = 'REC-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

            if (! Recibo::where('numero', $numero)->exists()) {
                return $numero;
            }
        }

        throw new BusinessRuleException('No fue posible generar el número de recibo.');
    }
}
