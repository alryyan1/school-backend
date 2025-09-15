<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'student_id',
        'transaction_type',
        'description',
        'amount',
        'balance_after',
        'transaction_date',
        'reference_number',
        'payment_method',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'date',
        'metadata' => 'array',
    ];

    // Transaction types constants
    const TYPE_FEE = 'fee';
    const TYPE_PAYMENT = 'payment';
    const TYPE_DISCOUNT = 'discount';
    const TYPE_REFUND = 'refund';
    const TYPE_ADJUSTMENT = 'adjustment';

    // Payment method constants
    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_BANAK = 'bankak';
    const PAYMENT_METHOD_FAWRI = 'Fawri';
    const PAYMENT_METHOD_OCASH = 'OCash';

    /**
     * Get the enrollment that owns the ledger entry.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(EnrollMent::class);
    }

    /**
     * Get the student that owns the ledger entry.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who created the ledger entry.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get entries by transaction type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope to get entries within a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Get the current balance for a student enrollment.
     */
    public static function getCurrentBalance($enrollmentId)
    {
        $latestEntry = self::where('enrollment_id', $enrollmentId)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $latestEntry ? $latestEntry->balance_after : 0;
    }

    /**
     * Add a new ledger entry and calculate the new balance.
     */
    public static function addEntry($data)
    {
        $currentBalance = self::getCurrentBalance($data['enrollment_id']);
        
        // Calculate new balance based on transaction type
        switch ($data['transaction_type']) {
            case self::TYPE_FEE:
                // Fees increase the balance (student owes more)
                $newBalance = $currentBalance + $data['amount'];
                break;
                
            case self::TYPE_PAYMENT:
            case self::TYPE_DISCOUNT:
            case self::TYPE_REFUND:
                // Payments, discounts, and refunds decrease the balance (student owes less)
                $newBalance = $currentBalance - $data['amount'];
                break;
                
            case self::TYPE_ADJUSTMENT:
                // Adjustments can be positive or negative based on amount sign
                $newBalance = $currentBalance + $data['amount'];
                break;
                
            default:
                // Default behavior: add the amount
                $newBalance = $currentBalance + $data['amount'];
                break;
        }

        return self::create([
            ...$data,
            'balance_after' => $newBalance,
        ]);
    }
}
