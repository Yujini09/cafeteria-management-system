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
    Schema::table('reservations', function (Blueprint $table) {
        if (!Schema::hasColumn('reservations', 'contact_person')) {
            $table->string('contact_person')->nullable();
            $table->string('department')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('venue')->nullable();
            $table->string('project_name')->nullable();
            $table->string('account_code')->nullable();
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
