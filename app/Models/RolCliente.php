<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RolCliente extends Model
{
    protected $table = 'rolclientes';

    protected $fillable = ['nombre', 'descripcion'];

    /** @return HasMany<Cliente, $this> */
    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'rol_id');
    }
}
