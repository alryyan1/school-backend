<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'student_id',
        'user_id',
        'action_type',
        'field_name',
        'old_value',
        'new_value',
        'description',
        'metadata',
        'changed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'changed_at' => 'datetime',
    ];

    /**
     * Get the enrollment that this log belongs to.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the student that this log belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who made the change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a log entry for enrollment changes.
     */
    public static function logChange(
        int $enrollmentId,
        int $studentId,
        string $actionType,
        string $fieldName,
        $oldValue = null,
        $newValue = null,
        string $description = null,
        array $metadata = []
    ): self {
        return self::create([
            'enrollment_id' => $enrollmentId,
            'student_id' => $studentId,
            'user_id' => auth()->id(),
            'action_type' => $actionType,
            'field_name' => $fieldName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $description,
            'metadata' => $metadata,
            'changed_at' => now(),
        ]);
    }

    /**
     * Get human-readable action type.
     */
    public function getActionTypeLabelAttribute(): string
    {
        return match($this->action_type) {
            'grade_level_change' => 'تغيير المرحلة الدراسية',
            'status_change' => 'تغيير حالة التسجيل',
            'classroom_change' => 'تغيير الفصل الدراسي',
            'fees_change' => 'تغيير الرسوم',
            'discount_change' => 'تغيير الخصم',
            'academic_year_change' => 'تغيير العام الدراسي',
            default => $this->action_type,
        };
    }
}
