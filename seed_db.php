#!/usr/bin/env php
<?php

use Illuminate\Support\Facades\Artisan;

define('LARAVEL_START', microtime(true));

// Require autoloader
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require __DIR__.'/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

// Execute artisan commands
Artisan::call('db:seed --class=InventoryItemSeeder 2>&1 || true');
Artisan::call('db:seed --class=MenuSeeder 2>&1 || true');

echo "Database seeded successfully.\n";
