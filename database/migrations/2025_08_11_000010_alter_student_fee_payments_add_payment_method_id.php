<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_fee_payments', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->after('amount')->constrained('payment_methods');
        });

        // Seed default methods and backfill existing rows
        DB::table('payment_methods')->insertOrIgnore([
            ['name' => 'cash', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'bank', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Backfill based on old enum if exists
        if (Schema::hasColumn('student_fee_payments', 'payment_method')) {
            $cashId = DB::table('payment_methods')->where('name', 'cash')->value('id');
            $bankId = DB::table('payment_methods')->where('name', 'bank')->value('id');
            DB::table('student_fee_payments')->where('payment_method', 'cash')->update(['payment_method_id' => $cashId]);
            DB::table('student_fee_payments')->where('payment_method', 'bank')->update(['payment_method_id' => $bankId]);
        }

        // Make FK not nullable after backfill
        Schema::table('student_fee_payments', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable(false)->change();
        });

        // Drop old enum column if it exists
        if (Schema::hasColumn('student_fee_payments', 'payment_method')) {
            Schema::table('student_fee_payments', function (Blueprint $table) {
                $table->dropColumn('payment_method');
            });
        }
    }

    public function down(): void
    {
        // Recreate old enum column (best effort) and drop FK
        Schema::table('student_fee_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('student_fee_payments', 'payment_method')) {
                $table->enum('payment_method', ['cash', 'bank'])->default('cash')->after('amount');
            }
            $table->dropConstrainedForeignId('payment_method_id');
        });

        Schema::dropIfExists('payment_methods');
    }
};


