<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntradaSalida extends Model
{
    protected $table = 'entrada_salida';

    protected $fillable = [
        'propietario_id',
        'vehiculo_id',
        'ticket',
        'fecha_entrada',
        'fecha_salida',
        'marcar_salida',
        'registrado_por_id',
        'salida_registrada_por_id',
    ];

    protected $casts = [
        'fecha_entrada' => 'datetime',
        'fecha_salida' => 'datetime',
        'marcar_salida' => 'boolean',
    ];

    /** @return BelongsTo<Cliente, $this> */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'propietario_id');
    }

    /** @return BelongsTo<Vehiculo, $this> */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id', 'placa');
    }

    /** @return BelongsTo<Usuario, $this> */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por_id');
    }

    /** @return BelongsTo<Usuario, $this> */
    public function salidaRegistradaPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'salida_registrada_por_id');
    }

    /**
     * @param  Builder<EntradaSalida>  $query
     * @return Builder<EntradaSalida>
     */
    public function scopeAbiertas(Builder $query): Builder
    {
        return $query->whereNull('fecha_salida');
    }

    public function estaAbierta(): bool
    {
        return $this->fecha_salida === null;
    }
}
