<?php
// app/Models/StudentFeePayment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentFeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_academic_year_id',
        'amount',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        // Cast amount to a specific decimal format (e.g., 2 decimal places)
        // Adjust '2' if you need different precision
        'amount' => 'decimal:2',
        'payment_date' => 'date:Y-m-d',
    ];

    /**
     * Get the enrollment record this payment belongs to.
     */
    public function studentAcademicYear(): BelongsTo
    {
        return $this->belongsTo(StudentAcademicYear::class);
    }
}