<?php

use App\Models\Employee;
use App\Models\SchoolStage;
use App\Models\Teacher;
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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // School ID (e.g., SCH-001)
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->string('principal_name')->nullable();
            $table->date('establishment_date')->nullable();
            $table->string('logo')->nullable();
            // $table->boolean('is_active')->default(true);
            $table->timestamps();
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
