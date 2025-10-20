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
        Schema::create('enrollment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('user_id')->nullable(); // Who made the change
            $table->string('action_type'); // 'grade_level_change', 'status_change', 'classroom_change', etc.
            $table->string('field_name'); // 'grade_level_id', 'status', 'classroom_id', etc.
            $table->text('old_value')->nullable(); // Previous value
            $table->text('new_value')->nullable(); // New value
            $table->text('description')->nullable(); // Human readable description
            $table->json('metadata')->nullable(); // Additional context data
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes for better performance
            $table->index(['enrollment_id', 'action_type']);
            $table->index(['student_id', 'changed_at']);
            $table->index('action_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_logs');
    }
};
