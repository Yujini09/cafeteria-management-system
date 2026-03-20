<?php

use App\Models\InventoryItem;
use App\Models\Menu;
use Illuminate\Validation\ValidationException;

it('prevents auto-copying recipes when source unit is incompatible with stock unit', function () {
    $inventory = InventoryItem::create([
        'name' => 'Cooking Oil',
        'qty' => 10,
        'unit' => 'liters',
        'category' => 'Condiments',
    ]);

    $menuA = Menu::create([
        'name' => 'Menu A',
        'description' => 'Test',
        'price' => 100,
        'meal_time' => 'lunch',
        'type' => 'standard',
    ]);

    $menuB = Menu::create([
        'name' => 'Menu B',
        'description' => 'Test',
        'price' => 100,
        'meal_time' => 'dinner',
        'type' => 'standard',
    ]);

    $sourceItem = $menuA->items()->create([
        'name' => 'Fried Rice',
        'type' => 'meal',
    ]);

    $targetItem = $menuB->items()->create([
        'name' => 'Fried Rice',
        'type' => 'meal',
    ]);

    $sourceItem->recipes()->create([
        'inventory_item_id' => $inventory->id,
        'quantity_needed' => 100,
        'unit' => 'g', // incompatible with liters stock unit
    ]);

    expect(fn () => $targetItem->copyRecipesFrom($sourceItem))
        ->toThrow(ValidationException::class);

    expect($targetItem->recipes()->count())->toBe(0);
});
