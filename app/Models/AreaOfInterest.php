<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AreaOfInterest extends Model
{
    protected $table = 'areas_of_interest';

    protected $fillable = ['name', 'slug'];

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'area_of_interest_program');
    }
}
