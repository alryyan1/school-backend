<?php
// app/Models/Classroom.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // E.g., "10A", "Grade 5", "Science Lab"
        'description', // Optional description of the classroom
    ];

 
}