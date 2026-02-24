<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Concentration extends Model
{
    protected $fillable = ['program_id', 'name', 'description', 'image', 'sort_order'];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
