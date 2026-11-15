<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bahia extends Model
{
    protected $table = 'bahias';

    protected $fillable = [
        'numero',
        'tipo_vehiculo_id',
        'capacidad_maxima',
        'ubicacion',
        'activa',
    ];

    protected $casts = [
        'capacidad_maxima' => 'integer',
        'activa' => 'boolean',
    ];

    /** @return BelongsTo<TipoVehiculo, $this> */
    public function tipoVehiculo(): BelongsTo
    {
        return $this->belongsTo(TipoVehiculo::class, 'tipo_vehiculo_id');
    }

    /**
     * @param  Builder<Bahia>  $query
     * @return Builder<Bahia>
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activa', true);
    }
}
