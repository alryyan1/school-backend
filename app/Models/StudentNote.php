<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'note',
        'user_id',
    ];

    protected $attributes = [
        'user_id' => 1, // Default user ID
    ];

    public function enrollment()
    {
        return $this->belongsTo(EnrollMent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
