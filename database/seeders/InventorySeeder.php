<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('inventory_items')) {
            $this->command?->warn('Skipping InventorySeeder: inventory_items table is missing.');

            return;
        }

        foreach ($this->items() as $item) {
            InventoryItem::updateOrCreate(
                ['name' => $item['name']],
                [
                    'qty' => $item['qty'],
                    'unit' => $item['unit'],
                    'category' => $item['category'] ?? $this->categoryFor($item['name']),
                    'expiry_date' => null,
                ]
            );
        }
    }

    private function items(): array
    {
        return array_merge(
            $this->pieceItems(),
            $this->gramItems(),
            $this->milliliterItems(),
        );
    }

    private function pieceItems(): array
    {
        return [
            ['name' => 'Bottled Water', 'qty' => 640, 'unit' => 'pc'],
            ['name' => 'Distilled Water', 'qty' => 460, 'unit' => 'pc'],
            ['name' => 'Coffee Sachet', 'qty' => 880, 'unit' => 'pc'],
            ['name' => 'Tea Bag', 'qty' => 620, 'unit' => 'pc'],
            ['name' => 'Bread Slice', 'qty' => 2400, 'unit' => 'pc'],
            ['name' => 'Burger Bun', 'qty' => 360, 'unit' => 'pc'],
            ['name' => 'Banana', 'qty' => 220, 'unit' => 'pc'],
            ['name' => 'Saba Banana', 'qty' => 180, 'unit' => 'pc'],
            ['name' => 'Salted Egg', 'qty' => 140, 'unit' => 'pc'],
            ['name' => 'Quail Egg', 'qty' => 300, 'unit' => 'pc'],
            ['name' => 'Lumpia Wrapper', 'qty' => 950, 'unit' => 'pc'],
            ['name' => 'Sausage', 'qty' => 260, 'unit' => 'pc'],
            ['name' => 'Leche Flan Cup', 'qty' => 120, 'unit' => 'pc'],
            ['name' => 'Fruit Salad Cup', 'qty' => 110, 'unit' => 'pc'],
            ['name' => 'Fruit Cocktail Cup', 'qty' => 110, 'unit' => 'pc'],
            ['name' => 'Buko Pandan Cup', 'qty' => 110, 'unit' => 'pc'],
        ];
    }

    private function gramItems(): array
    {
        return [
            ['name' => 'Rice (Uncooked)', 'qty' => 182000, 'unit' => 'g'],
            ['name' => 'Assorted Fruit', 'qty' => 94000, 'unit' => 'g'],
            ['name' => 'Sugar', 'qty' => 126000, 'unit' => 'g'],
            ['name' => 'Brown Sugar', 'qty' => 34000, 'unit' => 'g'],
            ['name' => 'Creamer', 'qty' => 58000, 'unit' => 'g'],
            ['name' => 'Gulaman Powder', 'qty' => 6200, 'unit' => 'g'],
            ['name' => 'Sago Pearls', 'qty' => 16000, 'unit' => 'g'],
            ['name' => 'Butter', 'qty' => 18000, 'unit' => 'g'],
            ['name' => 'Jam', 'qty' => 15000, 'unit' => 'g'],
            ['name' => 'Longanisa', 'qty' => 52000, 'unit' => 'g'],
            ['name' => 'Egg', 'qty' => 85000, 'unit' => 'g'],
            ['name' => 'Tomato', 'qty' => 56000, 'unit' => 'g'],
            ['name' => 'Ground Pork', 'qty' => 92000, 'unit' => 'g'],
            ['name' => 'Ground Pork/Beef', 'qty' => 54000, 'unit' => 'g'],
            ['name' => 'Ground Beef/Pork', 'qty' => 52000, 'unit' => 'g'],
            ['name' => 'Ground Chicken', 'qty' => 42000, 'unit' => 'g'],
            ['name' => 'Ground Fish', 'qty' => 36000, 'unit' => 'g'],
            ['name' => 'Breadcrumbs', 'qty' => 26000, 'unit' => 'g'],
            ['name' => 'Raisins', 'qty' => 4800, 'unit' => 'g'],
            ['name' => 'Carrot', 'qty' => 36000, 'unit' => 'g'],
            ['name' => 'Onion', 'qty' => 61000, 'unit' => 'g'],
            ['name' => 'Garlic', 'qty' => 26000, 'unit' => 'g'],
            ['name' => 'Ginger', 'qty' => 16000, 'unit' => 'g'],
            ['name' => 'Pepper', 'qty' => 2500, 'unit' => 'g'],
            ['name' => 'Luncheon Meat', 'qty' => 32000, 'unit' => 'g'],
            ['name' => 'Dilis', 'qty' => 15000, 'unit' => 'g'],
            ['name' => 'Pork Strips', 'qty' => 52000, 'unit' => 'g'],
            ['name' => 'Pork Cutlets', 'qty' => 26000, 'unit' => 'g'],
            ['name' => 'Pork', 'qty' => 110000, 'unit' => 'g'],
            ['name' => 'Pork Belly', 'qty' => 32000, 'unit' => 'g'],
            ['name' => 'Pork Face/Shoulder', 'qty' => 26000, 'unit' => 'g'],
            ['name' => 'Pork Liver', 'qty' => 16000, 'unit' => 'g'],
            ['name' => 'Chicken', 'qty' => 98000, 'unit' => 'g'],
            ['name' => 'Chicken Breast', 'qty' => 46000, 'unit' => 'g'],
            ['name' => 'Chicken Fillet', 'qty' => 42000, 'unit' => 'g'],
            ['name' => 'Chicken/Pork', 'qty' => 40000, 'unit' => 'g'],
            ['name' => 'Bangus', 'qty' => 42000, 'unit' => 'g'],
            ['name' => 'Bangus Fillet', 'qty' => 42000, 'unit' => 'g'],
            ['name' => 'Tilapia', 'qty' => 48000, 'unit' => 'g'],
            ['name' => 'Fish', 'qty' => 65000, 'unit' => 'g'],
            ['name' => 'Fish Fillet', 'qty' => 65000, 'unit' => 'g'],
            ['name' => 'Hito', 'qty' => 22000, 'unit' => 'g'],
            ['name' => 'Shrimp', 'qty' => 42000, 'unit' => 'g'],
            ['name' => 'Crab Meat', 'qty' => 12000, 'unit' => 'g'],
            ['name' => 'Ham', 'qty' => 16000, 'unit' => 'g'],
            ['name' => 'Cheese', 'qty' => 32000, 'unit' => 'g'],
            ['name' => 'Mayonnaise', 'qty' => 26000, 'unit' => 'g'],
            ['name' => 'Young Coconut Strips', 'qty' => 22000, 'unit' => 'g'],
            ['name' => 'Mushroom', 'qty' => 26000, 'unit' => 'g'],
            ['name' => 'Eggplant', 'qty' => 32000, 'unit' => 'g'],
            ['name' => 'Bell Pepper', 'qty' => 18000, 'unit' => 'g'],
            ['name' => 'Celery', 'qty' => 4800, 'unit' => 'g'],
            ['name' => 'Cabbage', 'qty' => 35000, 'unit' => 'g'],
            ['name' => 'Sayote', 'qty' => 28000, 'unit' => 'g'],
            ['name' => 'Pechay', 'qty' => 15000, 'unit' => 'g'],
            ['name' => 'Sitaw', 'qty' => 18000, 'unit' => 'g'],
            ['name' => 'Chinese Vegetables', 'qty' => 26000, 'unit' => 'g'],
            ['name' => 'Mixed Vegetables', 'qty' => 26000, 'unit' => 'g'],
            ['name' => 'Mixed Pinakbet Vegetables', 'qty' => 32000, 'unit' => 'g'],
            ['name' => 'Assorted Lagalaga Vegetables', 'qty' => 22000, 'unit' => 'g'],
            ['name' => 'Native Vegetables', 'qty' => 24000, 'unit' => 'g'],
            ['name' => 'Green Beans', 'qty' => 16000, 'unit' => 'g'],
            ['name' => 'Green Mango', 'qty' => 14000, 'unit' => 'g'],
            ['name' => 'Radish', 'qty' => 16000, 'unit' => 'g'],
            ['name' => 'Kangkong', 'qty' => 12000, 'unit' => 'g'],
            ['name' => 'Malunggay Leaves', 'qty' => 6000, 'unit' => 'g'],
            ['name' => 'Chili Leaves', 'qty' => 6000, 'unit' => 'g'],
            ['name' => 'Sigarilyas', 'qty' => 18000, 'unit' => 'g'],
            ['name' => 'Patola', 'qty' => 12000, 'unit' => 'g'],
            ['name' => 'Togue', 'qty' => 16000, 'unit' => 'g'],
            ['name' => 'Tofu', 'qty' => 10000, 'unit' => 'g'],
            ['name' => 'Potato', 'qty' => 34000, 'unit' => 'g'],
            ['name' => 'Pimiento', 'qty' => 5000, 'unit' => 'g'],
            ['name' => 'Bagoong', 'qty' => 18000, 'unit' => 'g'],
            ['name' => 'Peanut Butter', 'qty' => 12000, 'unit' => 'g'],
            ['name' => 'Liver Spread', 'qty' => 6000, 'unit' => 'g'],
            ['name' => 'Buro', 'qty' => 10000, 'unit' => 'g'],
            ['name' => 'Chili', 'qty' => 3200, 'unit' => 'g'],
            ['name' => 'Breading Mix', 'qty' => 18000, 'unit' => 'g'],
            ['name' => 'Annatto', 'qty' => 900, 'unit' => 'g'],
            ['name' => 'Bay Leaf', 'qty' => 300, 'unit' => 'g'],
            ['name' => 'Star Anise', 'qty' => 250, 'unit' => 'g'],
            ['name' => 'Cornstarch', 'qty' => 16000, 'unit' => 'g'],
            ['name' => 'Corn Kernels', 'qty' => 14000, 'unit' => 'g'],
            ['name' => 'Cream Corn', 'qty' => 18000, 'unit' => 'g'],
            ['name' => 'Lomi Noodles', 'qty' => 30000, 'unit' => 'g'],
            ['name' => 'Bihon', 'qty' => 30000, 'unit' => 'g'],
            ['name' => 'Sotanghon', 'qty' => 24000, 'unit' => 'g'],
            ['name' => 'Misua', 'qty' => 14000, 'unit' => 'g'],
            ['name' => 'Batchoy Noodles', 'qty' => 28000, 'unit' => 'g'],
            ['name' => 'Spaghetti Pasta', 'qty' => 42000, 'unit' => 'g'],
            ['name' => 'Pasta', 'qty' => 36000, 'unit' => 'g'],
            ['name' => 'Hotdog', 'qty' => 18000, 'unit' => 'g'],
            ['name' => 'Bacon/Ham', 'qty' => 16000, 'unit' => 'g'],
            ['name' => 'Rice Flour', 'qty' => 30000, 'unit' => 'g'],
            ['name' => 'All-Purpose Flour', 'qty' => 42000, 'unit' => 'g'],
            ['name' => 'Flour', 'qty' => 50000, 'unit' => 'g'],
            ['name' => 'Baking Powder', 'qty' => 1800, 'unit' => 'g'],
            ['name' => 'Grated Coconut', 'qty' => 12000, 'unit' => 'g'],
            ['name' => 'Spring Onion', 'qty' => 9000, 'unit' => 'g'],
            ['name' => 'Lemongrass', 'qty' => 5200, 'unit' => 'g'],
            ['name' => 'Tuna Flakes', 'qty' => 16000, 'unit' => 'g'],
            ['name' => 'Sampalok Mix', 'qty' => 4500, 'unit' => 'g'],
        ];
    }

    private function milliliterItems(): array
    {
        return [
            ['name' => 'Water', 'qty' => 420000, 'unit' => 'ml'],
            ['name' => 'Soy Sauce', 'qty' => 32000, 'unit' => 'ml'],
            ['name' => 'Calamansi Juice', 'qty' => 12000, 'unit' => 'ml'],
            ['name' => 'Vinegar', 'qty' => 22000, 'unit' => 'ml'],
            ['name' => 'Tomato Sauce', 'qty' => 42000, 'unit' => 'ml'],
            ['name' => 'Banana Ketchup', 'qty' => 26000, 'unit' => 'ml'],
            ['name' => 'Catsup', 'qty' => 16000, 'unit' => 'ml'],
            ['name' => 'Sweet Chili Sauce', 'qty' => 12000, 'unit' => 'ml'],
            ['name' => 'Pineapple Juice', 'qty' => 12000, 'unit' => 'ml'],
            ['name' => 'Milk', 'qty' => 42000, 'unit' => 'ml'],
            ['name' => 'All-Purpose Cream', 'qty' => 22000, 'unit' => 'ml'],
            ['name' => 'Coconut Milk', 'qty' => 26000, 'unit' => 'ml'],
            ['name' => 'Coco Cream', 'qty' => 12000, 'unit' => 'ml'],
            ['name' => 'Oyster Sauce', 'qty' => 8000, 'unit' => 'ml'],
            ['name' => 'Liver Sauce', 'qty' => 8000, 'unit' => 'ml'],
            ['name' => 'Lemon Juice', 'qty' => 3500, 'unit' => 'ml'],
            ['name' => 'Lye Water', 'qty' => 1200, 'unit' => 'ml'],
            ['name' => 'P/A Juice', 'qty' => 36000, 'unit' => 'ml'],
            ['name' => 'P/A Orange Juice', 'qty' => 22000, 'unit' => 'ml'],
            ['name' => 'Orange Juice', 'qty' => 24000, 'unit' => 'ml'],
            ['name' => 'Buko Juice', 'qty' => 22000, 'unit' => 'ml'],
            ['name' => '4 Season Juice', 'qty' => 22000, 'unit' => 'ml'],
        ];
    }

    private function categoryFor(string $name): string
    {
        if (in_array($name, [
            'Bottled Water',
            'Distilled Water',
            'P/A Juice',
            'P/A Orange Juice',
            'Orange Juice',
            'Buko Juice',
            '4 Season Juice',
        ], true)) {
            return 'Beverages';
        }

        if (in_array($name, [
            'Leche Flan Cup',
            'Fruit Salad Cup',
            'Fruit Cocktail Cup',
            'Buko Pandan Cup',
            'Gulaman Powder',
            'Sago Pearls',
            'Young Coconut Strips',
            'Jam',
        ], true)) {
            return 'Desserts';
        }

        if (in_array($name, [
            'Sugar',
            'Brown Sugar',
            'Soy Sauce',
            'Vinegar',
            'Tomato Sauce',
            'Banana Ketchup',
            'Catsup',
            'Sweet Chili Sauce',
            'Mayonnaise',
            'Peanut Butter',
            'Bagoong',
            'Liver Sauce',
            'Oyster Sauce',
            'Pepper',
            'Annatto',
            'Bay Leaf',
            'Star Anise',
            'Lye Water',
        ], true)) {
            return 'Condiments';
        }

        if (in_array($name, [
            'Longanisa',
            'Egg',
            'Luncheon Meat',
            'Dilis',
            'Pork Strips',
            'Pork Cutlets',
            'Pork',
            'Pork Belly',
            'Pork Face/Shoulder',
            'Pork Liver',
            'Ground Pork',
            'Ground Pork/Beef',
            'Ground Beef/Pork',
            'Ground Chicken',
            'Ground Fish',
            'Chicken',
            'Chicken Breast',
            'Chicken Fillet',
            'Chicken/Pork',
            'Bangus',
            'Bangus Fillet',
            'Tilapia',
            'Fish',
            'Fish Fillet',
            'Hito',
            'Shrimp',
            'Crab Meat',
            'Ham',
            'Cheese',
            'Butter',
            'Milk',
            'All-Purpose Cream',
            'Coconut Milk',
            'Coco Cream',
            'Tomato',
            'Carrot',
            'Onion',
            'Garlic',
            'Ginger',
            'Mushroom',
            'Eggplant',
            'Bell Pepper',
            'Celery',
            'Cabbage',
            'Sayote',
            'Pechay',
            'Sitaw',
            'Chinese Vegetables',
            'Mixed Vegetables',
            'Mixed Pinakbet Vegetables',
            'Assorted Lagalaga Vegetables',
            'Native Vegetables',
            'Green Beans',
            'Green Mango',
            'Radish',
            'Kangkong',
            'Malunggay Leaves',
            'Chili Leaves',
            'Sigarilyas',
            'Patola',
            'Togue',
            'Tofu',
            'Potato',
            'Pimiento',
            'Banana',
            'Saba Banana',
            'Salted Egg',
            'Quail Egg',
            'Sausage',
            'Creamer',
            'Assorted Fruit',
            'Corn Kernels',
            'Cream Corn',
            'Spring Onion',
            'Lemongrass',
            'Tuna Flakes',
        ], true)) {
            return 'Perishable';
        }

        return 'Others';
    }
}
