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
        Schema::table('menus', function (Blueprint $table) {
            if (Schema::hasColumn('menus', 'menu_type')) {
                $table->dropColumn('menu_type');
            }
            if (Schema::hasColumn('menus', 'day_no')) {
                $table->dropColumn('day_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (!Schema::hasColumn('menus', 'menu_type')) {
                $table->enum('menu_type', ['standard', 'special'])->default('standard');
            }
            if (!Schema::hasColumn('menus', 'day_no')) {
                $table->integer('day_no')->default(1);
            }
        });
    }
};
