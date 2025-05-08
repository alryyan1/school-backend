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
        Schema::table('student_fee_payments', function (Blueprint $table) {
            // Add payment method column after 'amount'
            $table->enum('payment_method', ['cash', 'bank']) // Define allowed methods
                  ->default('cash') // Set a default
                  ->after('amount')
                  ->comment('Payment method used (e.g., cash, bank transfer)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_fee_payments', function (Blueprint $table) {
             // Check if column exists before trying to drop (safer)
             if (Schema::hasColumn('student_fee_payments', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};