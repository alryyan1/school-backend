<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLedgerDeletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'ledger_entry_id',
        'enrollment_id',
        'student_id',
        'transaction_type',
        'description',
        'amount',
        'transaction_date',
        'balance_before',
        'balance_after',
        'reference_number',
        'payment_method',
        'metadata',
        'original_created_by',
        'original_created_at',
        'deleted_by',
        'deletion_reason',
        'deleted_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
        'transaction_date' => 'date',
        'original_created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the enrollment that this deletion record belongs to.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the student that this deletion record belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who originally created the ledger entry.
     */
    public function originalCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'original_created_by');
    }

    /**
     * Get the user who deleted the ledger entry.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
