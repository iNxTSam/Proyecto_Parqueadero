<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoVehiculo extends Model
{
    protected $table = 'tipos_vehiculo';

    protected $fillable = ['nombre', 'descripcion'];

    /** @return HasMany<Vehiculo, $this> */
    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class, 'tipo_vehiculo_id');
    }

    /** @return HasMany<Bahia, $this> */
    public function bahias(): HasMany
    {
        return $this->hasMany(Bahia::class, 'tipo_vehiculo_id');
    }

    /** @return HasMany<Tarifa, $this> */
    public function tarifas(): HasMany
    {
        return $this->hasMany(Tarifa::class, 'tipo_vehiculo_id');
    }
}
