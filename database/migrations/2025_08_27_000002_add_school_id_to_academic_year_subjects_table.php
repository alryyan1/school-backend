<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_year_subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_year_subjects', 'school_id')) {
                $table->foreignId('school_id')->nullable()->after('academic_year_id')->constrained('schools');
            }
        });

        try {
            DB::statement(<<<SQL
                UPDATE academic_year_subjects ays
                LEFT JOIN academic_years ay ON ay.id = ays.academic_year_id
                SET ays.school_id = ay.school_id
            SQL);
        } catch (\Throwable $e) {
            logger()->warning('Backfill academic_year_subjects.school_id skipped: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        Schema::table('academic_year_subjects', function (Blueprint $table) {
            if (Schema::hasColumn('academic_year_subjects', 'school_id')) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            }
        });
    }
};
