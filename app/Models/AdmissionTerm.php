<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdmissionTerm extends Model
{
    protected $fillable = ['name'];

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'program_admission_terms');
    }
}
