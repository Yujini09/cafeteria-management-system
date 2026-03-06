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

        $columns = array_values(array_filter([
            'quote_status',
            'total_snapshot',
            'order_request_mode',
            'personalized_items',
        ], fn (string $column): bool => Schema::hasColumn('reservations', $column)));

        if ($columns === []) {
            return;
        }

        Schema::table('reservations', function (Blueprint $table) use ($columns) {
            $table->dropColumn($columns);
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('reservations')) {
            return;
        }

        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'quote_status')) {
                $table->string('quote_status')->nullable();
            }

            if (!Schema::hasColumn('reservations', 'total_snapshot')) {
                $table->decimal('total_snapshot', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('reservations', 'order_request_mode')) {
                $table->string('order_request_mode')->nullable();
            }

            if (!Schema::hasColumn('reservations', 'personalized_items')) {
                $table->json('personalized_items')->nullable();
            }
        });
    }
};
