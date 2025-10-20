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
        Schema::create('bulk_whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulk_whatsapp_send_id')->constrained()->onDelete('cascade');
            $table->string('recipient');
            $table->string('message');
            $table->integer('sequence_order');
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->string('ultramsg_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_whatsapp_messages');
    }
};
