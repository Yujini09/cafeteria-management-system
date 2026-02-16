<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'payment_requested_at')) {
                // We remove 'after' because payment_status doesn't exist yet
                $table->timestamp('payment_requested_at')->nullable();            }
            if (!Schema::hasColumn('reservations', 'payment_last_reminder_at')) {
                $table->timestamp('payment_last_reminder_at')->nullable()->after('payment_requested_at');
            }
            if (!Schema::hasColumn('reservations', 'payment_reminder_count')) {
                $table->unsignedTinyInteger('payment_reminder_count')->default(0)->after('payment_last_reminder_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_requested_at',
                'payment_last_reminder_at',
                'payment_reminder_count',
            ]);
        });
    }
};
