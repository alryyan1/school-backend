<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Store human-readable year label like "2025-2026"
            if (!Schema::hasColumn('students', 'academic_year')) {
                $table->string('academic_year')->nullable()->after('wished_school');
            }
        });

        // Best-effort backfill from latest enrollment's academic_year.name
        // Uses MySQL syntax; wrap in try/catch to avoid failing on unsupported DBs
        try {
            DB::statement(<<<SQL
                UPDATE students s
                LEFT JOIN (
                  SELECT say.student_id,
                         ay.name AS year_name
                  FROM student_academic_years say
                  LEFT JOIN academic_years ay ON ay.id = say.academic_year_id
                  INNER JOIN (
                    SELECT student_id, MAX(id) AS latest_id
                    FROM student_academic_years
                    GROUP BY student_id
                  ) latest ON latest.student_id = say.student_id AND latest.latest_id = say.id
                ) latest_year ON latest_year.student_id = s.id
                SET s.academic_year = COALESCE(latest_year.year_name, s.academic_year)
            SQL);
        } catch (\Throwable $e) {
            // Log but do not fail migration if DB engine doesn't support the above
            logger()->warning('students.academic_year backfill skipped: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'academic_year')) {
                $table->dropColumn('academic_year');
            }
        });
    }
};
