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
        Schema::table('student_ledgers', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'bankak', 'Fawri', 'OCash'])->nullable()->after('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_ledgers', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
