<?php
// app/Models/Teacher.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'national_id',
        'name',
        'email',
        'phone',
        'gender',
        'birth_date',
        'qualification',
        'hire_date',
        'address',
        'photo', // Path to photo
        'is_active',
    ];
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher'); // Assumes pivot table name
    }
    protected $casts = [
        'birth_date' => 'date:Y-m-d', // Cast to date, format on serialization
        'hire_date' => 'date:Y-m-d',  // Cast to date, format on serialization
        'is_active' => 'boolean',
    ];
    // In app/Models/Teacher.php
    public function academicYearSubjects(): HasMany
    {
        return $this->hasMany(AcademicYearSubject::class);
    }
    
}
