<?php

// database/migrations/xxxx_xx_xx_create_students_table.php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('student_name'); // لازم رباعي
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
            $table->enum('gender',['ذكر','انثي']);
            // $table->string('closest_name')->nullable();
            // $table->string('closest_phone')->nullable();
            $table->string('referred_school')->nullable();
            $table->string('success_percentage')->nullable();
            $table->string('medical_condition')->nullable();

            $table->string('other_parent')->nullable(); //ولي امر اخر ف حال لم يتوفر الاب او الام
            $table->string('relation_of_other_parent')->nullable();//في حاله لم يتوفر الاب او الام لازم يحدد العلاقه
            $table->string('relation_job')->nullable();//في حاله لم توفر الاب او الام لازم نعبي دي
            $table->string('relation_phone')->nullable();//في حاله لم توفر الاب او الام لازم نعبي دي
            $table->string('relation_whatsapp')->nullable();//في حاله لم توفر الاب او الام لازم نعبي دي
            $table->string('image')->nullable(); // لازم يكون عندنا خيار تحديث الصوره
            $table->boolean('approved')->default(0);
            $table->dateTime('aproove_date')->nullable();
            $table->foreignIdFor(User::class,'approved_by_user')->nullable()->constrained('users');
            $table->boolean('message_sent')->default(0);
            $table->string('goverment_id')->nullable();
            $table->enum('wished_level',['روضه','ابتدائي','متوسط','ثانوي']);
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}
