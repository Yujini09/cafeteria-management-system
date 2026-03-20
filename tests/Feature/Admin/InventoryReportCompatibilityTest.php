<?php

use App\Exceptions\IncompatibleRecipeUnitException;
use App\Exports\InventoryReportExport;
use App\Models\InventoryItem;
use App\Models\Menu;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;

it('fails inventory export when incompatible units are detected', function () {
    $customer = User::factory()->create(['role' => 'customer']);

    $inventory = InventoryItem::create([
        'name' => 'Milk',
        'qty' => 3,
        'unit' => 'liters',
        'category' => 'Perishable',
    ]);

    $menu = Menu::create([
        'name' => 'Menu X',
        'description' => 'Test',
        'price' => 100,
        'meal_time' => 'breakfast',
        'type' => 'standard',
    ]);

    $menuItem = $menu->items()->create([
        'name' => 'Milk Stew',
        'type' => 'meal',
    ]);

    $menuItem->recipes()->create([
        'inventory_item_id' => $inventory->id,
        'quantity_needed' => 100,
        'unit' => 'g', // incompatible with liters
    ]);

    $reservation = Reservation::create([
        'user_id' => $customer->id,
        'event_name' => 'Inventory Export Test',
        'event_date' => now()->toDateString(),
        'event_time' => '09:00',
        'number_of_persons' => 10,
        'status' => 'approved',
        'payment_status' => 'pending',
    ]);

    $reservation->items()->create([
        'menu_id' => $menu->id,
        'quantity' => 1,
        'day_number' => 1,
        'meal_time' => 'breakfast',
    ]);

    $export = new InventoryReportExport(Carbon::now()->subDay(), Carbon::now()->addDay());

    expect(fn () => $export->collection())
        ->toThrow(IncompatibleRecipeUnitException::class);
});
