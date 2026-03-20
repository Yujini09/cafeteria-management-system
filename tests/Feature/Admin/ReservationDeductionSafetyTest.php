<?php

use App\Models\InventoryItem;
use App\Models\InventoryUsageLog;
use App\Models\Menu;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Notification as NotificationFacade;

function makeAdminUser(): User
{
    return User::factory()->create([
        'role' => 'admin',
    ]);
}

function makeCustomerUser(): User
{
    return User::factory()->create([
        'role' => 'customer',
    ]);
}

function makePendingReservation(User $customer, Menu $menu, int $quantity = 1): Reservation
{
    $reservation = Reservation::create([
        'user_id' => $customer->id,
        'event_name' => 'Unit Safety Test Event',
        'event_date' => now()->toDateString(),
        'event_time' => '10:00',
        'number_of_persons' => 20,
        'status' => 'pending',
        'payment_status' => 'pending',
    ]);

    $reservation->items()->create([
        'menu_id' => $menu->id,
        'quantity' => $quantity,
        'day_number' => 1,
        'meal_time' => 'lunch',
    ]);

    return $reservation;
}

it('fails approval loudly when a recipe unit is incompatible with stock unit', function () {
    NotificationFacade::fake();

    $admin = makeAdminUser();
    $customer = makeCustomerUser();

    $milk = InventoryItem::create([
        'name' => 'Milk',
        'qty' => 10,
        'unit' => 'liters',
        'category' => 'Perishable',
    ]);

    $menu = Menu::create([
        'name' => 'Breakfast Menu',
        'description' => 'Test',
        'price' => 100,
        'meal_time' => 'breakfast',
        'type' => 'standard',
    ]);

    $menuItem = $menu->items()->create([
        'name' => 'Milk Soup',
        'type' => 'meal',
    ]);

    $menuItem->recipes()->create([
        'inventory_item_id' => $milk->id,
        'quantity_needed' => 100,
        'unit' => 'g', // incompatible with liters stock
    ]);

    $reservation = makePendingReservation($customer, $menu, 2);

    $response = $this->actingAs($admin)
        ->from(route('admin.reservations.show', $reservation))
        ->patch(route('admin.reservations.approve', $reservation));

    $response->assertRedirect(route('admin.reservations.show', $reservation));
    $response->assertSessionHasErrors('inventory_units');

    expect($reservation->fresh()->status)->toBe('pending');
    expect((float) $milk->fresh()->qty)->toEqualWithDelta(10.0, 0.000001);
    expect(InventoryUsageLog::count())->toBe(0);
});

it('does not perform partial deduction when one ingredient has incompatible units', function () {
    NotificationFacade::fake();

    $admin = makeAdminUser();
    $customer = makeCustomerUser();

    $water = InventoryItem::create([
        'name' => 'Water',
        'qty' => 5,
        'unit' => 'liters',
        'category' => 'Beverages',
    ]);

    $salt = InventoryItem::create([
        'name' => 'Salt',
        'qty' => 1000,
        'unit' => 'g',
        'category' => 'Condiments',
    ]);

    $menu = Menu::create([
        'name' => 'Lunch Menu',
        'description' => 'Test',
        'price' => 120,
        'meal_time' => 'lunch',
        'type' => 'standard',
    ]);

    $menuItem = $menu->items()->create([
        'name' => 'Soup',
        'type' => 'meal',
    ]);

    $menuItem->recipes()->create([
        'inventory_item_id' => $water->id,
        'quantity_needed' => 500,
        'unit' => 'ml', // compatible
    ]);

    $menuItem->recipes()->create([
        'inventory_item_id' => $salt->id,
        'quantity_needed' => 1,
        'unit' => 'liters', // incompatible with g stock
    ]);

    $reservation = makePendingReservation($customer, $menu, 1);

    $response = $this->actingAs($admin)
        ->from(route('admin.reservations.show', $reservation))
        ->patch(route('admin.reservations.approve', $reservation));

    $response->assertRedirect(route('admin.reservations.show', $reservation));
    $response->assertSessionHasErrors('inventory_units');

    expect($reservation->fresh()->status)->toBe('pending');
    expect((float) $water->fresh()->qty)->toEqualWithDelta(5.0, 0.000001);
    expect((float) $salt->fresh()->qty)->toEqualWithDelta(1000.0, 0.000001);
    expect(InventoryUsageLog::count())->toBe(0);
});

it('preserves decimal stock quantity after auto deduction', function () {
    NotificationFacade::fake();

    $admin = makeAdminUser();
    $customer = makeCustomerUser();

    $juice = InventoryItem::create([
        'name' => 'Juice Base',
        'qty' => 1.500,
        'unit' => 'liters',
        'category' => 'Beverages',
    ]);

    $menu = Menu::create([
        'name' => 'Snack Menu',
        'description' => 'Test',
        'price' => 90,
        'meal_time' => 'pm_snacks',
        'type' => 'standard',
    ]);

    $menuItem = $menu->items()->create([
        'name' => 'Juice Drink',
        'type' => 'drink',
    ]);

    $menuItem->recipes()->create([
        'inventory_item_id' => $juice->id,
        'quantity_needed' => 250,
        'unit' => 'ml',
    ]);

    $reservation = makePendingReservation($customer, $menu, 1);

    $response = $this->actingAs($admin)
        ->from(route('admin.reservations.show', $reservation))
        ->patch(route('admin.reservations.approve', $reservation));

    $response->assertRedirect(route('admin.reservations.show', $reservation));

    expect($reservation->fresh()->status)->toBe('approved');
    expect((float) $juice->fresh()->qty)->toEqualWithDelta(1.250, 0.000001);
    expect(InventoryUsageLog::count())->toBe(1);
});
