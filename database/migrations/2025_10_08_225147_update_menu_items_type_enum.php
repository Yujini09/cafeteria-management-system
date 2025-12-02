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
        // Temporarily change to VARCHAR to allow updates
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN type VARCHAR(255)");

        // Update existing 'meal' to 'food'
        DB::statement("UPDATE menu_items SET type = 'food' WHERE type = 'meal'");

        // Change back to ENUM with new values
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN type ENUM('food','drink','dessert','other') DEFAULT 'other'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Temporarily change to VARCHAR
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN type VARCHAR(255)");

        // Revert 'food' back to 'meal'
        DB::statement("UPDATE menu_items SET type = 'meal' WHERE type = 'food'");

        // Change back to original ENUM
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN type ENUM('meal','drink','dessert','other') DEFAULT 'other'");
    }
};
