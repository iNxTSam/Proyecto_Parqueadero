<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarifa extends Model
{
    protected $table = 'tarifas';

    protected $fillable = [
        'nombre',
        'tipo',
        'tipo_vehiculo_id',
        'valor',
        'dias',
        'activa',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'dias' => 'integer',
        'activa' => 'boolean',
    ];

    /** @return BelongsTo<TipoVehiculo, $this> */
    public function tipoVehiculo(): BelongsTo
    {
        return $this->belongsTo(TipoVehiculo::class, 'tipo_vehiculo_id');
    }

    /** @return HasMany<Contrato, $this> */
    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'tarifa_id');
    }

    /**
     * @param  Builder<Tarifa>  $query
     * @return Builder<Tarifa>
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activa', true);
    }
}
