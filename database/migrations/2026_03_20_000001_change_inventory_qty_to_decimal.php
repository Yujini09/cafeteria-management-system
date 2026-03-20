<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventory_items') || !Schema::hasColumn('inventory_items', 'qty')) {
            return;
        }

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->decimal('qty', 12, 3)->default(0)->change();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('inventory_items') || !Schema::hasColumn('inventory_items', 'qty')) {
            return;
        }

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->integer('qty')->default(0)->change();
        });
    }
};
