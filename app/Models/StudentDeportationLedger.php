<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDeportationLedger extends Model
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
        return $this->belongsTo(Enrollment::class);
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
        $totalFees = self::where('enrollment_id', $enrollmentId)
            ->where('transaction_type', self::TYPE_FEE)
            ->sum('amount');

        $totalPayments = self::where('enrollment_id', $enrollmentId)
            ->where('transaction_type', self::TYPE_PAYMENT)
            ->sum('amount');

        $totalDiscounts = self::where('enrollment_id', $enrollmentId)
            ->where('transaction_type', self::TYPE_DISCOUNT)
            ->sum('amount');

        return ($totalFees) - ($totalDiscounts + $totalPayments);
    }

    /**
     * Add a new ledger entry and calculate the new balance.
     */
    public static function addEntry($data)
    {
        $enrollmentId = $data['enrollment_id'];

        $totalFees = self::where('enrollment_id', $enrollmentId)
            ->where('transaction_type', self::TYPE_FEE)
            ->sum('amount');

        $totalPayments = self::where('enrollment_id', $enrollmentId)
            ->where('transaction_type', self::TYPE_PAYMENT)
            ->sum('amount');

        $totalDiscounts = self::where('enrollment_id', $enrollmentId)
            ->where('transaction_type', self::TYPE_DISCOUNT)
            ->sum('amount');

        switch ($data['transaction_type']) {
            case self::TYPE_FEE:
                $newBalance = ($totalFees + $data['amount']) - ($totalDiscounts + $totalPayments);
                break;

            case self::TYPE_PAYMENT:
                $newBalance = ($totalFees) - ($totalDiscounts + $totalPayments + $data['amount']);
                break;

            case self::TYPE_DISCOUNT:
                $newBalance = ($totalFees) - ($totalDiscounts + $data['amount'] + $totalPayments);
                break;

            case self::TYPE_REFUND:
                // Treat refund similar to payment (reduce what the student owes)
                $newBalance = ($totalFees) - ($totalDiscounts + $totalPayments + $data['amount']);
                break;

            case self::TYPE_ADJUSTMENT:
                // Adjustment directly affects balance; positive increases what is owed, negative decreases
                $currentBalance = ($totalFees) - ($totalDiscounts + $totalPayments);
                $newBalance = $currentBalance + $data['amount'];
                break;

            default:
                $currentBalance = ($totalFees) - ($totalDiscounts + $totalPayments);
                $newBalance = $currentBalance + $data['amount'];
                break;
        }

        return self::create([
            ...$data,
            'balance_after' => $newBalance,
        ]);
    }
}


