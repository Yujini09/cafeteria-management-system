<?php

use App\Models\InventoryItem;
use App\Models\Menu;
use App\Models\User;

it('blocks menu creation when recipe ingredients exceed current inventory stock', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $inventory = InventoryItem::create([
        'name' => 'Chicken Stock',
        'qty' => 1,
        'unit' => 'liters',
        'category' => 'Perishable',
    ]);

    $payload = [
        'type' => 'standard',
        'meal_time' => 'lunch',
        'description' => 'Stock-heavy menu',
        'items' => [
            [
                'name' => 'Soup',
                'type' => 'food',
                'recipes' => [
                    [
                        'inventory_item_id' => $inventory->id,
                        'quantity_needed' => 1500,
                        'unit' => 'ml',
                    ],
                ],
            ],
        ],
    ];

    $response = $this->actingAs($admin)->postJson(route('admin.menus.store'), $payload);

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('insufficient_inventory.0.id', $inventory->id)
        ->assertJsonPath('insufficient_inventory.0.name', 'Chicken Stock');

    expect((float) data_get($response->json(), 'insufficient_inventory.0.required'))
        ->toEqualWithDelta(1.5, 0.000001);
    expect((float) data_get($response->json(), 'insufficient_inventory.0.shortage'))
        ->toEqualWithDelta(0.5, 0.000001);

    expect(Menu::count())->toBe(0);
});
