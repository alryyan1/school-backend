<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * رسوم الترحيل لكل نوع (قابلة للتعديل من الإعدادات)
     */
    public function up(): void
    {
        Schema::create('deportation_fees', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // داخلي / خارجي / المتمة
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });

        DB::table('deportation_fees')->insert([
            ['type' => 'داخلي', 'amount' => 600000, 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'خارجي', 'amount' => 750000, 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'المتمة', 'amount' => 1000000, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('deportation_fees');
    }
};
