<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('reservations', 'service_fee')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('service_fee');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('reservations', 'service_fee')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->decimal('service_fee', 10, 2)->default(0)->after('status');
            });
        }
    }
};
