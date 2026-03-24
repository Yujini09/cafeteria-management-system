<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('accounts:prune-pending {--days=3 : Remove pending accounts older than this many days} {--dry-run : Report candidates without deleting}', function () {
    $days = max(1, (int) $this->option('days'));
    $cutoff = now()->subDays($days);
    $pendingRoles = ['admin_pending', 'customer_pending'];

    $query = User::query()
        ->whereIn('role', $pendingRoles)
        ->where('updated_at', '<=', $cutoff);

    $count = (clone $query)->count();

    if ($this->option('dry-run')) {
        $this->info("Dry run: {$count} pending account(s) would be deleted (updated on/before {$cutoff->toDateTimeString()}).");

        return self::SUCCESS;
    }

    $deleted = $query->delete();

    $this->info("Deleted {$deleted} pending account(s) older than {$days} day(s) (updated on/before {$cutoff->toDateTimeString()}).");

    return self::SUCCESS;
})->purpose('Delete pending admin/customer accounts that are still inactive after 3 days');

Schedule::command('accounts:prune-pending --days=3')->hourly();
