<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeportationFee extends Model
{
    /** أنواع الترحيل المسموح بها */
    public const TYPES = ['داخلي', 'خارجي', 'المتمة'];

    protected $fillable = ['type', 'amount'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * رسوم نوع الترحيل المحدد (0 إذا لم يكن له رسوم مسجلة)
     */
    public static function amountFor(?string $type): float
    {
        if (!$type) {
            return 0;
        }

        return (float) (static::where('type', $type)->value('amount') ?? 0);
    }
}
