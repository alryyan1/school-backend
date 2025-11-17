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
        Schema::create('student_deportation_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('transaction_type'); // 'fee', 'payment', 'discount', 'refund', 'adjustment'
            $table->string('description');
            $table->decimal('amount', 10, 2); // Positive for credits, negative for debits
            $table->decimal('balance_after', 10, 2); // Running balance after this transaction
            $table->date('transaction_date');
            $table->string('reference_number')->nullable(); // For external references
            $table->enum('payment_method', ['cash', 'bankak', 'Fawri', 'OCash'])->nullable();
            $table->json('metadata')->nullable(); // Additional transaction details
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['enrollment_id', 'transaction_date']);
            $table->index(['student_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_deportation_ledgers');
    }
};


