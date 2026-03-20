<?php

use App\Models\InventoryItem;
use App\Models\Menu;
use App\Models\User;

it('blocks inventory unit updates that would break linked recipe compatibility', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $inventory = InventoryItem::create([
        'name' => 'Flour',
        'qty' => 15,
        'unit' => 'g',
        'category' => 'Perishable',
    ]);

    $menu = Menu::create([
        'name' => 'Bread Menu',
        'description' => 'Test',
        'price' => 100,
        'meal_time' => 'breakfast',
        'type' => 'standard',
    ]);

    $menuItem = $menu->items()->create([
        'name' => 'Bread',
        'type' => 'meal',
    ]);

    $menuItem->recipes()->create([
        'inventory_item_id' => $inventory->id,
        'quantity_needed' => 50,
        'unit' => 'g',
    ]);

    $response = $this->actingAs($admin)
        ->from(route('admin.inventory.index'))
        ->put(route('admin.inventory.update', $inventory), [
            'name' => 'Flour',
            'qty' => 15,
            'unit' => 'liters',
            'expiry_date' => null,
            'category' => 'Perishable',
        ]);

    $response->assertRedirect(route('admin.inventory.index'));
    $response->assertSessionHasErrors('unit');

    expect($inventory->fresh()->unit)->toBe('g');
});

it('normalizes accepted inventory unit aliases on update', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $inventory = InventoryItem::create([
        'name' => 'Rice Sack',
        'qty' => 5,
        'unit' => 'g',
        'category' => 'Perishable',
    ]);

    $response = $this->actingAs($admin)
        ->from(route('admin.inventory.index'))
        ->put(route('admin.inventory.update', $inventory), [
            'name' => 'Rice Sack',
            'qty' => 5.125,
            'unit' => 'kg',
            'expiry_date' => null,
            'category' => 'Perishable',
        ]);

    $response->assertRedirect(route('admin.inventory.index'));
    expect($inventory->fresh()->unit)->toBe('kgs');
    expect((float) $inventory->fresh()->qty)->toEqualWithDelta(5.125, 0.000001);
});
