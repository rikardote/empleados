<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fm1Form extends Model
{
    protected $guarded = [];

    protected $casts = [
        'fecha_movimiento' => 'date',
        'fecha_final'      => 'date',
        'fecha_inicio_ant' => 'date',
        'fecha_fin_ant'    => 'date',
    ];
}
