<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nombre',
        'telefono',
        'correo',
        'activo',
        'rol_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /** @return BelongsTo<RolCliente, $this> */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(RolCliente::class, 'rol_id');
    }

    /** @return HasMany<Vehiculo, $this> */
    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class, 'propietario_id');
    }

    /** @return HasMany<Contrato, $this> */
    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'propietario_id');
    }

    /** @return HasMany<EntradaSalida, $this> */
    public function entradasSalidas(): HasMany
    {
        return $this->hasMany(EntradaSalida::class, 'propietario_id');
    }

    public function esAprendiz(): bool
    {
        return (int) $this->rol_id === 1;
    }
}
