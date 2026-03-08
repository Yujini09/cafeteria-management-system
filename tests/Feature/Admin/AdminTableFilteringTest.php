<?php

use App\Models\InventoryItem;
use App\Models\Reservation;
use App\Models\User;

function makeAdminUser(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'must_change_password' => false,
    ]);
}

function makeReservation(User $customer, array $attributes = []): Reservation
{
    $reservation = Reservation::create(array_merge([
        'user_id' => $customer->id,
        'event_name' => 'Campus Event',
        'event_date' => now()->addWeek()->toDateString(),
        'event_time' => '08:00-10:00',
        'number_of_persons' => 25,
        'status' => 'pending',
        'payment_status' => 'pending',
        'contact_person' => $customer->name,
        'department' => $customer->department,
        'email' => $customer->email,
        'contact_number' => '09123456789',
        'venue' => 'Main Hall',
        'project_name' => 'Program',
        'account_code' => 'AC-100',
    ], $attributes));

    if (isset($attributes['created_at']) || isset($attributes['updated_at'])) {
        $reservation->forceFill([
            'created_at' => $attributes['created_at'] ?? $reservation->created_at,
            'updated_at' => $attributes['updated_at'] ?? $attributes['created_at'] ?? $reservation->updated_at,
        ])->save();
    }

    return $reservation;
}

