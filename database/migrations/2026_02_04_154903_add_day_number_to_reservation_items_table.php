<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reservation_items', function (Blueprint $table) {
            // Add day_number if it doesn't exist
            if (!Schema::hasColumn('reservation_items', 'day_number')) {
                $table->integer('day_number')->default(1)->after('quantity');
            }
            
            // Also ensure meal_time exists (just in case)
            if (!Schema::hasColumn('reservation_items', 'meal_time')) {
                $table->string('meal_time')->nullable()->after('day_number');
            }
        });
    }

    public function down()
    {
        Schema::table('reservation_items', function (Blueprint $table) {
            $table->dropColumn(['day_number', 'meal_time']);
        });
    }
};