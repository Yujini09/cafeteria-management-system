<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('contact_messages')) {
            return;
        }

        if (!Schema::hasColumn('contact_messages', 'status')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                $table->string('status')->default('UNREAD')->after('message');
            });
        }

        if (Schema::hasColumn('contact_messages', 'is_read') && Schema::hasColumn('contact_messages', 'status')) {
            DB::table('contact_messages')
                ->where('is_read', true)
                ->where('status', 'UNREAD')
                ->update(['status' => 'READ']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('contact_messages')) {
            return;
        }

        if (Schema::hasColumn('contact_messages', 'status')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
