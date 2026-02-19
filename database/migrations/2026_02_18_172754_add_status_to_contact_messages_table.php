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
    // Add this check to prevent the 'Duplicate column' error
    if (!Schema::hasColumn('contact_messages', 'status')) {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('status')->default('UNREAD')->after('message');
        });
    }
}

public function down(): void
{
    Schema::table('contact_messages', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
};
