// database/migrations/xxxx_xx_xx_create_teachers_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('national_id', 20)->unique(); // الرقم الوطني
            $table->string('name'); // الاسم بالعربية
            $table->string('email')->unique(); // البريد الإلكتروني
            $table->string('phone', 15)->nullable(); // رقم الهاتف
            $table->enum('gender', ['ذكر', 'انثي']); // الجنس
            $table->date('birth_date')->nullable(); // تاريخ الميلاد
            $table->string('qualification'); // المؤهل العلمي
            $table->date('hire_date'); // تاريخ التعيين
            $table->text('address')->nullable(); // العنوان
            $table->string('photo')->nullable(); // صورة الشخصية (مسار الملف)
            $table->boolean('is_active')->default(true); // حالة التنشيط
            $table->timestamps(); // created_at و updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('teachers');
    }
};