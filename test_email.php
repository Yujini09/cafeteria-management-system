<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('email', 'nepomuceno.johnella@clsu2.edu.ph')->first();
if ($user) {
    echo "User found: " . $user->email . "\n";
    echo "Verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";
    if (!$user->email_verified_at) {
        $user->sendEmailVerificationNotification();
        echo "Email verification notification sent to nepomuceno.johnella@clsu2.edu.ph\n";
    } else {
        echo "User is already verified\n";
    }
} else {
    echo "User not found\n";
}
