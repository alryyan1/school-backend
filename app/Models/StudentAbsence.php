<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAbsence extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_academic_year_id',
        'absent_date',
        'reason',
        'excused',
    ];

    protected $casts = [
        'absent_date' => 'date:Y-m-d',
        'excused' => 'boolean',
    ];

    public function enrollment()
    {
        return $this->belongsTo(EnrollMent::class, 'student_academic_year_id');
    }
}


