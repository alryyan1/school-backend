<?php
// database/migrations/xxxx_xx_xx_create_schools_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('state');
            $table->string('city');
            $table->string('logo')->nullable(); // Stores the path/filename of the logo
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}