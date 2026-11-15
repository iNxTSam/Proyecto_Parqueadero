<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReporteService
{
    public const TIPOS = [
        'ocupacion' => 'Ocupación actual',
        'ingresos' => 'Ingresos por contratos',
        'contratos' => 'Contratos',
        'usuarios' => 'Usuarios del sistema',
    ];

    /**
     * @return Collection<int, \stdClass>
     */
    public function datos(string $tipo, ?string $fechaInicio = null, ?string $fechaFin = null): Collection
    {
        return match ($tipo) {
            'ocupacion' => $this->ocupacion(),
            'ingresos' => $this->ingresos($fechaInicio, $fechaFin),
            'contratos' => $this->contratos($fechaInicio, $fechaFin),
            'usuarios' => $this->usuarios(),
            default => collect(),
        };
    }

    /**
     * @return array<int, string>
     */
    public function encabezados(string $tipo): array
    {
        return match ($tipo) {
            'ocupacion' => ['Placa', 'Tipo', 'Propietario', 'Ticket', 'Fecha de entrada'],
            'ingresos' => ['Recibo', 'Fecha', 'Valor', 'Placa', 'Cliente', 'Estado'],
            'contratos' => ['Placa', 'Propietario', 'Fecha inicio', 'Fecha fin', 'Tarifa', 'Estado', 'Valor'],
            'usuarios' => ['Nombre', 'Cédula', 'Correo', 'Rol', 'Activo', 'Registro'],
            default => [],
        };
    }

    /**
     * @return Collection<int, \stdClass>
     */
    private function ocupacion(): Collection
    {
        return DB::table('vehiculos')
            ->join('tipos_vehiculo', 'vehiculos.tipo_vehiculo_id', '=', 'tipos_vehiculo.id')
            ->join('clientes', 'vehiculos.propietario_id', '=', 'clientes.id')
            ->leftJoin('entrada_salida', function ($join) {
                $join->on('entrada_salida.vehiculo_id', '=', 'vehiculos.placa')
                    ->whereNull('entrada_salida.fecha_salida');
            })
            ->where('vehiculos.estado_parqueo', true)
            ->select(
                'vehiculos.placa',
                'tipos_vehiculo.nombre as tipo',
                'clientes.nombre as propietario',
                'entrada_salida.ticket',
                'entrada_salida.fecha_entrada'
            )
            ->orderBy('entrada_salida.fecha_entrada')
            ->get();
    }

    /**
     * @return Collection<int, \stdClass>
     */
    private function ingresos(?string $desde, ?string $hasta): Collection
    {
        $query = DB::table('recibos')
            ->join('contratos', 'recibos.contrato_id', '=', 'contratos.id')
            ->join('vehiculos', 'contratos.vehiculo_id', '=', 'vehiculos.placa')
            ->join('clientes', 'contratos.propietario_id', '=', 'clientes.id')
            ->select(
                'recibos.numero',
                'recibos.fecha_emision',
                'recibos.valor',
                'vehiculos.placa',
                'clientes.nombre as cliente',
                'recibos.estado'
            )
            ->orderByDesc('recibos.fecha_emision');

        if ($desde) {
            $query->whereDate('recibos.fecha_emision', '>=', $desde);
        }

        if ($hasta) {
            $query->whereDate('recibos.fecha_emision', '<=', $hasta);
        }

        return $query->get();
    }

    /**
     * @return Collection<int, \stdClass>
     */
    private function contratos(?string $desde, ?string $hasta): Collection
    {
        $query = DB::table('contratos')
            ->join('vehiculos', 'contratos.vehiculo_id', '=', 'vehiculos.placa')
            ->join('clientes', 'contratos.propietario_id', '=', 'clientes.id')
            ->join('tarifas', 'contratos.tarifa_id', '=', 'tarifas.id')
            ->join('estados_contrato', 'contratos.estado_id', '=', 'estados_contrato.id')
            ->select(
                'vehiculos.placa',
                'clientes.nombre as propietario',
                'contratos.fecha_inicio',
                'contratos.fecha_fin',
                'tarifas.nombre as tarifa',
                'estados_contrato.nombre as estado',
                'contratos.valor'
            )
            ->orderByDesc('contratos.id');

        if ($desde) {
            $query->whereDate('contratos.fecha_inicio', '>=', $desde);
        }

        if ($hasta) {
            $query->whereDate('contratos.fecha_inicio', '<=', $hasta);
        }

        return $query->get();
    }

    /**
     * @return Collection<int, \stdClass>
     */
    private function usuarios(): Collection
    {
        return DB::table('usuarios')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->select(
                'usuarios.nombre',
                'usuarios.cedula',
                'usuarios.email',
                'roles.nombre as rol',
                'usuarios.activo',
                'usuarios.created_at'
            )
            ->orderBy('usuarios.nombre')
            ->get();
    }
}
