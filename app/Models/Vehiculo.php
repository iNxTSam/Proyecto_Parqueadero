<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';

    protected $primaryKey = 'placa';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'placa',
        'tipo_vehiculo_id',
        'propietario_id',
        'autorizado_por_id',
        'color',
        'marca',
        'modelo',
        'estado_parqueo',
        'activo',
    ];

    protected $casts = [
        'estado_parqueo' => 'boolean',
        'activo' => 'boolean',
    ];

    /** @return BelongsTo<TipoVehiculo, $this> */
    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoVehiculo::class, 'tipo_vehiculo_id');
    }

    /** @return BelongsTo<Cliente, $this> */
    public function propietario(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'propietario_id');
    }

    /** @return BelongsTo<Usuario, $this> */
    public function autorizadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'autorizado_por_id');
    }

    /** @return HasMany<Contrato, $this> */
    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'vehiculo_id');
    }

    /** @return HasMany<EntradaSalida, $this> */
    public function entradasSalidas(): HasMany
    {
        return $this->hasMany(EntradaSalida::class, 'vehiculo_id');
    }

    /**
     * @param  Builder<Vehiculo>  $query
     * @return Builder<Vehiculo>
     */
    public function scopeParqueados(Builder $query): Builder
    {
        return $query->where('estado_parqueo', true);
    }
}
