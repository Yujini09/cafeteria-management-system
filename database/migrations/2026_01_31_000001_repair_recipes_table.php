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
        // Check if recipes table exists
        if (!Schema::hasTable('recipes')) {
            Schema::create('recipes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_item_id')->constrained('menu_items')->cascadeOnDelete();
                $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
                $table->decimal('quantity_needed', 10, 3);
                $table->string('unit', 50)->nullable();
                $table->timestamps();
                $table->unique(['menu_item_id', 'inventory_item_id']);
                $table->index('menu_item_id');
                $table->index('inventory_item_id');
            });
        } else {
            // Check if unit column exists (added in later migration)
            if (!Schema::hasColumn('recipes', 'unit')) {
                Schema::table('recipes', function (Blueprint $table) {
                    $table->string('unit', 50)->nullable()->after('quantity_needed');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a repair migration, don't drop on rollback
    }
};
