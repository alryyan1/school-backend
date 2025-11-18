<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtherRevenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'desc',
        'amount',
        'revenue_category_id',
        'payment_method',
        'revenue_date',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'revenue_date' => 'date',
    ];

    /**
     * Get the revenue category that owns the revenue.
     */
    public function revenueCategory(): BelongsTo
    {
        return $this->belongsTo(RevenueCategory::class);
    }

    /**
     * Get the user who created the revenue.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
