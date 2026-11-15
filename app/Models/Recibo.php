<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recibo extends Model
{
    protected $table = 'recibos';

    protected $fillable = [
        'numero',
        'contrato_id',
        'fecha_emision',
        'valor',
        'concepto',
        'estado',
        'emitido_por_id',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'valor' => 'decimal:2',
    ];

    /** @return BelongsTo<Contrato, $this> */
    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    /** @return BelongsTo<Usuario, $this> */
    public function emitidoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'emitido_por_id');
    }
}
