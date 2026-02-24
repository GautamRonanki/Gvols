<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faculty extends Model
{
    protected $table = 'faculty';

    protected $fillable = [
        'program_id', 'name', 'photo', 'department',
        'courses_taught', 'description', 'sort_order',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
