<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('menus') || !Schema::hasColumn('menus', 'name')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE menus MODIFY name VARCHAR(255) NULL");
        }
    }

    public function down(): void {
        if (!Schema::hasTable('menus') || !Schema::hasColumn('menus', 'name')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE menus MODIFY name VARCHAR(255) NOT NULL");
        }
    }
};
