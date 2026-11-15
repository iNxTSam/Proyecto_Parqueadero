<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoContrato extends Model
{
    public const ACTIVO = 1;

    public const CANCELADO = 2;

    public const FINALIZADO = 3;

    protected $table = 'estados_contrato';

    protected $fillable = ['nombre', 'descripcion'];

    /** @return HasMany<Contrato, $this> */
    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'estado_id');
    }
}
