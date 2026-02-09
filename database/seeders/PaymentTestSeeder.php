<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PaymentTestSeeder extends Seeder
{
    public function run(): void
    {
        $customerEmail = 'nepomuceno.johnella@clsu2.edu.ph';

        $customer = User::firstOrCreate(
            ['email' => $customerEmail],
            [
                'name' => 'Johnella Nepomuceno',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'contact_no' => '09123456789',
                'department' => 'CLSU',
            ]
        );

        $menu = Menu::firstOrCreate(
            [
                'name' => 'Payment Test Menu',
                'meal_time' => 'lunch',
                'type' => 'standard',
            ],
            [
                'description' => 'Seeder menu for payment tests.',
                'price' => 150,
            ]
        );

        $finishedReservation = Reservation::updateOrCreate(
            [
                'user_id' => $customer->id,
                'event_name' => 'Payment Test - Finished Reservation',
            ],
            [
                'event_date' => Carbon::today()->subDays(2)->toDateString(),
                'end_date' => Carbon::today()->subDay()->toDateString(),
                'event_time' => '09:00',
                'number_of_persons' => 40,
                'status' => 'approved',
                'payment_status' => 'pending',
                'payment_requested_at' => null,
                'payment_last_reminder_at' => null,
                'payment_reminder_count' => 0,
                'contact_person' => $customer->name,
                'department' => $customer->department ?? 'CLSU',
                'address' => 'CLSU Main Campus',
                'email' => $customerEmail,
                'contact_number' => $customer->contact_number ?? $customer->contact_no,
                'venue' => 'Main Hall',
                'special_requests' => 'Seeder: finished reservation to trigger payment request.',
            ]
        );

        if (! $finishedReservation->items()->exists()) {
            ReservationItem::create([
                'reservation_id' => $finishedReservation->id,
                'menu_id' => $menu->id,
                'quantity' => 40,
                'day_number' => 1,
                'meal_time' => 'lunch',
            ]);
        }

        $finishedUnpaidReservation = Reservation::updateOrCreate(
            [
                'user_id' => $customer->id,
                'event_name' => 'Payment Test - Finished Unpaid Reservation 2',
            ],
            [
                'event_date' => Carbon::today()->subDays(6)->toDateString(),
                'end_date' => Carbon::today()->subDays(5)->toDateString(),
                'event_time' => '10:00',
                'number_of_persons' => 30,
                'status' => 'approved',
                'payment_status' => 'pending',
                'payment_requested_at' => null,
                'payment_last_reminder_at' => null,
                'payment_reminder_count' => 0,
                'contact_person' => $customer->name,
                'department' => $customer->department ?? 'CLSU',
                'address' => 'CLSU Main Campus',
                'email' => $customerEmail,
                'contact_number' => $customer->contact_number ?? $customer->contact_no,
                'venue' => 'Conference Hall',
                'special_requests' => 'Seeder: additional finished unpaid reservation.',
            ]
        );

        if (! $finishedUnpaidReservation->items()->exists()) {
            ReservationItem::create([
                'reservation_id' => $finishedUnpaidReservation->id,
                'menu_id' => $menu->id,
                'quantity' => 30,
                'day_number' => 1,
                'meal_time' => 'lunch',
            ]);
        }

        $reviewReservation = Reservation::updateOrCreate(
            [
                'user_id' => $customer->id,
                'event_name' => 'Payment Test - Under Review',
            ],
            [
                'event_date' => Carbon::today()->subDays(5)->toDateString(),
                'end_date' => Carbon::today()->subDays(4)->toDateString(),
                'event_time' => '12:00',
                'number_of_persons' => 25,
                'status' => 'approved',
                'payment_status' => 'under_review',
                'contact_person' => $customer->name,
                'department' => $customer->department ?? 'CLSU',
                'address' => 'CLSU Main Campus',
                'email' => $customerEmail,
                'contact_number' => $customer->contact_number ?? $customer->contact_no,
                'venue' => 'Function Room',
                'special_requests' => 'Seeder: payment already submitted.',
            ]
        );

        if (! $reviewReservation->items()->exists()) {
            ReservationItem::create([
                'reservation_id' => $reviewReservation->id,
                'menu_id' => $menu->id,
                'quantity' => 25,
                'day_number' => 1,
                'meal_time' => 'lunch',
            ]);
        }

        if (! $reviewReservation->payments()->exists()) {
            Payment::create([
                'reservation_id' => $reviewReservation->id,
                'user_id' => $customer->id,
                'reference_number' => 'PAYTEST-' . $reviewReservation->id,
                'department_office' => $reviewReservation->department,
                'payer_name' => $customer->name,
                'amount' => (float) $menu->price * 25,
                'status' => 'submitted',
            ]);
        }
    }
}
