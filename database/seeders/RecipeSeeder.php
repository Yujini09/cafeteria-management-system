<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MenuItem;
use App\Models\InventoryItem;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all menu items
        $menuItems = MenuItem::all();

        // Define common ingredients for different types of menu items
        $ingredientMap = [
            // Breakfast items
            'Rice' => [
                ['name' => 'Rice', 'quantity' => 0.1, 'unit' => 'kg'],
            ],
            'Fried Egg Sunny Side Up' => [
                ['name' => 'Egg', 'quantity' => 1, 'unit' => 'pcs'],
                ['name' => 'Cooking Oil', 'quantity' => 0.01, 'unit' => 'liter'],
                ['name' => 'Salt', 'quantity' => 0.001, 'unit' => 'kg'],
            ],
            'Longanisa w/ Slice Tomato' => [
                ['name' => 'Longanisa', 'quantity' => 0.05, 'unit' => 'kg'],
                ['name' => 'Tomato', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Cooking Oil', 'quantity' => 0.01, 'unit' => 'liter'],
            ],
            'Pork Embutido' => [
                ['name' => 'Ground Pork', 'quantity' => 0.08, 'unit' => 'kg'],
                ['name' => 'Bread Crumbs', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Egg', 'quantity' => 0.5, 'unit' => 'pcs'],
                ['name' => 'Onion', 'quantity' => 0.01, 'unit' => 'kg'],
            ],
            'Onion Omelet' => [
                ['name' => 'Egg', 'quantity' => 2, 'unit' => 'pcs'],
                ['name' => 'Onion', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Cooking Oil', 'quantity' => 0.01, 'unit' => 'liter'],
                ['name' => 'Salt', 'quantity' => 0.001, 'unit' => 'kg'],
            ],
            'Luncheon Meat' => [
                ['name' => 'Luncheon Meat', 'quantity' => 0.05, 'unit' => 'kg'],
                ['name' => 'Cooking Oil', 'quantity' => 0.01, 'unit' => 'liter'],
            ],
            'Dilis w/ Chopped Tomato' => [
                ['name' => 'Dried Fish (Dilis)', 'quantity' => 0.03, 'unit' => 'kg'],
                ['name' => 'Tomato', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Cooking Oil', 'quantity' => 0.01, 'unit' => 'liter'],
            ],
            'Pork Tapa w/ Tomato' => [
                ['name' => 'Pork Tapa', 'quantity' => 0.08, 'unit' => 'kg'],
                ['name' => 'Tomato', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Cooking Oil', 'quantity' => 0.01, 'unit' => 'liter'],
            ],
            'Salted Egg' => [
                ['name' => 'Salted Egg', 'quantity' => 1, 'unit' => 'pcs'],
            ],

            // Drinks
            'Tea/Coffee' => [
                ['name' => 'Tea/Coffee Mix', 'quantity' => 0.005, 'unit' => 'kg'],
                ['name' => 'Sugar', 'quantity' => 0.005, 'unit' => 'kg'],
                ['name' => 'Hot Water', 'quantity' => 0.15, 'unit' => 'liter'],
            ],
            'Bottled Water' => [
                ['name' => 'Bottled Water', 'quantity' => 0.3, 'unit' => 'liter'],
            ],
            'Distilled Water' => [
                ['name' => 'Distilled Water', 'quantity' => 0.3, 'unit' => 'liter'],
            ],

            // Lunch items
            'Chicken Soup' => [
                ['name' => 'Chicken', 'quantity' => 0.1, 'unit' => 'kg'],
                ['name' => 'Onion', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Carrot', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Water', 'quantity' => 0.5, 'unit' => 'liter'],
                ['name' => 'Salt', 'quantity' => 0.002, 'unit' => 'kg'],
            ],
            'Pork Karekare w/ Binagoongan' => [
                ['name' => 'Pork', 'quantity' => 0.15, 'unit' => 'kg'],
                ['name' => 'Peanut Butter', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Bagoong Alamang', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Onion', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Eggplant', 'quantity' => 0.05, 'unit' => 'kg'],
            ],
            'Lumpia Frito' => [
                ['name' => 'Ground Pork', 'quantity' => 0.05, 'unit' => 'kg'],
                ['name' => 'Carrot', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Onion', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Lumpia Wrapper', 'quantity' => 2, 'unit' => 'pcs'],
                ['name' => 'Cooking Oil', 'quantity' => 0.05, 'unit' => 'liter'],
            ],
            'Bolabola w/ P/A Sauce' => [
                ['name' => 'Ground Pork', 'quantity' => 0.08, 'unit' => 'kg'],
                ['name' => 'Carrot', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Onion', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Pineapple', 'quantity' => 0.03, 'unit' => 'kg'],
                ['name' => 'Sweet Chili Sauce', 'quantity' => 0.02, 'unit' => 'kg'],
            ],

            // Snacks
            'Ham & Cheese Sandwich' => [
                ['name' => 'Bread', 'quantity' => 2, 'unit' => 'pcs'],
                ['name' => 'Ham', 'quantity' => 0.03, 'unit' => 'kg'],
                ['name' => 'Cheese', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Butter', 'quantity' => 0.005, 'unit' => 'kg'],
            ],
            'Pimiento Sandwich' => [
                ['name' => 'Bread', 'quantity' => 2, 'unit' => 'pcs'],
                ['name' => 'Pimiento', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Cheese', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Butter', 'quantity' => 0.005, 'unit' => 'kg'],
            ],
            'Chicken Sandwich' => [
                ['name' => 'Bread', 'quantity' => 2, 'unit' => 'pcs'],
                ['name' => 'Chicken Breast', 'quantity' => 0.05, 'unit' => 'kg'],
                ['name' => 'Mayonnaise', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Lettuce', 'quantity' => 0.01, 'unit' => 'kg'],
            ],
            'Cheese Burger' => [
                ['name' => 'Burger Bun', 'quantity' => 1, 'unit' => 'pcs'],
                ['name' => 'Ground Beef', 'quantity' => 0.08, 'unit' => 'kg'],
                ['name' => 'Cheese', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Lettuce', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Tomato', 'quantity' => 0.02, 'unit' => 'kg'],
            ],
            'Tuna Sandwich' => [
                ['name' => 'Bread', 'quantity' => 2, 'unit' => 'pcs'],
                ['name' => 'Canned Tuna', 'quantity' => 0.05, 'unit' => 'kg'],
                ['name' => 'Mayonnaise', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Onion', 'quantity' => 0.01, 'unit' => 'kg'],
            ],
            'Cheese Pimiento Sandwich' => [
                ['name' => 'Bread', 'quantity' => 2, 'unit' => 'pcs'],
                ['name' => 'Pimiento', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Cheese', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Butter', 'quantity' => 0.005, 'unit' => 'kg'],
            ],

            // Beverages
            'Buko w/ Gulaman' => [
                ['name' => 'Coconut', 'quantity' => 0.5, 'unit' => 'pcs'],
                ['name' => 'Gulaman', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Sugar', 'quantity' => 0.02, 'unit' => 'kg'],
            ],
            'P/A Juice' => [
                ['name' => 'Pineapple', 'quantity' => 0.1, 'unit' => 'kg'],
                ['name' => 'Apple', 'quantity' => 0.1, 'unit' => 'kg'],
                ['name' => 'Sugar', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Water', 'quantity' => 0.3, 'unit' => 'liter'],
            ],
            'Iced Tea w/ Tanglad' => [
                ['name' => 'Tea Leaves', 'quantity' => 0.005, 'unit' => 'kg'],
                ['name' => 'Lemongrass', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Sugar', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Water', 'quantity' => 0.3, 'unit' => 'liter'],
                ['name' => 'Ice', 'quantity' => 0.1, 'unit' => 'kg'],
            ],
            'Black Gulaman' => [
                ['name' => 'Gulaman', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Sugar', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Water', 'quantity' => 0.3, 'unit' => 'liter'],
            ],
            'Iced Tea' => [
                ['name' => 'Tea Leaves', 'quantity' => 0.005, 'unit' => 'kg'],
                ['name' => 'Sugar', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Water', 'quantity' => 0.3, 'unit' => 'liter'],
                ['name' => 'Ice', 'quantity' => 0.1, 'unit' => 'kg'],
            ],

            // Dinner items
            'Egg Drop Soup' => [
                ['name' => 'Egg', 'quantity' => 1, 'unit' => 'pcs'],
                ['name' => 'Cornstarch', 'quantity' => 0.005, 'unit' => 'kg'],
                ['name' => 'Chicken Broth', 'quantity' => 0.2, 'unit' => 'liter'],
                ['name' => 'Green Onion', 'quantity' => 0.01, 'unit' => 'kg'],
            ],
            'Chicken Caldereta' => [
                ['name' => 'Chicken', 'quantity' => 0.2, 'unit' => 'kg'],
                ['name' => 'Tomato Sauce', 'quantity' => 0.05, 'unit' => 'liter'],
                ['name' => 'Potato', 'quantity' => 0.05, 'unit' => 'kg'],
                ['name' => 'Carrot', 'quantity' => 0.03, 'unit' => 'kg'],
                ['name' => 'Onion', 'quantity' => 0.02, 'unit' => 'kg'],
            ],
            'Fried Tilapia' => [
                ['name' => 'Tilapia', 'quantity' => 0.15, 'unit' => 'kg'],
                ['name' => 'Cooking Oil', 'quantity' => 0.05, 'unit' => 'liter'],
                ['name' => 'Salt', 'quantity' => 0.002, 'unit' => 'kg'],
                ['name' => 'Calamansi', 'quantity' => 0.01, 'unit' => 'kg'],
            ],
            'Chicken Tinola' => [
                ['name' => 'Chicken', 'quantity' => 0.2, 'unit' => 'kg'],
                ['name' => 'Ginger', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Chayote', 'quantity' => 0.05, 'unit' => 'kg'],
                ['name' => 'Malunggay Leaves', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Water', 'quantity' => 0.5, 'unit' => 'liter'],
            ],

            // Desserts
            'Molded Gulaman' => [
                ['name' => 'Gulaman', 'quantity' => 0.01, 'unit' => 'kg'],
                ['name' => 'Sugar', 'quantity' => 0.02, 'unit' => 'kg'],
                ['name' => 'Water', 'quantity' => 0.2, 'unit' => 'liter'],
            ],
            'Fruit in Season' => [
                ['name' => 'Seasonal Fruit', 'quantity' => 0.1, 'unit' => 'kg'],
            ],
            'Leche Flan' => [
                ['name' => 'Egg', 'quantity' => 3, 'unit' => 'pcs'],
                ['name' => 'Condensed Milk', 'quantity' => 0.15, 'unit' => 'liter'],
                ['name' => 'Sugar', 'quantity' => 0.05, 'unit' => 'kg'],
                ['name' => 'Water', 'quantity' => 0.1, 'unit' => 'liter'],
            ],
            'Banana' => [
                ['name' => 'Banana', 'quantity' => 1, 'unit' => 'pcs'],
            ],
            'Fruit Salad' => [
                ['name' => 'Apple', 'quantity' => 0.05, 'unit' => 'kg'],
                ['name' => 'Banana', 'quantity' => 0.05, 'unit' => 'kg'],
                ['name' => 'Pineapple', 'quantity' => 0.05, 'unit' => 'kg'],
                ['name' => 'Condensed Milk', 'quantity' => 0.05, 'unit' => 'liter'],
            ],
            'Sweetened Banana' => [
                ['name' => 'Banana', 'quantity' => 1, 'unit' => 'pcs'],
                ['name' => 'Sugar', 'quantity' => 0.01, 'unit' => 'kg'],
            ],
        ];

        // Create recipes for each menu item
        foreach ($menuItems as $menuItem) {
            if (isset($ingredientMap[$menuItem->name])) {
                foreach ($ingredientMap[$menuItem->name] as $ingredient) {
                    // Find or create inventory item
                    $inventoryItem = InventoryItem::firstOrCreate(
                        ['name' => $ingredient['name']],
                        [
                            'qty' => rand(50, 200), // Random quantity for demo
                            'unit' => $ingredient['unit'],
                            'category' => $this->getCategoryForIngredient($ingredient['name']),
                            'expiry_date' => now()->addDays(rand(7, 90)),
                        ]
                    );

                    // Create recipe
                    DB::table('recipes')->insert([
                        'menu_item_id' => $menuItem->id,
                        'inventory_item_id' => $inventoryItem->id,
                        'quantity_needed' => $ingredient['quantity'],
                        'unit' => $ingredient['unit'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Get category for ingredient
     */
    private function getCategoryForIngredient($name)
    {
        $categories = [
            'Perishable' => ['Chicken', 'Pork', 'Beef', 'Fish', 'Egg', 'Milk', 'Banana', 'Apple', 'Pineapple', 'Tomato', 'Onion', 'Carrot', 'Potato', 'Ginger', 'Garlic', 'Lettuce', 'Calamansi'],
            'Condiments' => ['Salt', 'Sugar', 'Soy Sauce', 'Vinegar', 'Mayonnaise', 'Ketchup', 'Sweet Chili Sauce', 'Peanut Butter', 'Bagoong Alamang'],
            'Frozen' => ['Ground Pork', 'Ground Beef', 'Chicken Breast', 'Frozen Vegetables'],
            'Beverages' => ['Tea Leaves', 'Coffee', 'Coconut', 'Pineapple Juice', 'Apple Juice', 'Water', 'Bottled Water', 'Distilled Water'],
            'Desserts' => ['Condensed Milk', 'Leche Flan', 'Fruit Salad', 'Gulaman', 'Bread', 'Cake'],
            'Others' => ['Rice', 'Bread Crumbs', 'Cornstarch', 'Cooking Oil', 'Butter', 'Cheese', 'Ham', 'Luncheon Meat', 'Canned Tuna'],
        ];

        foreach ($categories as $category => $items) {
            if (in_array($name, $items)) {
                return $category;
            }
        }

        return 'Others';
    }
}
