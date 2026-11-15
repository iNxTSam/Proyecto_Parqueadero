<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reporte extends Model
{
    protected $table = 'reportes';

    protected $fillable = [
        'nombre',
        'tipo',
        'formato',
        'ruta_archivo',
        'parametros',
        'generado_por_id',
    ];

    protected $casts = [
        'parametros' => 'array',
    ];

    /** @return BelongsTo<Usuario, $this> */
    public function generadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'generado_por_id');
    }
}
