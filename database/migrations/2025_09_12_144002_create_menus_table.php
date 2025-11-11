<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
Schema::create('menus', function (Blueprint $t) {
    $t->id();
    $t->string('name');
    $t->enum('meal_time', ['breakfast', 'am_snacks', 'lunch', 'pm_snacks', 'dinner']);
    $t->enum('type', ['standard', 'special']);
    $t->decimal('price', 10, 2);
    $t->text('description')->nullable(); // <— ADD HERE
    $t->timestamps();
});

  }
  public function down(): void { Schema::dropIfExists('menus'); }
};
