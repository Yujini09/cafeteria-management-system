<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if menu_items table exists
        if (!Schema::hasTable('menu_items')) {
            // Recreate menu_items table
            Schema::create('menu_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
                $table->string('name');
                $table->enum('type', ['food', 'drink', 'dessert', 'other'])->default('other');
                $table->timestamps();
                $table->index('menu_id');
            });
        } else {
            // Check if the table has all required columns
            if (!Schema::hasColumn('menu_items', 'menu_id')) {
                Schema::table('menu_items', function (Blueprint $table) {
                    $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
                });
            }
            
            if (!Schema::hasColumn('menu_items', 'name')) {
                Schema::table('menu_items', function (Blueprint $table) {
                    $table->string('name');
                });
            }
            
            if (!Schema::hasColumn('menu_items', 'type')) {
                Schema::table('menu_items', function (Blueprint $table) {
                    $table->enum('type', ['food', 'drink', 'dessert', 'other'])->default('other');
                });
            }
            
            // Ensure type enum has correct values
            if (Schema::hasColumn('menu_items', 'type')) {
                $driver = Schema::getConnection()->getDriverName();
                if ($driver === 'mysql') {
                    DB::statement("ALTER TABLE menu_items MODIFY COLUMN type ENUM('food','drink','dessert','other') DEFAULT 'other'");
                }
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
