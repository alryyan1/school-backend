<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_academic_years_id',
        'note',
        'user_id',
    ];

    public function studentAcademicYear()
    {
        return $this->belongsTo(EnrollMent::class, 'student_academic_years_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
