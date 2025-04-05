<?php

// database/migrations/xxxx_xx_xx_create_classrooms_table.php

use App\Models\GradeLevel;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassroomsTable extends Migration
{
    public function up()
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Class 10-A"
            $table->foreignIdFor(GradeLevel::class)->constrained();
            $table->foreignIdFor(Teacher::class)->nullable()->constrained('teachers'); // Homeroom teacher
            $table->integer('capacity')->default(30);
            $table->foreignIdFor(School::class)->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('classrooms');
    }
}
