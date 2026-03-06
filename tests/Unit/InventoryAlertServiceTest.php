<?php

use App\Models\InventoryItem;
use App\Services\InventoryAlertService;
use Illuminate\Support\Collection;

function makeInventoryAlertService(array $items, bool $hasMinStockColumn = false): InventoryAlertService
{
    $service = new InventoryAlertService();

    $propertyValues = [
        'hasInventoryTable' => true,
        'hasMinStockColumn' => $hasMinStockColumn,
        'inventoryItems' => new Collection($items),
    ];

    foreach ($propertyValues as $propertyName => $value) {
        $property = new ReflectionProperty(InventoryAlertService::class, $propertyName);
        $property->setAccessible(true);
        $property->setValue($service, $value);
    }

    return $service;
}

it('uses min_stock before unit fallback when min_stock is present', function () {
    $item = new InventoryItem([
        'name' => 'Cooking Oil Reserve',
        'qty' => 0.85,
        'unit' => 'Liters',
        'category' => 'Condiments',
        'expiry_date' => null,
    ]);
    $item->setAttribute('min_stock', 1.0);

    $alerts = makeInventoryAlertService([$item], true)->getLowStocks();

    expect($alerts)->toHaveCount(1);
});

it('does not flag liters stock as low when above the liters fallback threshold', function () {
    $item = new InventoryItem([
        'name' => 'Cooking Oil Reserve',
        'qty' => 0.85,
        'unit' => 'Liters',
        'category' => 'Condiments',
        'expiry_date' => null,
    ]);

    $alerts = makeInventoryAlertService([$item])->getLowStocks();

    expect($alerts)->toHaveCount(0);
});

it('flags pieces stock as low when it falls below the pieces fallback threshold', function () {
    $item = new InventoryItem([
        'name' => 'Disposable Spoon Set',
        'qty' => 9,
        'unit' => 'Pieces',
        'category' => 'Others',
        'expiry_date' => null,
    ]);

    $alerts = makeInventoryAlertService([$item])->getLowStocks();

    expect($alerts)->toHaveCount(1);
});
