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
        Schema::create('bulk_whatsapp_sends', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->integer('total_recipients');
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('pending_count');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_whatsapp_sends');
    }
};
