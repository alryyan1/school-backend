<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class MaxAmountBasedOnGrade implements ValidationRule
{
    protected int $schoolId;
    protected int $gradeLevelId;

    public function __construct(int $schoolId, int $gradeLevelId)
    {
        $this->schoolId = $schoolId;
        $this->gradeLevelId = $gradeLevelId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $basicFees = DB::table('school_grade_levels')
            ->where('school_id', $this->schoolId)
            ->where('grade_level_id', $this->gradeLevelId)
            ->value('basic_fees');

        if ($basicFees === null || $value > $basicFees) {
            $fail("المبلغ لا يجب أن يتجاوز قيمة الرسوم الأساسية: {$basicFees}.");
        }
    }
}
