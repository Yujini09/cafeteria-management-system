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
        if (!Schema::hasTable('reservation_items')) {
            return;
        }

        $hasReservationId = Schema::hasColumn('reservation_items', 'reservation_id');
        $hasMenuId = Schema::hasColumn('reservation_items', 'menu_id');

        Schema::table('reservation_items', function (Blueprint $table) use ($hasReservationId, $hasMenuId) {
            if (!$hasReservationId) {
                $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            }

            if (!$hasMenuId) {
                $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('reservation_items')) {
            return;
        }

        Schema::table('reservation_items', function (Blueprint $table) {
            try {
                $table->dropForeign(['reservation_id']);
            } catch (\Throwable $e) {
                // Ignore if foreign key does not exist.
            }

            try {
                $table->dropForeign(['menu_id']);
            } catch (\Throwable $e) {
                // Ignore if foreign key does not exist.
            }
        });

        Schema::table('reservation_items', function (Blueprint $table) {
            if (Schema::hasColumn('reservation_items', 'reservation_id')) {
                $table->dropColumn('reservation_id');
            }
            if (Schema::hasColumn('reservation_items', 'menu_id')) {
                $table->dropColumn('menu_id');
            }
        });
    }
};
