<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeInstallment extends Model
{
    use HasFactory;
    protected $fillable = ['student_academic_year_id', 'title', 'amount_due', 'amount_paid', 'due_date', 'status', 'notes'];
    protected $casts = ['amount_due' => 'decimal:2', 'amount_paid' => 'decimal:2', 'due_date' => 'date:Y-m-d'];

    public function studentAcademicYear(): BelongsTo
    {
        return $this->belongsTo(StudentAcademicYear::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(StudentFeePayment::class);
    } // Link to payments made FOR this installment
}
