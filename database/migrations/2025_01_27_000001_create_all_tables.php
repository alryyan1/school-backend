<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create fee_installments table
        Schema::create('fee_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('title');
            $table->decimal('amount_due', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->date('due_date')->index();
            $table->enum('status', ['قيد الانتظار', 'دفع جزئي', 'مدفوع', 'متأخر'])->default('قيد الانتظار');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('student_id');
        });

        // Create enrollments table
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('academic_year')->nullable();
            $table->foreignId('grade_level_id')->constrained('grade_levels')->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->onDelete('set null');
            $table->enum('status', ['active', 'transferred', 'graduated', 'withdrawn'])->default('active');
            $table->enum('enrollment_type', ['regular', 'scholarship'])->default('regular');
            $table->integer('fees');
            $table->integer('discount');
            $table->timestamps();
            
            $table->index(['student_id', 'academic_year']);
            $table->index('status');
            $table->index('enrollment_type');
        });

        // Create student_fee_payments table
        Schema::create('student_fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_installment_id')->constrained('fee_installments')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('payment_date');
            $table->index('amount');
        });

        // Create student_warnings table
        Schema::create('student_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('issued_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('severity', ['low', 'medium', 'high'])->default('low');
            $table->text('reason');
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
            
            $table->index('severity');
            $table->index('issued_at');
        });

        // Create student_absences table
        Schema::create('student_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('absent_date');
            $table->string('reason')->nullable();
            $table->boolean('excused')->default(false);
            $table->timestamps();
            
            $table->index('absent_date');
            $table->index('excused');
        });

        // Create grade_level_subjects_table (if it doesn't exist)
        if (!Schema::hasTable('grade_level_subjects_table')) {
            Schema::create('grade_level_subjects_table', function (Blueprint $table) {
                $table->id();
                $table->foreignId('grade_level_id')->constrained('grade_levels')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
                $table->timestamps();
                
                $table->index(['grade_level_id', 'subject_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_absences');
        Schema::dropIfExists('student_warnings');
        Schema::dropIfExists('student_fee_payments');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('fee_installments');
        
        // Don't drop grade_level_subjects_table as it might have data
    }
};
