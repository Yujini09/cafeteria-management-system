<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
             * Run the migrations.
             */
public function up()
{
    Schema::table('reservations', function (Blueprint $table) {
        // Only add payment_status if it doesn't already exist
        if (!Schema::hasColumn('reservations', 'payment_status')) {
            $table->string('payment_status')->default('unpaid');
        }
        
        // Only add or_number if it doesn't already exist
        if (!Schema::hasColumn('reservations', 'or_number')) {
            $table->string('or_number')->nullable();
        }
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            //
        });
    }
};
