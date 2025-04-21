<?php

namespace App\Rules;

use App\Models\StudentAcademicYear;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PaymentExceedTotal implements ValidationRule
{
    protected int $studentAcademicId;

    public function __construct($std_id){
        $this->studentAcademicId = $std_id;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $student = StudentAcademicYear::find($this->studentAcademicId);
        $amount_paid =   $student->payments()->sum('amount');
        $fees = $student->fees;
        if($amount_paid + $value > $fees){
            $fail('لا يمكن اجمالي المبلغ المدفوع اكبر من الرسوم المقرره للطالب');
        }
        
        // if($student->fees > )
        
    }
}
