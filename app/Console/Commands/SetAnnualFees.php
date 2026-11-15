<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\School;
use App\Models\StudentLedger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * تعديل الرسوم الدراسية لكل المدارس، واختيارياً لكل التسجيلات الحالية
 * مع إنشاء قيود في دفتر الطالب بفرق الرسوم (بنفس منطق EnrollMentController::update).
 *
 * أمثلة:
 *   php artisan fees:set-annual 1200000 --dry-run
 *   php artisan fees:set-annual 1200000
 *   php artisan fees:set-annual 1200000 --enrollments --academic-year=2025/2026
 */
class SetAnnualFees extends Command
{
    protected $signature = 'fees:set-annual
                            {amount : الرسوم الجديدة}
                            {--enrollments : تعديل رسوم التسجيلات الحالية أيضاً مع قيود في دفتر الطالب}
                            {--academic-year= : حصر تعديل التسجيلات على عام دراسي محدد}
                            {--dry-run : عرض ما سيتغير بدون حفظ}';

    protected $description = 'Set annual_fees for all schools (and optionally existing enrollment fees with ledger entries)';

    public function handle(): int
    {
        $amount = (int) $this->argument('amount');
        $dryRun = (bool) $this->option('dry-run');

        if ($amount <= 0) {
            $this->error('المبلغ يجب أن يكون أكبر من صفر');
            return self::FAILURE;
        }

        $schools = School::all(['id', 'name', 'annual_fees']);
        $this->table(['ID', 'المدرسة', 'الرسوم الحالية', 'الرسوم الجديدة'],
            $schools->map(fn ($s) => [$s->id, $s->name, $s->annual_fees ?? '-', $amount])->all());

        $enrollments = collect();
        if ($this->option('enrollments')) {
            $enrollments = Enrollment::query()
                ->where('enrollment_type', '!=', 'free') // التسجيل المجاني يبقى بدون رسوم
                ->where('fees', '!=', $amount)
                ->when($this->option('academic-year'), fn ($q, $y) => $q->where('academic_year', $y))
                ->get();
            $this->info("عدد التسجيلات التي ستتغير رسومها: {$enrollments->count()}");
        }

        if ($dryRun) {
            $this->warn('تجربة فقط (--dry-run): لم يتم حفظ أي تغيير.');
            return self::SUCCESS;
        }

        if (!$this->confirm('هل تريد تنفيذ التعديل؟', false)) {
            return self::SUCCESS;
        }

        DB::transaction(function () use ($amount, $enrollments) {
            School::query()->update(['annual_fees' => $amount]);

            foreach ($enrollments as $enrollment) {
                $oldFees = (int) $enrollment->fees;
                $diff = $amount - $oldFees;
                $enrollment->update(['fees' => $amount]);

                StudentLedger::addEntry([
                    'enrollment_id' => $enrollment->id,
                    'student_id' => $enrollment->student_id,
                    'transaction_type' => $diff > 0 ? 'fee' : 'adjustment',
                    'description' => ($diff > 0 ? 'تحديث رسوم التسجيل - ' : 'تخفيض رسوم التسجيل - ') . $enrollment->academic_year,
                    'amount' => $diff,
                    'transaction_date' => now()->format('Y-m-d'),
                    'reference_number' => ($diff > 0 ? 'ENR-UPDATE-' : 'ENR-ADJUST-') . $enrollment->id,
                    'metadata' => [
                        'enrollment_id' => $enrollment->id,
                        'academic_year' => $enrollment->academic_year,
                        'grade_level_id' => $enrollment->grade_level_id,
                        'school_id' => $enrollment->school_id,
                        'old_fees' => $oldFees,
                        'new_fees' => $amount,
                        'fee_change' => $diff,
                        'auto_created' => true,
                        'change_type' => $diff > 0 ? 'fee_update' : 'fee_reduction',
                        'source' => 'fees:set-annual',
                    ],
                    'created_by' => null,
                ]);
            }
        });

        $this->info('تم التعديل بنجاح.');
        return self::SUCCESS;
    }
}
