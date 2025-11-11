<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Super Admin (create only if not exists)
        User::firstOrCreate(
            ['email' => 'devscarbin@gmail.com'],
            [
                'name' => 'Devs Carbin',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'contact_no' => '09123456789',
            ]
        );

        // ✅ Create 5 Customers
        for ($i = 1; $i <= 5; $i++) {
            User::firstOrCreate(
                ['email' => "customer{$i}@example.com"],
                [
                    'name' => "Customer {$i}",
                    'password' => Hash::make('password123'),
                    'role' => 'customer',
                    'contact_no' => '0912345678' . $i,
                ]
            );
        }
    }
}
