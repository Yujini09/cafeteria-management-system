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
        if (!Schema::hasTable('notifications') || Schema::hasColumn('notifications', 'title')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            $table->string('title')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('notifications') || !Schema::hasColumn('notifications', 'title')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
};
