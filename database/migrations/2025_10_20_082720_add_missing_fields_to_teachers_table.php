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
        Schema::table('teachers', function (Blueprint $table) {
            // Personal Data Fields
            $table->string('place_of_birth')->nullable()->after('birth_date');
            $table->string('nationality')->nullable()->after('place_of_birth');
            $table->enum('document_type', ['جواز سفر', 'البطاقة الشخصية', 'الرقم الوطني'])->nullable()->after('nationality');
            $table->string('document_number')->nullable()->after('document_type');
            $table->enum('marital_status', ['ارمل', 'مطلق', 'متزوج', 'اعزب'])->nullable()->after('document_number');
            $table->integer('number_of_children')->nullable()->after('marital_status');
            $table->integer('children_in_school')->nullable()->after('number_of_children');
            
            // Contact Data Fields
            $table->string('secondary_phone', 15)->nullable()->after('phone');
            $table->string('whatsapp_number', 15)->nullable()->after('secondary_phone');
            
            // Educational Qualifications
            $table->enum('highest_qualification', ['جامعي', 'ثانوي'])->nullable()->after('qualification');
            $table->string('specialization')->nullable()->after('highest_qualification');
            $table->enum('academic_degree', ['دبلوم', 'بكالوريوس', 'ماجستير', 'دكتوراه'])->nullable()->after('specialization');
            
            // Employment Details
            $table->date('appointment_date')->nullable()->after('hire_date');
            $table->integer('years_of_teaching_experience')->nullable()->after('appointment_date');
            $table->text('training_courses')->nullable()->after('years_of_teaching_experience');
            
            // Document Paths
            $table->string('academic_qualifications_doc_path')->nullable()->after('photo');
            $table->string('personal_id_doc_path')->nullable()->after('academic_qualifications_doc_path');
            $table->string('cv_doc_path')->nullable()->after('personal_id_doc_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Drop all the added columns
            $table->dropColumn([
                'place_of_birth',
                'nationality',
                'document_type',
                'document_number',
                'marital_status',
                'number_of_children',
                'children_in_school',
                'secondary_phone',
                'whatsapp_number',
                'highest_qualification',
                'specialization',
                'academic_degree',
                'appointment_date',
                'years_of_teaching_experience',
                'training_courses',
                'academic_qualifications_doc_path',
                'personal_id_doc_path',
                'cv_doc_path'
            ]);
        });
    }
};
