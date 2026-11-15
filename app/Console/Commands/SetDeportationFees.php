<?php

namespace App\Console\Commands;

use App\Models\DeportationFee;
use App\Models\Enrollment;
use App\Models\StudentDeportationLedger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * رفع رسوم الترحيل للمشتركين الحاليين: يضيف قيد "رسوم" في دفتر الترحيل
 * بالفرق بين الرسوم الجديدة وما سُجّل عليهم من رسوم ترحيل حتى الآن.
 *
 * أمثلة:
 *   php artisan fees:set-deportation --dry-run   (المبالغ من صفحة إعدادات رسوم الترحيل)
 *   php artisan fees:set-deportation
 */
class SetDeportationFees extends Command
{
    protected $signature = 'fees:set-deportation
                            {--internal= : رسوم الترحيل الداخلي (الافتراضي: من الإعدادات)}
                            {--external= : رسوم الترحيل الخارجي (الافتراضي: من الإعدادات)}
                            {--almatamma= : رسوم ترحيل المتمة (الافتراضي: من الإعدادات)}
                            {--charge-missing : إضافة الرسوم كاملة لمن ليس عليه أي رسوم ترحيل في الدفتر}
                            {--dry-run : عرض ما سيتغير بدون حفظ}';

    protected $description = 'Raise deportation fees for current subscribers by adding the difference to their deportation ledger';

    public function handle(): int
    {
        $targets = [
            'داخلي' => (int) ($this->option('internal') ?? DeportationFee::amountFor('داخلي')),
            'خارجي' => (int) ($this->option('external') ?? DeportationFee::amountFor('خارجي')),
            'المتمة' => (int) ($this->option('almatamma') ?? DeportationFee::amountFor('المتمة')),
        ];

        $enrollments = Enrollment::query()
            ->where('deportation', true)
            ->whereIn('deportation_type', array_keys($targets))
            ->get();

        // مجموع رسوم الترحيل المسجلة لكل تسجيل
        $currentFees = StudentDeportationLedger::query()
            ->whereIn('enrollment_id', $enrollments->pluck('id'))
            ->where('transaction_type', StudentDeportationLedger::TYPE_FEE)
            ->groupBy('enrollment_id')
            ->selectRaw('enrollment_id, SUM(amount) as total')
            ->pluck('total', 'enrollment_id');

        $toCharge = [];
        $stats = ['charge' => 0, 'already' => 0, 'missing' => 0, 'higher' => 0];

        foreach ($enrollments as $e) {
            $target = $targets[$e->deportation_type];
            $current = (float) ($currentFees[$e->id] ?? 0);

            if ($current == 0 && !$this->option('charge-missing')) {
                $stats['missing']++;
                continue;
            }
            if ($current == $target) {
                $stats['already']++;
                continue;
            }
            if ($current > $target) {
                $stats['higher']++;
                continue;
            }

            $toCharge[] = [$e, $current, $target - $current];
            $stats['charge']++;
        }

        $byType = collect($toCharge)->groupBy(fn ($r) => $r[0]->deportation_type);
        $this->table(['النوع', 'الرسوم الجديدة', 'عدد الطلاب', 'مجموع الفروقات المضافة'],
            collect($targets)->map(fn ($t, $type) => [
                $type, $t,
                $byType->get($type, collect())->count(),
                $byType->get($type, collect())->sum(fn ($r) => $r[2]),
            ])->values()->all());

        $this->line("سيتم تعديلهم: {$stats['charge']}");
        $this->line("رسومهم بالفعل بالمبلغ الجديد: {$stats['already']}");
        $this->line("رسومهم المسجلة أعلى من الجديد (لن تتغير): {$stats['higher']}");
        if (!$this->option('charge-missing')) {
            $this->line("مشتركون بدون أي رسوم ترحيل في الدفتر (تم تخطيهم، استخدم --charge-missing لإضافتها): {$stats['missing']}");
        }

        if ($this->option('dry-run')) {
            $this->warn('تجربة فقط (--dry-run): لم يتم حفظ أي تغيير.');
            return self::SUCCESS;
        }

        if (empty($toCharge)) {
            $this->info('لا يوجد ما يتم تعديله.');
            return self::SUCCESS;
        }

        if (!$this->confirm('هل تريد تنفيذ التعديل؟', false)) {
            return self::SUCCESS;
        }

        DB::transaction(function () use ($toCharge) {
            foreach ($toCharge as [$e, $current, $diff]) {
                StudentDeportationLedger::addEntry([
                    'enrollment_id' => $e->id,
                    'student_id' => $e->student_id,
                    'transaction_type' => StudentDeportationLedger::TYPE_FEE,
                    'description' => $current > 0
                        ? 'تحديث رسوم اشتراك الترحيل - ' . $e->deportation_type
                        : 'رسوم اشتراك الترحيل - ' . $e->deportation_type,
                    'amount' => $diff,
                    'transaction_date' => now()->toDateString(),
                    'reference_number' => 'DEP-UPDATE-' . $e->id,
                    'payment_method' => null,
                    'metadata' => [
                        'deportation_type' => $e->deportation_type,
                        'deportation_path_id' => $e->deportation_path_id,
                        'old_fees' => $current,
                        'new_fees' => $current + $diff,
                        'fee_change' => $diff,
                        'source' => 'fees:set-deportation',
                    ],
                    'created_by' => null,
                ]);
            }
        });

        $this->info('تم التعديل بنجاح.');
        return self::SUCCESS;
    }
}
