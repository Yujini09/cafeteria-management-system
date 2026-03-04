<?php

use App\Models\Menu;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    config(['app.timezone' => 'Asia/Manila']);

    $this->menu = Menu::create([
        'name' => 'Test Breakfast Menu',
        'description' => 'For reservation rate limit tests',
        'price' => 150,
        'meal_time' => 'breakfast',
        'type' => 'standard',
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

function reservationStepOneSessionData(): array
{
    return [
        'start_date' => Carbon::now('Asia/Manila')->addDays(7)->toDateString(),
        'end_date' => Carbon::now('Asia/Manila')->addDays(7)->toDateString(),
        'day_times' => json_encode([
            Carbon::now('Asia/Manila')->addDays(7)->toDateString() => [
                'start_time' => '08:00',
                'end_time' => '10:00',
            ],
        ]),
        'name' => 'Test Customer',
        'email' => 'customer@example.com',
        'phone' => '09123456789',
        'venue' => 'Conference Room',
        'activity' => 'Planning Session',
        'department' => 'IT',
        'address' => 'Campus',
        'project_name' => 'Rate Limit Test',
        'account_code' => 'AC-123',
    ];
}

function reservationStorePayload(int $menuId): array
{
    return [
        'notes' => 'Testing reservation limits',
        'reservations' => [
            1 => [
                'breakfast' => [
                    'category' => 'standard',
                    'menu' => $menuId,
                    'qty' => 10,
                ],
            ],
        ],
    ];
}

function createReservationForUser(User $user, Carbon $createdAt, string $status = 'pending'): Reservation
{
    $reservation = Reservation::create([
        'user_id' => $user->id,
        'event_name' => 'Existing Reservation',
        'event_date' => $createdAt->copy()->addDays(7)->toDateString(),
        'end_date' => $createdAt->copy()->addDays(7)->toDateString(),
        'event_time' => '08:00',
        'day_times' => [
            $createdAt->copy()->addDays(7)->toDateString() => [
                'start_time' => '08:00',
                'end_time' => '10:00',
            ],
        ],
        'number_of_persons' => 10,
        'special_requests' => 'Test setup',
        'status' => $status,
        'contact_person' => $user->name,
        'department' => 'IT',
        'address' => 'Campus',
        'email' => $user->email,
        'contact_number' => '09123456789',
        'venue' => 'Conference Room',
        'project_name' => 'Existing Test',
        'account_code' => 'AC-123',
        'payment_status' => 'pending',
    ]);

    Reservation::whereKey($reservation->id)->update([
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);

    return $reservation->fresh();
}

function submitReservation($testCase, User $user, int $menuId)
{
    return $testCase
        ->actingAs($user)
        ->withSession(['reservation_data' => reservationStepOneSessionData()])
        ->from(route('reservation.create'))
        ->post(route('reservation.store'), reservationStorePayload($menuId));
}

it('blocks creating a reservation during the per-user cooldown window', function () {
    Carbon::setTestNow(Carbon::parse('2026-03-04 09:10:00', 'Asia/Manila'));

    $user = User::factory()->create([
        'role' => 'customer',
        'email' => 'cooldown@example.com',
    ]);

    createReservationForUser($user, Carbon::now('Asia/Manila')->copy()->subMinutes(5));

    $response = submitReservation($this, $user, $this->menu->id);

    $response->assertSessionHasErrors([
        'reservations' => 'Please wait 5 minutes before creating another reservation.',
    ]);

    expect(Reservation::where('user_id', $user->id)->count())->toBe(1);
});

it('shows the cooldown notice and locks the first-step reservation form', function () {
    Carbon::setTestNow(Carbon::parse('2026-03-04 09:10:00', 'Asia/Manila'));

    $user = User::factory()->create([
        'role' => 'customer',
        'email' => 'step-one-lock@example.com',
    ]);

    createReservationForUser($user, Carbon::now('Asia/Manila')->copy()->subMinutes(5));

    $response = $this
        ->actingAs($user)
        ->get(route('reservation_form'));

    $response
        ->assertOk()
        ->assertSee('Please wait 5 minutes before creating another reservation.')
        ->assertSee('If you placed an incorrect reservation, please cancel the previous one.')
        ->assertSee('data-reservation-form-locked="true"', false);
});

it('blocks creating a sixth non-cancelled reservation in the same app day', function () {
    Carbon::setTestNow(Carbon::parse('2026-03-04 14:00:00', 'Asia/Manila'));

    $user = User::factory()->create([
        'role' => 'customer',
        'email' => 'daily-cap@example.com',
    ]);

    foreach ([8, 9, 10, 11, 12] as $hour) {
        createReservationForUser($user, Carbon::parse("2026-03-04 {$hour}:00:00", 'Asia/Manila'));
    }

    $response = submitReservation($this, $user, $this->menu->id);

    $response->assertSessionHasErrors([
        'reservations' => "You've reached the maximum of 5 reservations for today.",
    ]);

    expect(Reservation::where('user_id', $user->id)->count())->toBe(5);
});

it('frees the daily cap immediately when the customer cancels a reservation', function () {
    Carbon::setTestNow(Carbon::parse('2026-03-04 14:00:00', 'Asia/Manila'));

    $user = User::factory()->create([
        'role' => 'customer',
        'email' => 'customer-cancel@example.com',
    ]);

    $reservations = collect();
    foreach ([8, 9, 10, 11, 12] as $hour) {
        $reservations->push(
            createReservationForUser($user, Carbon::parse("2026-03-04 {$hour}:00:00", 'Asia/Manila'))
        );
    }

    $reservationToCancel = $reservations->last();

    $cancelResponse = $this
        ->actingAs($user)
        ->from(route('reservation_details'))
        ->patch(route('reservation.cancel', $reservationToCancel));

    $cancelResponse->assertSessionHas('success', 'Reservation cancelled successfully.');

    $createResponse = submitReservation($this, $user, $this->menu->id);

    $createResponse
        ->assertRedirect(route('reservation_details'))
        ->assertSessionHas('success', 'Reservation placed successfully!');

    expect(Reservation::where('user_id', $user->id)->count())->toBe(6);
    expect(
        Reservation::where('user_id', $user->id)
            ->whereNotIn('status', ['cancelled', 'canceled'])
            ->count()
    )->toBe(5);
});

it('treats admin-side cancellation the same by excluding cancelled reservations from the daily cap', function () {
    Carbon::setTestNow(Carbon::parse('2026-03-04 14:00:00', 'Asia/Manila'));

    $user = User::factory()->create([
        'role' => 'customer',
        'email' => 'admin-cancel@example.com',
    ]);

    $reservations = collect();
    foreach ([8, 9, 10, 11, 12] as $hour) {
        $reservations->push(
            createReservationForUser($user, Carbon::parse("2026-03-04 {$hour}:00:00", 'Asia/Manila'))
        );
    }

    $reservations->last()->update(['status' => 'cancelled']);

    $response = submitReservation($this, $user, $this->menu->id);

    $response
        ->assertRedirect(route('reservation_details'))
        ->assertSessionHas('success', 'Reservation placed successfully!');

    expect(
        Reservation::where('user_id', $user->id)
            ->whereNotIn('status', ['cancelled', 'canceled'])
            ->count()
    )->toBe(5);
});
