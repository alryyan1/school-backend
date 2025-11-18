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
        Schema::create('other_revenues', function (Blueprint $table) {
            $table->id();
            $table->text('desc');
            $table->decimal('amount', 10, 2);
            $table->foreignId('revenue_category_id')->constrained()->onDelete('cascade');
            $table->enum('payment_method', ['cash', 'bank']);
            $table->date('revenue_date');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_revenues');
    }
};
