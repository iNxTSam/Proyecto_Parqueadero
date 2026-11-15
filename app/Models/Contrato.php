<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrato extends Model
{
    protected $table = 'contratos';

    protected $fillable = [
        'vehiculo_id',
        'propietario_id',
        'tarifa_id',
        'fecha_inicio',
        'fecha_fin',
        'estado_id',
        'valor',
        'observaciones',
        'created_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'valor' => 'decimal:2',
    ];

    /** @return BelongsTo<Vehiculo, $this> */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id', 'placa');
    }

    /** @return BelongsTo<Cliente, $this> */
    public function propietario(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'propietario_id');
    }

    /** @return BelongsTo<Tarifa, $this> */
    public function tarifa(): BelongsTo
    {
        return $this->belongsTo(Tarifa::class, 'tarifa_id');
    }

    /** @return BelongsTo<EstadoContrato, $this> */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoContrato::class, 'estado_id');
    }

    /** @return BelongsTo<Usuario, $this> */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    /** @return HasMany<Recibo, $this> */
    public function recibos(): HasMany
    {
        return $this->hasMany(Recibo::class, 'contrato_id');
    }

    /**
     * @param  Builder<Contrato>  $query
     * @return Builder<Contrato>
     */
    public function scopeVigentes(Builder $query): Builder
    {
        return $query->where('estado_id', EstadoContrato::ACTIVO)
            ->whereDate('fecha_fin', '>=', today());
    }
}
