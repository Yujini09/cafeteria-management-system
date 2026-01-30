<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('event_name');
            $table->date('event_date');
            $table->date('end_date')->nullable(); // ADDED: for multi-day reservations
            $table->string('event_time')->nullable(); // CHANGED: from time() to string() for time ranges like "7:00-10:00"
            $table->json('day_times')->nullable(); // ADDED: for storing multi-day time data
            $table->integer('number_of_persons');
            $table->text('special_requests')->nullable();
            $table->enum('status', ['pending', 'approved', 'declined', 'cancelled'])->default('pending');
            $table->text('decline_reason')->nullable();

            // ADDED: Contact information fields
            $table->string('contact_person')->nullable();
            $table->string('department')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('venue')->nullable();
            $table->string('project_name')->nullable();
            $table->string('account_code')->nullable();
            
            // ADDED: Payment fields
            $table->string('receipt_path')->nullable();
            $table->timestamp('receipt_uploaded_at')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'under_review'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
