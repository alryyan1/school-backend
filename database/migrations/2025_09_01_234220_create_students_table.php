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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('father_name');
            $table->string('father_job');
            $table->string('father_address');
            $table->string('father_phone');
            $table->string('father_whatsapp')->nullable();
            $table->string('mother_name');
            $table->string('mother_job');
            $table->string('mother_address');
            $table->string('mother_phone');
            $table->string('mother_whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['ذكر', 'انثي']);
            $table->string('referred_school')->nullable();
            $table->string('success_percentage')->nullable();
            $table->string('medical_condition')->nullable();
            $table->string('other_parent')->nullable();
            $table->string('relation_of_other_parent')->nullable();
            $table->string('relation_job')->nullable();
            $table->string('relation_phone')->nullable();
            $table->string('relation_whatsapp')->nullable();
            $table->string('image')->nullable();
            $table->boolean('approved')->default(false);
            $table->dateTime('aproove_date')->nullable();
            $table->foreignId('approved_by_user')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('message_sent')->default(false);
            $table->string('goverment_id')->nullable();
            $table->enum('wished_level', ['روضه', 'ابتدائي', 'متوسط', 'ثانوي']);
            $table->foreignId('wished_school')->nullable()->constrained('schools')->onDelete('set null');
            $table->string('academic_year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
