<?php
// app/Models/School.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// use Illuminate\Database\Eloquent\SoftDeletes; // Uncomment if you add soft deletes later

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $address
 * @property string $phone
 * @property string $email
 * @property string|null $principal_name
 * @property \Illuminate\Support\Carbon|null $establishment_date
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GradeLevel> $gradeLevels
 * @property-read int|null $grade_levels_count
 * @method static \Illuminate\Database\Eloquent\Builder|School newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|School newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|School query()
 * @method static \Illuminate\Database\Eloquent\Builder|School whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereEstablishmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School wherePrincipalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
    /**
     * The grade levels that belong to the school.
     */
    public function gradeLevels(): BelongsToMany // <-- Define Relationship
    {
        return $this->belongsToMany(GradeLevel::class, 'school_grade_levels')->withPivot('basic_fees') // <-- Include pivot data
            ->withTimestamps();;
    }
    protected $casts = [
        'establishment_date' => 'date:Y-m-d', // Cast to date, format on serialization
        // 'is_active' => 'boolean', // Uncomment if added later
    ];

    // Define relationships here if needed in the future
    // e.g., public function students() { return $this->hasMany(Student::class); }
}
