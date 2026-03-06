<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->take(5)->get();

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Please seed users first.');

            return;
        }

        foreach ($customers as $index => $customer) {
            Reservation::create([
                'user_id' => $customer->id,
                'event_name' => 'Sample Event '.($index + 1),
                'event_date' => '2025-09-15',
                'event_time' => '12:00:00',
                'number_of_persons' => 10,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
