<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (!Schema::hasColumn('menus', 'meal_time')) {
                // SAFE: no "after(...)" so it won't care about column order
                $table->enum('meal_time', [
                    'breakfast','am_snacks','lunch','pm_snacks','dinner'
                ])->default('breakfast');
            }
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (Schema::hasColumn('menus', 'meal_time')) {
                $table->dropColumn('meal_time');
            }
        });
    }
};
