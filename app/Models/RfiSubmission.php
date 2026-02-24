<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfiSubmission extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'admission_term_id',
        'program_id',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function admissionTerm(): BelongsTo
    {
        return $this->belongsTo(AdmissionTerm::class);
    }
}
