<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeInstallment extends Model
{
    use HasFactory;
    protected $fillable = ['enrollment_id', 'title', 'amount_due', 'amount_paid', 'due_date', 'status', 'notes'];
    protected $casts = ['amount_due' => 'decimal:2', 'amount_paid' => 'decimal:2', 'due_date' => 'date:Y-m-d'];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(EnrollMent::class);
    }

    public function student(): BelongsTo
    {
        return $this->enrollment->student();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StudentFeePayment::class);
    } // Link to payments made FOR this installment
}
