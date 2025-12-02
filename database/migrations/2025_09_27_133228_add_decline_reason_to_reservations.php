<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // If you need a non-default DB connection, uncomment:
    // protected $connection = 'mysql';

    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $t) {
            if (!Schema::hasColumn('reservations', 'decline_reason')) {
                $t->text('decline_reason')->nullable()->after('status'); // or place where you want
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $t) {
            if (Schema::hasColumn('reservations', 'decline_reason')) {
                $t->dropColumn('decline_reason');
            }
        });
    }
};
