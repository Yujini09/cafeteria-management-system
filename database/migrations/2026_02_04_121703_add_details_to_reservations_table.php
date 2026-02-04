<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Add end_date if it doesn't exist
            if (!Schema::hasColumn('reservations', 'end_date')) {
                $table->date('end_date')->nullable()->after('event_date');
            }

            // Add day_times (JSON) if it doesn't exist
            if (!Schema::hasColumn('reservations', 'day_times')) {
                $table->json('day_times')->nullable()->after('event_time');
            }

            // Add payment status columns
            if (!Schema::hasColumn('reservations', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('status');
            }
            if (!Schema::hasColumn('reservations', 'receipt_uploaded_at')) {
                $table->timestamp('receipt_uploaded_at')->nullable()->after('receipt_path');
            }
            if (!Schema::hasColumn('reservations', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'end_date', 
                'day_times', 
                'receipt_path', 
                'receipt_uploaded_at', 
                'payment_status'
            ]);
        });
    }
};