<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('recipes', function (Blueprint $t) {
      $t->id();
      $t->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
      $t->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
      // quantity needed PER SERVING of this food
      $t->decimal('quantity_needed', 10, 3);
      $t->timestamps();
      $t->unique(['menu_item_id','inventory_item_id']);
    });
  }
  public function down(): void { Schema::dropIfExists('recipes'); }
};
