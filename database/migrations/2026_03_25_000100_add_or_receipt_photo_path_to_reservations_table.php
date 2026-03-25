<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reservations')) {
            return;
        }

        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'or_receipt_photo_path')) {
                $table->string('or_receipt_photo_path')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('reservations')) {
            return;
        }

        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'or_receipt_photo_path')) {
                $table->dropColumn('or_receipt_photo_path');
            }
        });
    }
};
