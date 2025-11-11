<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('recipes', function (Blueprint $table) {
            if (!Schema::hasColumn('recipes', 'unit')) {
                $table->string('unit', 50)->nullable()->after('quantity_needed');
            }
        });
    }

    public function down(): void {
        Schema::table('recipes', function (Blueprint $table) {
            if (Schema::hasColumn('recipes', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }
};
