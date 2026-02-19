<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // No-op.
        // Status migration and data backfill are handled by:
        // 2026_02_18_172322_update_contact_messages_status.php
    }

    public function down(): void
    {
        // No-op.
    }
};
