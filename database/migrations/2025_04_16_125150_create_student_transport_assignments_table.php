<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_student_transport_assignments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\StudentAcademicYear;
use App\Models\TransportRoute;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_transport_assignments', function (Blueprint $table) {
            $table->id();
            // Link to the specific enrollment record (includes student, year, grade, school)
            $table->foreignIdFor(StudentAcademicYear::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(TransportRoute::class)->constrained()->cascadeOnDelete();
            $table->string('pickup_point')->nullable(); // Optional specific points
            $table->string('dropoff_point')->nullable();
            $table->timestamps();

            // Ensure a student is only assigned to one route per academic year
            $table->unique(['student_academic_year_id'], 'student_route_unique_per_year');
        });
    }
    public function down(): void { Schema::dropIfExists('student_transport_assignments'); }
};
