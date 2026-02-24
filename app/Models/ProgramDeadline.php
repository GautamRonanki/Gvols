<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramDeadline extends Model
{
    protected $fillable = ['program_id', 'admission_term_id', 'deadline_date'];

    protected $casts = ['deadline_date' => 'date'];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function admissionTerm(): BelongsTo
    {
        return $this->belongsTo(AdmissionTerm::class);
    }
}
