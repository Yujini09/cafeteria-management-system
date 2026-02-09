<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'account_code')) {
                $table->string('account_code')->nullable()->after('department_office');
            }
            if (!Schema::hasColumn('payments', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('account_code');
            }
            if (!Schema::hasColumn('payments', 'receipt_uploaded_at')) {
                $table->timestamp('receipt_uploaded_at')->nullable()->after('receipt_path');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payments')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'receipt_uploaded_at')) {
                $table->dropColumn('receipt_uploaded_at');
            }
            if (Schema::hasColumn('payments', 'receipt_path')) {
                $table->dropColumn('receipt_path');
            }
            if (Schema::hasColumn('payments', 'account_code')) {
                $table->dropColumn('account_code');
            }
        });
    }
};
