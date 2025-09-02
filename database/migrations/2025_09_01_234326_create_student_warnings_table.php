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
        Schema::create('student_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('issued_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('severity', ['low', 'medium', 'high'])->default('low');
            $table->text('reason');
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
            
            $table->index('student_id');
            $table->index('severity');
            $table->index('issued_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_warnings');
    }
};
