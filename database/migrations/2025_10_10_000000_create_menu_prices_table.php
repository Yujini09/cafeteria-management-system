<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_prices', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['standard', 'special']);
            $table->enum('meal_time', ['breakfast', 'am_snacks', 'lunch', 'pm_snacks', 'dinner']);
            $table->decimal('price', 8, 2);
            $table->timestamps();

            $table->unique(['type', 'meal_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_prices');
    }
}
