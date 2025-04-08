<?php
// app/Models/School.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes; // Uncomment if you add soft deletes later

class School extends Model
{
    use HasFactory;
    // use SoftDeletes; // Uncomment if needed

    protected $fillable = [
        'name',
        'code', // School ID
        'address',
        'phone',
        'email',
        'principal_name',
        'establishment_date',
        'logo', // Path to logo file
        // 'is_active', // Uncomment if added later
    ];

    protected $casts = [
        'establishment_date' => 'date:Y-m-d', // Cast to date, format on serialization
        // 'is_active' => 'boolean', // Uncomment if added later
    ];

    // Define relationships here if needed in the future
    // e.g., public function students() { return $this->hasMany(Student::class); }
}