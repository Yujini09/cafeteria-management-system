<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\MenuItem;
use App\Support\RecipeUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('recipes') || !Schema::hasTable('menu_items') || !Schema::hasTable('inventory_items')) {
            $this->command?->warn('Skipping RecipeSeeder: recipes/menu_items/inventory_items table is missing.');

            return;
        }

        $recipeMap = $this->recipeMap();
        $menuItemsByName = MenuItem::query()->get()->groupBy('name');
        $inventoryIdsByName = InventoryItem::query()->pluck('id', 'name');

        $missingMenuNames = collect(array_keys($recipeMap))
            ->filter(fn (string $name) => !$menuItemsByName->has($name))
            ->values()
            ->all();

        if ($missingMenuNames !== []) {
            throw new RuntimeException(
                'RecipeSeeder is out of sync with menu items. Missing menu item names: ' . implode(', ', $missingMenuNames)
            );
        }

        $currentMenuNames = $menuItemsByName->keys()->sort()->values();
        $mappedMenuNames = collect(array_keys($recipeMap))->sort()->values();
        $unmappedMenuNames = $currentMenuNames->diff($mappedMenuNames)->values()->all();

        if ($unmappedMenuNames !== []) {
            throw new RuntimeException(
                'RecipeSeeder does not cover all current menu items. Unmapped menu item names: ' . implode(', ', $unmappedMenuNames)
            );
        }

        $missingInventoryNames = collect($recipeMap)
            ->flatten(1)
            ->pluck(0)
            ->unique()
            ->filter(fn (string $name) => !$inventoryIdsByName->has($name))
            ->values()
            ->all();

        if ($missingInventoryNames !== []) {
            throw new RuntimeException(
                'RecipeSeeder is out of sync with inventory items. Missing inventory item names: ' . implode(', ', $missingInventoryNames)
            );
        }

        DB::transaction(function () use ($recipeMap, $menuItemsByName, $inventoryIdsByName): void {
            DB::table('recipes')->delete();

            foreach ($recipeMap as $menuItemName => $ingredients) {
                /** @var Collection<int, MenuItem> $menuItems */
                $menuItems = $menuItemsByName->get($menuItemName, collect());

                foreach ($menuItems as $menuItem) {
                    foreach ($ingredients as [$inventoryName, $quantityNeeded, $unit]) {
                        $normalizedUnit = RecipeUnit::normalize($unit);

                        if (!RecipeUnit::isAllowedRecipeUnit($normalizedUnit)) {
                            throw new RuntimeException("Invalid recipe unit [{$unit}] for menu item [{$menuItemName}].");
                        }

                        DB::table('recipes')->insert([
                            'menu_item_id' => $menuItem->id,
                            'inventory_item_id' => $inventoryIdsByName->get($inventoryName),
                            'quantity_needed' => $quantityNeeded,
                            'unit' => $normalizedUnit,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });
    }

    private function recipeMap(): array
    {
        return [
            'Rice' => [['Rice (Uncooked)', 125, 'g']],
            'Tea/Coffee' => [['Coffee Sachet', 1, 'pc'], ['Sugar', 15, 'g'], ['Creamer', 20, 'g']],
            'Bottled Water' => [['Bottled Water', 1, 'pc']],
            'Distilled Water' => [['Distilled Water', 1, 'pc']],
            'Fruit in Season' => [['Assorted Fruit', 150, 'g']],
            'Molded Gulaman' => [['Gulaman Powder', 2.5, 'g'], ['Sugar', 25, 'g'], ['Water', 200, 'ml']],
            'Black Gulaman' => [['Gulaman Powder', 2.5, 'g'], ['Brown Sugar', 30, 'g'], ['Water', 250, 'ml']],
            'Sago\'t Gulaman' => [['Sago Pearls', 25, 'g'], ['Gulaman Powder', 2.5, 'g'], ['Brown Sugar', 30, 'g'], ['Water', 300, 'ml']],
            'Toasted Bread' => [['Bread Slice', 2, 'pc'], ['Butter', 10, 'g']],
            'Banana' => [['Banana', 1, 'pc']],
            'Slice Fruits' => [['Assorted Fruit', 150, 'g']],
            'Leche Flan' => [['Leche Flan Cup', 1, 'pc']],
            'Fruit Salad' => [['Fruit Salad Cup', 1, 'pc']],
            'Fruit Cocktail' => [['Fruit Cocktail Cup', 1, 'pc']],
            'Buko Pandan' => [['Buko Pandan Cup', 1, 'pc']],
            'P/A Juice' => [['P/A Juice', 200, 'ml']],
            'P/A Orange Juice' => [['P/A Orange Juice', 200, 'ml']],
            'Orange Juice' => [['Orange Juice', 200, 'ml']],
            'Buko Juice' => [['Buko Juice', 200, 'ml']],
            '4 Season Juice' => [['4 Season Juice', 200, 'ml']],
            'Iced Tea' => [['Tea Bag', 1, 'pc'], ['Sugar', 20, 'g'], ['Water', 200, 'ml']],
            'Iced Tea w/ Tanglad' => [['Tea Bag', 1, 'pc'], ['Lemongrass', 10, 'g'], ['Sugar', 20, 'g'], ['Water', 200, 'ml']],
            'Iced Tea w/ Lemon' => [['Tea Bag', 1, 'pc'], ['Sugar', 20, 'g'], ['Lemon Juice', 10, 'ml'], ['Water', 200, 'ml']],

            'Longanisa w/ Slice Tomato' => [['Longanisa', 120, 'g'], ['Tomato', 80, 'g']],
            'Fried Egg Sunny Side Up' => [['Egg', 50, 'g']],
            'Pork Embutido' => [['Ground Pork', 100, 'g'], ['Breadcrumbs', 15, 'g'], ['Egg', 10, 'g'], ['Raisins', 8, 'g'], ['Carrot', 15, 'g']],
            'Onion Omelet' => [['Egg', 50, 'g'], ['Onion', 50, 'g']],
            'Luncheon Meat' => [['Luncheon Meat', 70, 'g']],
            'Dilis w/ Chopped Tomato' => [['Dilis', 30, 'g'], ['Tomato', 70, 'g'], ['Onion', 15, 'g']],
            'Pork Tapa w/ Tomato' => [['Pork Strips', 120, 'g'], ['Soy Sauce', 12, 'ml'], ['Calamansi Juice', 10, 'ml'], ['Garlic', 5, 'g'], ['Sugar', 4, 'g'], ['Pepper', 1, 'g'], ['Tomato', 70, 'g']],
            'Salted Egg' => [['Salted Egg', 1, 'pc']],
            'Boneless Daing na Bangus' => [['Bangus Fillet', 150, 'g'], ['Vinegar', 8, 'ml'], ['Garlic', 4, 'g']],
            'Mushroom Omelet' => [['Mushroom', 30, 'g'], ['Egg', 50, 'g']],
            'Pork Omelet w/ Catsup' => [['Ground Pork', 70, 'g'], ['Egg', 50, 'g'], ['Catsup', 25, 'ml']],
            'Fried Eggplant' => [['Eggplant', 150, 'g']],
            'Toasted Bread w/ Jam&Butter' => [['Bread Slice', 2, 'pc'], ['Jam', 25, 'g'], ['Butter', 20, 'g']],
            'Chicken Embutido' => [['Ground Chicken', 100, 'g'], ['Breadcrumbs', 15, 'g'], ['Egg', 10, 'g'], ['Carrot', 15, 'g'], ['Bell Pepper', 10, 'g']],
            'Fried Sausage' => [['Sausage', 2, 'pc']],
            'Nilagang Saba' => [['Saba Banana', 2, 'pc']],
            'Daing Dilis' => [['Dilis', 25, 'g']],

            'Ham & Cheese Sandwich' => [['Bread Slice', 2, 'pc'], ['Ham', 40, 'g'], ['Cheese', 20, 'g'], ['Mayonnaise', 15, 'g']],
            'Buko w/ Gulaman' => [['Young Coconut Strips', 50, 'g'], ['Gulaman Powder', 2.5, 'g'], ['Sugar', 25, 'g'], ['Water', 200, 'ml']],
            'Pimiento Sandwich' => [['Bread Slice', 2, 'pc'], ['Cheese', 50, 'g'], ['Mayonnaise', 25, 'g'], ['Pimiento', 20, 'g']],
            'Chicken Sandwich' => [['Bread Slice', 2, 'pc'], ['Chicken Breast', 70, 'g'], ['Mayonnaise', 25, 'g'], ['Onion', 5, 'g'], ['Celery', 5, 'g']],
            'Cheese Burger' => [['Burger Bun', 1, 'pc'], ['Ground Beef/Pork', 100, 'g'], ['Garlic', 3, 'g'], ['Onion', 10, 'g'], ['Breadcrumbs', 10, 'g'], ['Cheese', 20, 'g']],
            'Lomi' => [['Lomi Noodles', 100, 'g'], ['Chicken/Pork', 50, 'g'], ['Garlic', 4, 'g'], ['Onion', 15, 'g'], ['Cabbage', 40, 'g'], ['Carrot', 15, 'g'], ['Egg', 25, 'g'], ['Cornstarch', 10, 'g'], ['Spring Onion', 10, 'g']],
            'Puto Cheese' => [['Rice Flour', 50, 'g'], ['Sugar', 20, 'g'], ['Baking Powder', 2, 'g'], ['Cheese', 10, 'g']],
            'Bihon Guisado' => [['Bihon', 75, 'g'], ['Chicken/Pork', 50, 'g'], ['Garlic', 3, 'g'], ['Onion', 15, 'g'], ['Carrot', 15, 'g'], ['Cabbage', 30, 'g'], ['Soy Sauce', 10, 'ml']],
            'Kutsinta w/ Latik' => [['Rice Flour', 50, 'g'], ['All-Purpose Flour', 25, 'g'], ['Brown Sugar', 30, 'g'], ['Lye Water', 1.5, 'ml'], ['Annatto', 1, 'g'], ['Grated Coconut', 20, 'g']],
            'Spaghetti w/ Meat Balls' => [['Spaghetti Pasta', 100, 'g'], ['Ground Pork/Beef', 70, 'g'], ['Hotdog', 30, 'g'], ['Tomato Sauce', 100, 'ml'], ['Banana Ketchup', 50, 'ml'], ['Sugar', 10, 'g'], ['Breadcrumbs', 10, 'g'], ['Egg', 5, 'g']],
            'Carbonara w/ Chicken Fillet' => [['Pasta', 100, 'g'], ['Bacon/Ham', 30, 'g'], ['All-Purpose Cream', 50, 'ml'], ['Milk', 50, 'ml'], ['Cheese', 20, 'g'], ['Chicken Fillet', 100, 'g'], ['Flour', 15, 'g'], ['Egg', 10, 'g'], ['Breadcrumbs', 20, 'g']],

            'Chickenn Soup' => [['Chicken', 120, 'g'], ['Onion', 20, 'g'], ['Garlic', 5, 'g'], ['Ginger', 8, 'g'], ['Sayote', 50, 'g']],
            'Pork Karekare w/ Binagoongan' => [['Pork', 150, 'g'], ['Peanut Butter', 30, 'g'], ['Eggplant', 60, 'g'], ['Sitaw', 40, 'g'], ['Pechay', 30, 'g'], ['Annatto', 2, 'g'], ['Bagoong', 20, 'g']],
            'Lumpia Frito' => [['Lumpia Wrapper', 2, 'pc'], ['Ground Pork', 50, 'g'], ['Carrot', 10, 'g']],
            'Bolabola w/ P/A Sauce' => [['Ground Pork/Beef', 70, 'g'], ['Breadcrumbs', 10, 'g'], ['Egg', 5, 'g'], ['Pineapple Juice', 25, 'ml'], ['Catsup', 15, 'ml'], ['Sugar', 5, 'g']],
            'Crab & Corn Soup' => [['Crab Meat', 30, 'g'], ['Cream Corn', 60, 'g'], ['Egg', 15, 'g']],
            'Pork w/ Mushroom' => [['Pork Strips', 100, 'g'], ['Mushroom', 30, 'g'], ['Soy Sauce', 10, 'ml']],
            'Chinese Veg. w/ Quail Egg' => [['Chinese Vegetables', 80, 'g'], ['Quail Egg', 2, 'pc']],
            'Fish Fillet w/ Sweet Chilli Sauce' => [['Fish Fillet', 120, 'g'], ['Sweet Chili Sauce', 30, 'ml']],
            'Onion Soup' => [['Onion', 100, 'g']],
            'Cordon Bleu w/Creamy Mushroom-Sauce' => [['Chicken Breast', 120, 'g'], ['Ham', 20, 'g'], ['Cheese', 20, 'g'], ['Flour', 15, 'g'], ['Egg', 10, 'g'], ['Breadcrumbs', 25, 'g'], ['Mushroom', 25, 'g'], ['All-Purpose Cream', 40, 'ml']],
            'Pork Bistick' => [['Pork', 100, 'g'], ['Soy Sauce', 12, 'ml'], ['Calamansi Juice', 12, 'ml']],
            'Toge Guisado' => [['Togue', 80, 'g'], ['Carrot', 15, 'g'], ['Tofu', 30, 'g']],
            'Corn Soup' => [['Cream Corn', 60, 'g'], ['Egg', 15, 'g']],
            'Pork Sarciado' => [['Pork', 100, 'g'], ['Tomato', 50, 'g'], ['Onion', 25, 'g'], ['Egg', 15, 'g']],
            'Gising-gising' => [['Sigarilyas', 80, 'g'], ['Coconut Milk', 50, 'ml'], ['Chili', 3, 'g'], ['Ground Pork', 30, 'g']],
            'Fish Bolabola' => [['Ground Fish', 100, 'g'], ['Breadcrumbs', 10, 'g'], ['Egg', 5, 'g']],
            'Egg Drop Soup' => [['Egg', 20, 'g']],
            'Pork Caldereta' => [['Pork', 150, 'g'], ['Tomato Sauce', 50, 'ml'], ['Liver Spread', 20, 'g'], ['Potato', 50, 'g'], ['Carrot', 30, 'g'], ['Bell Pepper', 20, 'g']],
            'Chinese Vegetables' => [['Chinese Vegetables', 80, 'g'], ['Oyster Sauce', 8, 'ml']],
            'Sweet and Sour Fish' => [['Fish', 120, 'g'], ['Vinegar', 10, 'ml'], ['Sugar', 10, 'g'], ['Bell Pepper', 15, 'g'], ['Onion', 15, 'g']],
            'Steamed Veg. w/Butter Garlic Sauce' => [['Mixed Vegetables', 100, 'g'], ['Butter', 10, 'g'], ['Garlic', 5, 'g']],
            'Chicken Pork Adobo w/Coco Cream' => [['Chicken', 80, 'g'], ['Pork', 70, 'g'], ['Soy Sauce', 15, 'ml'], ['Vinegar', 12, 'ml'], ['Garlic', 10, 'g'], ['Bay Leaf', 0.5, 'g'], ['Coco Cream', 25, 'ml']],
            'Fish Escabeche' => [['Fish', 120, 'g'], ['Vinegar', 10, 'ml'], ['Sugar', 8, 'g'], ['Carrot', 15, 'g'], ['Bell Pepper', 15, 'g'], ['Onion', 15, 'g']],
            'Sinigang na Hipon' => [['Shrimp', 120, 'g'], ['Tomato', 40, 'g'], ['Onion', 20, 'g'], ['Radish', 40, 'g'], ['Kangkong', 40, 'g'], ['Sampalok Mix', 8, 'g']],
            'Fried Chicken' => [['Chicken', 150, 'g'], ['Calamansi Juice', 8, 'ml'], ['Garlic', 5, 'g'], ['Breading Mix', 25, 'g']],
            'Bolabola Fish w/ Misua' => [['Ground Fish', 100, 'g'], ['Misua', 15, 'g'], ['Patola', 50, 'g'], ['Onion', 15, 'g'], ['Garlic', 4, 'g']],
            'Pork Karekare' => [['Pork', 150, 'g'], ['Peanut Butter', 30, 'g'], ['Eggplant', 60, 'g'], ['Sitaw', 40, 'g'], ['Pechay', 30, 'g'], ['Bagoong', 20, 'g']],
            'Lumpia Shanghai' => [['Lumpia Wrapper', 3, 'pc'], ['Ground Pork', 70, 'g'], ['Carrot', 10, 'g']],
            'Breaded Chicken w/ P/A Sauce' => [['Chicken Fillet', 100, 'g'], ['Flour', 15, 'g'], ['Egg', 10, 'g'], ['Breadcrumbs', 20, 'g'], ['Pineapple Juice', 25, 'ml']],

            'Cheese Burger Sandwich' => [['Burger Bun', 1, 'pc'], ['Ground Beef/Pork', 100, 'g'], ['Cheese', 20, 'g'], ['Garlic', 3, 'g'], ['Onion', 10, 'g'], ['Breadcrumbs', 10, 'g']],
            'Tuna Sandwich' => [['Bread Slice', 2, 'pc'], ['Tuna Flakes', 50, 'g'], ['Mayonnaise', 25, 'g'], ['Onion', 8, 'g']],
            'Cheese Pimiento Sandwich' => [['Bread Slice', 2, 'pc'], ['Cheese', 50, 'g'], ['Mayonnaise', 25, 'g'], ['Pimiento', 20, 'g']],
            'Carbonara' => [['Pasta', 100, 'g'], ['Bacon/Ham', 30, 'g'], ['All-Purpose Cream', 50, 'ml'], ['Milk', 50, 'ml'], ['Cheese', 20, 'g']],
            'Sotanghon Guisado' => [['Sotanghon', 75, 'g'], ['Chicken/Pork', 50, 'g'], ['Garlic', 3, 'g'], ['Onion', 15, 'g'], ['Carrot', 15, 'g'], ['Cabbage', 30, 'g'], ['Soy Sauce', 10, 'ml']],
            'Maja' => [['Coconut Milk', 50, 'ml'], ['Cornstarch', 15, 'g'], ['Sugar', 25, 'g'], ['Corn Kernels', 30, 'g']],
            'Spaghetti' => [['Spaghetti Pasta', 100, 'g'], ['Ground Pork/Beef', 70, 'g'], ['Hotdog', 30, 'g'], ['Tomato Sauce', 100, 'ml'], ['Banana Ketchup', 50, 'ml'], ['Sugar', 10, 'g']],
            'Garlic Bread' => [['Bread Slice', 2, 'pc'], ['Butter', 10, 'g'], ['Garlic', 4, 'g']],

            'Mushroom Cabbage w/ Pork Balls' => [['Mushroom', 30, 'g'], ['Cabbage', 70, 'g'], ['Ground Pork', 70, 'g'], ['Breadcrumbs', 10, 'g'], ['Egg', 5, 'g']],
            'Chicken Caldereta' => [['Chicken', 150, 'g'], ['Tomato Sauce', 50, 'ml'], ['Liver Spread', 20, 'g'], ['Potato', 50, 'g'], ['Carrot', 30, 'g'], ['Bell Pepper', 15, 'g']],
            'Fried Tilapia' => [['Tilapia', 150, 'g']],
            'Bolabola w/ Misua' => [['Ground Pork', 70, 'g'], ['Misua', 15, 'g'], ['Onion', 15, 'g'], ['Garlic', 4, 'g']],
            'Breaded Pork' => [['Pork Cutlets', 120, 'g'], ['Flour', 15, 'g'], ['Egg', 10, 'g'], ['Breadcrumbs', 20, 'g']],
            'Bean w/ Ham Strips' => [['Green Beans', 70, 'g'], ['Ham', 30, 'g']],
            'Fried Bangus' => [['Bangus', 150, 'g']],
            'Batchoy w/ Meat' => [['Pork', 70, 'g'], ['Pork Liver', 25, 'g'], ['Batchoy Noodles', 75, 'g'], ['Garlic', 6, 'g'], ['Onion', 15, 'g']],
            'Pinakbet' => [['Mixed Pinakbet Vegetables', 120, 'g'], ['Bagoong', 15, 'g']],
            'Fried Hito' => [['Hito', 150, 'g']],
            'Chicken Tinola' => [['Chicken', 150, 'g'], ['Ginger', 8, 'g'], ['Garlic', 4, 'g'], ['Onion', 15, 'g'], ['Sayote', 100, 'g'], ['Malunggay Leaves', 20, 'g']],
            'Inihaw na Tilapia' => [['Tilapia', 150, 'g']],
            'Lagalaga Veg. w/ Buro' => [['Assorted Lagalaga Vegetables', 120, 'g'], ['Buro', 25, 'g']],
            'Chicken Swam' => [['Chicken', 120, 'g'], ['Native Vegetables', 100, 'g']],
            'Broiled Fish w/ Mango Salad' => [['Fish', 120, 'g'], ['Green Mango', 40, 'g'], ['Onion', 15, 'g']],
            'Lechon Kawali w/ Sauce' => [['Pork Belly', 150, 'g'], ['Liver Sauce', 25, 'ml']],
            'Lagalaga Veg. Delight w/ Buro' => [['Assorted Lagalaga Vegetables', 100, 'g'], ['Buro', 25, 'g']],
            'Sinampalukang Manok' => [['Chicken', 150, 'g'], ['Sampalok Mix', 8, 'g'], ['Tomato', 40, 'g'], ['Onion', 20, 'g'], ['Ginger', 6, 'g']],
            'Pork Sisig' => [['Pork Face/Shoulder', 120, 'g'], ['Onion', 20, 'g'], ['Chili', 3, 'g'], ['Calamansi Juice', 8, 'ml'], ['Mayonnaise', 15, 'g']],
            'Batchoy Soup' => [['Pork', 70, 'g'], ['Pork Liver', 25, 'g'], ['Batchoy Noodles', 75, 'g'], ['Garlic', 6, 'g']],
            'Fried Tilapia w/ Mango Sisig' => [['Tilapia', 150, 'g'], ['Green Mango', 40, 'g'], ['Onion', 15, 'g']],
            'Broiled Eggplant w/ Binagoongan' => [['Eggplant', 100, 'g'], ['Bagoong', 20, 'g']],
            'Chicken Barbeque' => [['Chicken', 120, 'g'], ['Soy Sauce', 12, 'ml'], ['Banana Ketchup', 15, 'ml'], ['Calamansi Juice', 8, 'ml'], ['Garlic', 4, 'g']],
            'Tinolang Manok' => [['Chicken', 150, 'g'], ['Ginger', 8, 'g'], ['Garlic', 4, 'g'], ['Onion', 15, 'g'], ['Sayote', 100, 'g'], ['Chili Leaves', 20, 'g']],
            'Pork Asado Chinese Style' => [['Pork', 120, 'g'], ['Soy Sauce', 12, 'ml'], ['Sugar', 12, 'g'], ['Star Anise', 0.5, 'g']],
            'Relleno Bangus' => [['Bangus', 180, 'g'], ['Carrot', 15, 'g'], ['Onion', 10, 'g'], ['Egg', 10, 'g']],
            'Sweetened Banana' => [['Saba Banana', 1, 'pc']],
        ];
    }
}
