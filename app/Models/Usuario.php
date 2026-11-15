<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    public const SUPERADMIN = 1;

    public const ADMIN = 2;

    public const VIGILANTE = 3;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'cedula',
        'carnet',
        'contacto',
        'email',
        'password',
        'rol_id',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'activo' => 'boolean',
    ];

    /** @return BelongsTo<Rol, $this> */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /** @return HasMany<Vehiculo, $this> */
    public function vehiculosAutorizados(): HasMany
    {
        return $this->hasMany(Vehiculo::class, 'autorizado_por_id');
    }

    public function esSuperAdmin(): bool
    {
        return (int) $this->rol_id === self::SUPERADMIN;
    }

    public function esAdmin(): bool
    {
        return (int) $this->rol_id === self::ADMIN;
    }

    public function esVigilante(): bool
    {
        return (int) $this->rol_id === self::VIGILANTE;
    }

    public function dashboardRoute(): string
    {
        return match ((int) $this->rol_id) {
            self::SUPERADMIN => 'superadmin.dashboard',
            self::ADMIN => 'admin.dashboard',
            default => 'vigilante.dashboard',
        };
    }
}
