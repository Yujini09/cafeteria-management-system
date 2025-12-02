<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reservation_items', function (Blueprint $table) {
            $table->integer('day_number')->default(1)->after('quantity');
            $table->string('meal_time')->nullable()->after('day_number');
        });
    }

    public function down()
    {
        Schema::table('reservation_items', function (Blueprint $table) {
            $table->dropColumn(['day_number', 'meal_time']);
        });
    }
};