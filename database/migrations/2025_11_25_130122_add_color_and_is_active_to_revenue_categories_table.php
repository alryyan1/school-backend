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
        Schema::table('revenue_categories', function (Blueprint $table) {
            $table->string('color', 7)->default('#3B82F6')->after('name'); // Hex color code
            $table->boolean('is_active')->default(true)->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revenue_categories', function (Blueprint $table) {
            $table->dropColumn(['color', 'is_active']);
        });
    }
};
