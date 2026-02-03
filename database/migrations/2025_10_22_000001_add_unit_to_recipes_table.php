<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('recipes') || Schema::hasColumn('recipes', 'unit')) {
            return;
        }

        Schema::table('recipes', function (Blueprint $table) {
            $table->string('unit', 50)->nullable()->after('quantity_needed');
        });
    }

    public function down(): void {
        if (!Schema::hasTable('recipes') || !Schema::hasColumn('recipes', 'unit')) {
            return;
        }

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }
};
