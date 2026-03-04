<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class InventoryAlertsSeeder extends Seeder
{
    /**
     * Seed inventory items that trigger the dashboard inventory alert modal.
     */
    public function run(): void
    {
        if (!Schema::hasTable('inventory_items')) {
            $this->command?->warn('Skipping InventoryAlertsSeeder: inventory_items table is missing.');

            return;
        }

        $hasExpiryDateColumn = Schema::hasColumn('inventory_items', 'expiry_date');

        $items = [
            [
                'name' => 'Emergency Rice Stock',
                'qty' => 0,
                'unit' => 'kg',
                'category' => 'Others',
                'expiry_date' => null,
            ],
            [
                'name' => 'Cooking Oil Reserve',
                'qty' => 3,
                'unit' => 'liters',
                'category' => 'Condiments',
                'expiry_date' => null,
            ],
            [
                'name' => 'Soy Sauce Backup',
                'qty' => 4,
                'unit' => 'bottles',
                'category' => 'Condiments',
                'expiry_date' => null,
            ],
            [
                'name' => 'Sugar Refill Pack',
                'qty' => 2,
                'unit' => 'kg',
                'category' => 'Others',
                'expiry_date' => null,
            ],
            [
                'name' => 'Paper Cup Supply',
                'qty' => 5,
                'unit' => 'packs',
                'category' => 'Others',
                'expiry_date' => null,
            ],
            [
                'name' => 'Frozen Chicken Stock',
                'qty' => 1,
                'unit' => 'kg',
                'category' => 'Frozen',
                'expiry_date' => null,
            ],
            [
                'name' => 'Fresh Milk Batch A',
                'qty' => 12,
                'unit' => 'liters',
                'category' => 'Perishable',
                'expiry_date' => $hasExpiryDateColumn
                    ? Carbon::today()->addDays(5)->toDateString()
                    : null,
            ],
            [
                'name' => 'Fresh Milk Batch B',
                'qty' => 9,
                'unit' => 'liters',
                'category' => 'Perishable',
                'expiry_date' => $hasExpiryDateColumn
                    ? Carbon::today()->addDays(7)->toDateString()
                    : null,
            ],
        ];

        foreach ($items as $itemData) {
            $attributes = [
                'qty' => $itemData['qty'],
                'unit' => $itemData['unit'],
                'category' => $itemData['category'],
            ];

            if ($hasExpiryDateColumn) {
                $attributes['expiry_date'] = $itemData['expiry_date'];
            }

            InventoryItem::updateOrCreate(
                ['name' => $itemData['name']],
                $attributes
            );
        }
    }
}
