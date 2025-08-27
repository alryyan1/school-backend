
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $tables = [
        'fee_installments',
        'exam_results',
        'student_transport_assignments',
        'student_absences',
        'student_warnings',
        'student_notes',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'student_id')) {
                    // Add at the end to avoid relying on existing column order
                    $table->foreignId('student_id')->nullable()->constrained('students');
                }
            });
        }

        // Backfill student_id from student_academic_years using best-effort column detection
        foreach ($this->tables as $tableName) {
            // Try common singular column name
            try {
                DB::statement(<<<SQL
                    UPDATE {$tableName} t
                    LEFT JOIN student_academic_years say ON say.id = t.student_academic_year_id
                    SET t.student_id = say.student_id
                SQL);
                continue; // Success for this table
            } catch (\Throwable $e) {
                // Fallback to pluralized column name variant used by some tables (e.g. student_notes)
                try {
                    DB::statement(<<<SQL
                        UPDATE {$tableName} t
                        LEFT JOIN student_academic_years say ON say.id = t.student_academic_years_id
                        SET t.student_id = say.student_id
                    SQL);
                } catch (\Throwable $e2) {
                    logger()->warning("Backfill {$tableName}.student_id skipped: " . $e2->getMessage());
                }
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (Schema::hasColumn($tableName = $table->getTable(), 'student_id')) {
                    // Drop FK and column safely
                    if (method_exists($table, 'dropConstrainedForeignId')) {
                        $table->dropConstrainedForeignId('student_id');
                    } else {
                        // Fallback: attempt to drop by convention, then column
                        try { DB::statement("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$tableName}_student_id_foreign`"); } catch (\Throwable $e) {}
                        $table->dropColumn('student_id');
                    }
                }
            });
        }
    }
};
