<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('meal_time', ['breakfast', 'am_snacks', 'lunch', 'pm_snacks', 'dinner']);
            $table->enum('type', ['standard', 'special']);
            $table->timestamps();
            
            // Add index for better query performance
            $table->index(['meal_time', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};