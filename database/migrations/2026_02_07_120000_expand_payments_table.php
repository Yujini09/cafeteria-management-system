<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('reference_number');
                $table->string('department_office')->nullable();
                $table->string('payer_name');
                $table->decimal('amount', 10, 2)->default(0);
                $table->string('status')->default('submitted'); // submitted|approved|rejected
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['reservation_id', 'status']);
                $table->index(['user_id', 'created_at']);
            });

            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'reservation_id')) {
                $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('payments', 'reference_number')) {
                $table->string('reference_number');
            }
            if (!Schema::hasColumn('payments', 'department_office')) {
                $table->string('department_office')->nullable();
            }
            if (!Schema::hasColumn('payments', 'payer_name')) {
                $table->string('payer_name');
            }
            if (!Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('payments', 'status')) {
                $table->string('status')->default('submitted');
            }
            if (!Schema::hasColumn('payments', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('payments', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('payments', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payments')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'reservation_id',
                'user_id',
                'reference_number',
                'department_office',
                'payer_name',
                'amount',
                'status',
                'reviewed_by',
                'reviewed_at',
                'notes',
            ]);
        });
    }
};
