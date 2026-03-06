<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reservations') && Schema::hasColumn('reservations', 'payment_status')) {
            DB::table('reservations')
                ->where('payment_status', 'under_review')
                ->update(['payment_status' => 'pending']);
        }

        Schema::dropIfExists('payments');
    }

    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            return;
        }

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference_number');
            $table->string('department_office')->nullable();
            $table->string('account_code')->nullable();
            $table->string('payer_name');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('receipt_path')->nullable();
            $table->timestamp('receipt_uploaded_at')->nullable();
            $table->string('status')->default('submitted');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['reservation_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }
};
