<?php
// app/Models/AcademicYear.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
        'school_id',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'is_current' => 'boolean',
    ];

    /**
     * Get the school that owns the academic year.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
    // In app/Models/AcademicYear.php
    public function academicYearSubjects(): HasMany
    {
        return $this->hasMany(AcademicYearSubject::class);
    }
}
