<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_fee_installments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\StudentAcademicYear;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fee_installments', function (Blueprint $table) {
            $table->id();
            // Link to the overall enrollment record for the year
            $table->foreignIdFor(StudentAcademicYear::class)->constrained()->cascadeOnDelete();
            $table->string('title'); // e.g., "القسط الأول", "دفعة شهر سبتمبر", "Term 1 Fee"
            $table->decimal('amount_due', 10, 2); // The amount scheduled for this installment
            $table->decimal('amount_paid', 10, 2)->default(0.00); // Track amount paid specifically for this installment
            $table->date('due_date'); // When this installment is due
            // Status to track if installment is fully paid, partially paid, or pending
            $table->enum('status', ['قيد الانتظار', 'دفع جزئي', 'مدفوع', 'متأخر السداد'])->default('قيد الانتظار');
            $table->text('notes')->nullable(); // Notes specific to this installment
            $table->timestamps();

            // Optional: Index for faster querying
            $table->index(['student_academic_year_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_installments');
    }
};