test('inventory search works across the entire dataset instead of only the current page', function () {
    $admin = makeAdminUser();

    foreach (range(1, 10) as $index) {
        InventoryItem::create([
            'name' => sprintf('Alpha Item %02d', $index),
            'qty' => 20 + $index,
            'unit' => 'pcs',
            'category' => 'Condiments',
            'expiry_date' => now()->addDays($index)->toDateString(),
        ]);
    }

    InventoryItem::create([
        'name' => 'Zeta Search Match',
        'qty' => 99,
        'unit' => 'pcs',
        'category' => 'Others',
        'expiry_date' => now()->addMonth()->toDateString(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.inventory.index'))
        ->assertOk()
        ->assertDontSeeText('Zeta Search Match');

    $this->actingAs($admin)
        ->get(route('admin.inventory.index', ['search' => 'Zeta Search Match']))
        ->assertOk()
        ->assertSeeText('Zeta Search Match')
        ->assertDontSeeText('Alpha Item 01');
});

test('inventory pagination keeps active search and category filters', function () {
    $admin = makeAdminUser();

    foreach (range(1, 12) as $index) {
        InventoryItem::create([
            'name' => sprintf('Filtered Item %02d', $index),
            'qty' => 10 + $index,
            'unit' => 'pcs',
            'category' => 'Condiments',
            'expiry_date' => now()->addDays($index)->toDateString(),
        ]);
    }

    InventoryItem::create([
        'name' => 'Filtered Item Other Category',
        'qty' => 5,
        'unit' => 'pcs',
        'category' => 'Frozen',
        'expiry_date' => now()->addDays(20)->toDateString(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.inventory.index', [
            'search' => 'Filtered Item',
            'category' => 'Condiments',
            'sort' => 'name',
            'direction' => 'asc',
        ]));

    $response->assertOk()
        ->assertSeeText('Filtered Item 01')
        ->assertSeeText('Filtered Item 10')
        ->assertDontSeeText('Filtered Item 11')
        ->assertDontSeeText('Filtered Item Other Category');

    $pageTwoUrl = null;
    preg_match('/<a href="([^"]+)"[^>]*aria-label="Go to page 2"/', $response->getContent(), $matches);
    if (isset($matches[1])) {
        $pageTwoUrl = html_entity_decode($matches[1], ENT_QUOTES);
    }

    expect($pageTwoUrl)->not->toBeNull();

    parse_str((string) parse_url($pageTwoUrl, PHP_URL_QUERY), $query);

    expect($query)->toMatchArray([
        'search' => 'Filtered Item',
        'category' => 'Condiments',
        'sort' => 'name',
        'direction' => 'asc',
        'page' => '2',
    ]);

    $pageTwoPath = (string) parse_url($pageTwoUrl, PHP_URL_PATH);
    $pageTwoQuery = (string) parse_url($pageTwoUrl, PHP_URL_QUERY);

    $this->actingAs($admin)
        ->get($pageTwoPath.'?'.$pageTwoQuery)
        ->assertOk()
        ->assertSeeText('Filtered Item 11')
        ->assertSeeText('Filtered Item 12')
        ->assertDontSeeText('Filtered Item Other Category');
});

test('reservations search works across the entire dataset instead of only the current page', function () {
    $admin = makeAdminUser();
    $customer = User::factory()->create([
        'role' => 'customer',
        'department' => 'Science Office',
    ]);

    foreach (range(1, 10) as $index) {
        makeReservation($customer, [
            'contact_person' => sprintf('Recent Guest %02d', $index),
            'email' => sprintf('recent-guest-%02d@example.com', $index),
            'created_at' => now()->subMinutes(10 - $index),
        ]);
    }

    makeReservation($customer, [
        'contact_person' => 'Search Target Guest',
        'email' => 'search-target@example.com',
        'created_at' => now()->subDays(3),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.reservations'))
        ->assertOk()
        ->assertDontSeeText('Search Target Guest');

    $this->actingAs($admin)
        ->get(route('admin.reservations', ['search' => 'Search Target Guest']))
        ->assertOk()
        ->assertSeeText('Search Target Guest')
        ->assertDontSeeText('Recent Guest 01');
});

test('reservations pagination keeps active search and filters', function () {
    $admin = makeAdminUser();
    $customer = User::factory()->create([
        'role' => 'customer',
        'department' => 'Engineering Office',
    ]);

    foreach (range(1, 12) as $index) {
        makeReservation($customer, [
            'contact_person' => sprintf('Paged Guest %02d', $index),
            'email' => sprintf('paged-guest-%02d@example.com', $index),
            'status' => 'pending',
            'payment_status' => 'pending',
            'created_at' => now()->subMinutes($index),
        ]);
    }

    makeReservation($customer, [
        'contact_person' => 'Paged Guest Approved',
        'email' => 'paged-guest-approved@example.com',
        'status' => 'approved',
        'payment_status' => 'paid',
        'created_at' => now()->subSecond(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.reservations', [
            'search' => 'Paged Guest',
            'status' => 'pending',
            'created_sort' => 'desc',
        ]));

    $response->assertOk()
        ->assertSeeText('Paged Guest 01')
        ->assertSeeText('Paged Guest 10')
        ->assertDontSeeText('Paged Guest 11')
        ->assertDontSeeText('Paged Guest Approved');

    $pageTwoUrl = null;
    preg_match('/<a href="([^"]+)"[^>]*aria-label="Go to page 2"/', $response->getContent(), $matches);
    if (isset($matches[1])) {
        $pageTwoUrl = html_entity_decode($matches[1], ENT_QUOTES);
    }

    expect($pageTwoUrl)->not->toBeNull();

    parse_str((string) parse_url($pageTwoUrl, PHP_URL_QUERY), $query);

    expect($query)->toMatchArray([
        'search' => 'Paged Guest',
        'status' => 'pending',
        'created_sort' => 'desc',
        'page' => '2',
    ]);

    $pageTwoPath = (string) parse_url($pageTwoUrl, PHP_URL_PATH);
    $pageTwoQuery = (string) parse_url($pageTwoUrl, PHP_URL_QUERY);

    $this->actingAs($admin)
        ->get($pageTwoPath.'?'.$pageTwoQuery)
        ->assertOk()
        ->assertSeeText('Paged Guest 11')
        ->assertSeeText('Paged Guest 12')
        ->assertDontSeeText('Paged Guest Approved');
});
