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
        Schema::create('student_ledger_deletions', function (Blueprint $table) {
            $table->id();
            
            // Original ledger entry data
            $table->unsignedBigInteger('ledger_entry_id');
            $table->foreignId('enrollment_id')->constrained('enrollments');
            $table->foreignId('student_id')->constrained('students');
            
            $table->enum('transaction_type', ['fee', 'payment', 'discount', 'refund', 'adjustment']);
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->date('transaction_date');
            $table->decimal('balance_before', 10, 2)->default(0);
            $table->decimal('balance_after', 10, 2)->default(0);
            $table->string('reference_number', 100)->nullable();
            $table->enum('payment_method', ['cash', 'bankak', 'Fawri', 'OCash'])->nullable();
            $table->json('metadata')->nullable();
            
            // Original creator info
            $table->foreignId('original_created_by')->nullable()->constrained('users');
            $table->timestamp('original_created_at')->nullable();
            
            // Deletion info
            $table->foreignId('deleted_by')->constrained('users');
            $table->text('deletion_reason')->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_ledger_deletions');
    }
};
