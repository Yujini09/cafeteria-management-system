<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Add end_date column
            $table->date('end_date')->nullable()->after('event_date');
            
            // Also add other missing columns from your fillable array
            $table->string('contact_person')->nullable()->after('special_requests');
            $table->string('department')->nullable()->after('contact_person');
            $table->text('address')->nullable()->after('department');
            $table->string('email')->nullable()->after('address');
            $table->string('contact_number')->nullable()->after('email');
            $table->string('venue')->nullable()->after('contact_number');
            $table->string('project_name')->nullable()->after('venue');
            $table->string('account_code')->nullable()->after('project_name');
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'end_date',
                'contact_person',
                'department',
                'address',
                'email',
                'contact_number',
                'venue',
                'project_name',
                'account_code'
            ]);
        });
    }
};