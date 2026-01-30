<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('menu_items', function (Blueprint $t) {
      $t->id();
      $t->foreignId('menu_id')->constrained()->cascadeOnDelete();
      $t->string('name');
      // type helps you group: ulam/drink/dessert/etc.
      $t->enum('type', ['meal','drink','dessert','other'])->default('other');
      $t->timestamps();
      $t->unique(['menu_id','name']);
    });
  }
  public function down(): void { Schema::dropIfExists('menu_items'); }
};