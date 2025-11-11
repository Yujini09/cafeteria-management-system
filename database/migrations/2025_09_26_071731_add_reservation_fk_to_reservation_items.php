<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // If you use a non-default connection, uncomment:
    // protected $connection = 'mysql';

    public function up(): void
    {
        if (! Schema::hasTable('reservations')) {
            Schema::create('reservations', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();

                // "Rich" event fields (nullable so legacy seeders can still run)
                $t->string('event_name')->nullable();
                $t->date('event_date')->nullable();
                $t->time('event_time')->nullable();
                $t->unsignedInteger('number_of_persons')->nullable();
                $t->text('special_requests')->nullable();

                // Legacy alias fields used by existing code/seeders
                $t->date('date')->nullable();
                $t->time('time')->nullable();
                $t->unsignedInteger('guests')->nullable();

                // Common fields
                $t->enum('status', ['pending','approved','declined'])->default('pending');
                $t->text('decline_reason')->nullable();

                $t->timestamps();

                // Optional: helpful index for filtering by time
                $t->index(['event_date', 'event_time']);
                $t->index(['date', 'time']);
                $t->index(['status']);
            });
            return;
        }

        // Table exists — add any missing columns safely.
        Schema::table('reservations', function (Blueprint $t) {
            // Rich fields
            if (!Schema::hasColumn('reservations','event_name'))        { $t->string('event_name')->nullable()->after('user_id'); }
            if (!Schema::hasColumn('reservations','event_date'))        { $t->date('event_date')->nullable()->after('event_name'); }
            if (!Schema::hasColumn('reservations','event_time'))        { $t->time('event_time')->nullable()->after('event_date'); }
            if (!Schema::hasColumn('reservations','number_of_persons')) { $t->unsignedInteger('number_of_persons')->nullable()->after('event_time'); }
            if (!Schema::hasColumn('reservations','special_requests'))  { $t->text('special_requests')->nullable()->after('number_of_persons'); }

            // Legacy fields
            if (!Schema::hasColumn('reservations','date'))              { $t->date('date')->nullable()->after('special_requests'); }
            if (!Schema::hasColumn('reservations','time'))              { $t->time('time')->nullable()->after('date'); }
            if (!Schema::hasColumn('reservations','guests'))            { $t->unsignedInteger('guests')->nullable()->after('time'); }

            // Shared fields
            if (!Schema::hasColumn('reservations','status'))            { $t->enum('status', ['pending','approved','declined'])->default('pending')->after('guests'); }
            if (!Schema::hasColumn('reservations','decline_reason'))    { $t->text('decline_reason')->nullable()->after('status'); }
        });
    }

    public function down(): void
    {
        // If the table was created by this migration originally, it's safe to drop it.
        // If you're worried, you can instead drop columns conditionally.
        if (Schema::hasTable('reservations')) {
            Schema::drop('reservations');
        }
    }
};
