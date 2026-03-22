<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fm1Form extends Model
{
    protected $guarded = [];

    protected $casts = [
        'fecha_movimiento' => 'date',
        'fecha_final'      => 'date',
        'fecha_inicio_ant' => 'date',
        'fecha_fin_ant'    => 'date',
    ];

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(Fm1ImportBatch::class, 'import_batch_id');
    }
}
