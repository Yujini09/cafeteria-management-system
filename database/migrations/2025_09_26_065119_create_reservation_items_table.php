<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('day_number')->default(1); // For multi-day reservations
            $table->string('meal_time'); // breakfast, am_snacks, lunch, pm_snacks, dinner
            $table->timestamps();
            
            // Optional: Add index for better performance
            $table->index(['reservation_id', 'day_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_items');
    }
};