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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->string('transaction_id')->unique(); // External transaction reference
            $table->string('status'); // 'pending', 'completed', 'failed', 'cancelled'
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->json('payment_details')->nullable(); // Payment method specific details
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['enrollment_id', 'payment_date']);
            $table->index(['student_id', 'payment_date']);
            $table->index(['status', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
