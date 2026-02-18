<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class VerifySuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'devscarbin@gmail.com')->first();
        if ($user) {
            $user->email_verified_at = now();
            $user->save();
            echo "Email verified for {$user->email}\n";
        } else {
            echo "User not found\n";
        }
    }
}
