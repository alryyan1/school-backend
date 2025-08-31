<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentWarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_academic_year_id',
        'issued_by_user_id',
        'severity',
        'reason',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function studentAcademicYear(): BelongsTo
    {
        return $this->belongsTo(EnrollMent::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }
}


