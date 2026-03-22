<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventory_items') || !Schema::hasColumn('inventory_items', 'qty')) {
            return;
        }

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->decimal('qty', 12, 2)->default(0)->change();
        });

        DB::statement("UPDATE inventory_items SET qty = ROUND(qty, 0) WHERE LOWER(unit) IN ('pieces', 'packs')");
    }

    public function down(): void
    {
        if (!Schema::hasTable('inventory_items') || !Schema::hasColumn('inventory_items', 'qty')) {
            return;
        }

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->decimal('qty', 12, 3)->default(0)->change();
        });
    }
};
