<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'title', 'program_name', 'slug', 'featured_image', 'program_type_id',
        'degree_coursework_name', 'program_major', 'college_id', 'program_format',
        'duration', 'credit_hours', 'program_fees', 'overview', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_hours' => 'integer',
        'program_fees' => 'decimal:2',
    ];

    public function programType(): BelongsTo
    {
        return $this->belongsTo(ProgramType::class);
    }

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    public function areasOfInterest(): BelongsToMany
    {
        return $this->belongsToMany(AreaOfInterest::class, 'area_of_interest_program');
    }

    public function admissionTerms(): BelongsToMany
    {
        return $this->belongsToMany(AdmissionTerm::class, 'program_admission_terms');
    }

    public function deadlines(): HasMany
    {
        return $this->hasMany(ProgramDeadline::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ProgramRequirement::class)->orderBy('sort_order');
    }

    public function concentrations(): HasMany
    {
        return $this->hasMany(Concentration::class)->orderBy('sort_order');
    }

    public function featuredCourses(): HasMany
    {
        return $this->hasMany(FeaturedCourse::class)->orderBy('sort_order');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class)->orderBy('sort_order');
    }

    public function faculty(): HasMany
    {
        return $this->hasMany(Faculty::class)->orderBy('sort_order');
    }

    public function relatedPrograms(): BelongsToMany
    {
        return $this->belongsToMany(
            Program::class,
            'related_programs',
            'program_id',
            'related_program_id'
        );
    }
}
