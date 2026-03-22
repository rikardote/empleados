<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fm1ImportBatch extends Model
{
    protected $guarded = [];

    public function forms(): HasMany
    {
        return $this->hasMany(Fm1Form::class, 'import_batch_id');
    }
}
