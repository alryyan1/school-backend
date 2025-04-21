<?php
// app/Models/StudentFeePayment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $student_academic_year_id
 * @property string $amount
 * @property \Illuminate\Support\Carbon $payment_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\StudentAcademicYear $studentAcademicYear
 * @method static \Database\Factories\StudentFeePaymentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|StudentFeePayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentFeePayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentFeePayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentFeePayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentFeePayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentFeePayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentFeePayment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentFeePayment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentFeePayment whereStudentAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentFeePayment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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