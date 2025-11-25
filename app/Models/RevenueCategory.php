<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RevenueCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'is_active',
    ];

    /**
     * Get the other revenues for this category.
     */
    public function otherRevenues(): HasMany
    {
        return $this->hasMany(OtherRevenue::class);
    }
}
