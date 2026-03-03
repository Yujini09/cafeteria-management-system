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
            'date',
            'time',
            'guests',
            'receipt_path',
            'receipt_uploaded_at',
            'payment_requested_at',
            'payment_last_reminder_at',
            'payment_reminder_count',
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
            if (!Schema::hasColumn('reservations', 'date')) {
                $table->date('date')->nullable();
            }

            if (!Schema::hasColumn('reservations', 'time')) {
                $table->time('time')->nullable();
            }

            if (!Schema::hasColumn('reservations', 'guests')) {
                $table->unsignedInteger('guests')->nullable();
            }

            if (!Schema::hasColumn('reservations', 'receipt_path')) {
                $table->string('receipt_path')->nullable();
            }

            if (!Schema::hasColumn('reservations', 'receipt_uploaded_at')) {
                $table->timestamp('receipt_uploaded_at')->nullable();
            }

            if (!Schema::hasColumn('reservations', 'payment_requested_at')) {
                $table->timestamp('payment_requested_at')->nullable();
            }

            if (!Schema::hasColumn('reservations', 'payment_last_reminder_at')) {
                $table->timestamp('payment_last_reminder_at')->nullable();
            }

            if (!Schema::hasColumn('reservations', 'payment_reminder_count')) {
                $table->unsignedTinyInteger('payment_reminder_count')->default(0);
            }
        });
    }
};
