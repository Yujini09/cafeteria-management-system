<?php

namespace Database\Seeders;

use App\Models\MenuPrice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class MenusAndItemsSqlSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('menus') || !Schema::hasTable('menu_items')) {
            $this->command?->warn('Skipping MenusAndItemsSqlSeeder: menus/menu_items table is missing.');
            return;
        }

        $menuColumns = $this->getMenuColumns();
        $menuItemColumns = $this->getMenuItemColumns();
        $this->assertRequiredColumns($menuColumns, $menuItemColumns);

        $priceMap = $this->resolvePriceMap();
        $sequenceByType = ['standard' => 0, 'special' => 0];

        DB::transaction(function () use ($menuColumns, $menuItemColumns, $priceMap, &$sequenceByType): void {
            DB::table('menu_items')->delete();
            DB::table('menus')->delete();

            foreach ($this->menuDefinitions() as $mealTime => $menusByType) {
                foreach ($menusByType as $type => $menus) {
                    foreach ($menus as $items) {
                        $sequenceByType[$type]++;

                        $menuId = DB::table('menus')->insertGetId(
                            $this->buildMenuPayload(
                                $menuColumns,
                                $type,
                                $mealTime,
                                $sequenceByType[$type],
                                (float) ($priceMap[$type][$mealTime] ?? 0)
                            )
                        );

                        $this->insertMenuItems($menuItemColumns, $menuId, $items);
                    }
                }
            }
        });
    }

    private function getMenuColumns(): array
    {
        return [
            'name' => Schema::hasColumn('menus', 'name'),
            'type' => Schema::hasColumn('menus', 'type'),
            'meal_time' => Schema::hasColumn('menus', 'meal_time'),
            'price' => Schema::hasColumn('menus', 'price'),
            'description' => Schema::hasColumn('menus', 'description'),
            'created_at' => Schema::hasColumn('menus', 'created_at'),
            'updated_at' => Schema::hasColumn('menus', 'updated_at'),
        ];
    }

    private function getMenuItemColumns(): array
    {
        return [
            'menu_id' => Schema::hasColumn('menu_items', 'menu_id'),
            'name' => Schema::hasColumn('menu_items', 'name'),
            'type' => Schema::hasColumn('menu_items', 'type'),
            'created_at' => Schema::hasColumn('menu_items', 'created_at'),
            'updated_at' => Schema::hasColumn('menu_items', 'updated_at'),
        ];
    }

    private function assertRequiredColumns(array $menuColumns, array $menuItemColumns): void
    {
        $missing = [];

        foreach (['name', 'type', 'meal_time'] as $column) {
            if (empty($menuColumns[$column])) {
                $missing[] = "menus.$column";
            }
        }

        foreach (['menu_id', 'name'] as $column) {
            if (empty($menuItemColumns[$column])) {
                $missing[] = "menu_items.$column";
            }
        }

        if (!empty($missing)) {
            throw new RuntimeException('Cannot seed menus: missing required columns: ' . implode(', ', $missing));
        }
    }

    private function buildMenuPayload(array $menuColumns, string $type, string $mealTime, int $menuNumber, float $price): array
    {
        $payload = [];
        $now = now();

        if ($menuColumns['name']) {
            $payload['name'] = 'Menu #' . $menuNumber;
        }
        if ($menuColumns['type']) {
            $payload['type'] = $type;
        }
        if ($menuColumns['meal_time']) {
            $payload['meal_time'] = $mealTime;
        }
        if ($menuColumns['price']) {
            $payload['price'] = $price;
        }
        if ($menuColumns['description']) {
            $payload['description'] = null;
        }
        if ($menuColumns['created_at']) {
            $payload['created_at'] = $now;
        }
        if ($menuColumns['updated_at']) {
            $payload['updated_at'] = $now;
        }

        return $payload;
    }

    private function insertMenuItems(array $menuItemColumns, int $menuId, array $items): void
    {
        if (empty($items)) {
            return;
        }

        $now = now();
        $rows = [];

        foreach ($items as $item) {
            $row = [];

            if ($menuItemColumns['menu_id']) {
                $row['menu_id'] = $menuId;
            }
            if ($menuItemColumns['name']) {
                $row['name'] = (string) ($item['name'] ?? 'Unnamed Item');
            }
            if ($menuItemColumns['type']) {
                $row['type'] = (string) ($item['type'] ?? 'food');
            }
            if ($menuItemColumns['created_at']) {
                $row['created_at'] = $now;
            }
            if ($menuItemColumns['updated_at']) {
                $row['updated_at'] = $now;
            }

            $rows[] = $row;
        }

        if (!empty($rows)) {
            DB::table('menu_items')->insert($rows);
        }
    }

    private function resolvePriceMap(): array
    {
        $defaults = [
            'standard' => ['breakfast' => 150, 'am_snacks' => 150, 'lunch' => 300, 'pm_snacks' => 100, 'dinner' => 300],
            'special' => ['breakfast' => 170, 'am_snacks' => 100, 'lunch' => 350, 'pm_snacks' => 150, 'dinner' => 350],
        ];

        if (!Schema::hasTable('menu_prices')) {
            return $defaults;
        }

        try {
            return MenuPrice::getPriceMap();
        } catch (\Throwable $e) {
            return $defaults;
        }
    }

    private function menuDefinitions(): array
    {
        return array (
  'breakfast' => 
  array (
    'standard' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'Longanisa w/ Slice Tomato',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Fried Egg Sunny Side Up',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        4 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'Pork Embutido',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Onion Omelet',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        4 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'Luncheon Meat',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Dilis w/ Chopped Tomato',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        4 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'Pork Tapa w/ Tomato',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Salted Egg',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        4 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
    ),
    'special' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'Fruit in Season',
          'type' => 'dessert',
        ),
        1 => 
        array (
          'name' => 'Longanisa w/ Slice Tomato',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Boneless Daing na Bangus',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Mushroom Omelet',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'Fruit in Season',
          'type' => 'dessert',
        ),
        1 => 
        array (
          'name' => 'Pork Omelet w/ Catsup',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Fried Eggplant',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Toasted Bread w/ Jam&Butter',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'Fruit in Season',
          'type' => 'dessert',
        ),
        1 => 
        array (
          'name' => 'Chicken Embutido',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Fried Sausage',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Fried Egg Sunny Side Up',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'Nilagang Saba',
          'type' => 'dessert',
        ),
        1 => 
        array (
          'name' => 'Pork Tapa w/ Tomato',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Salted Egg',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Daing Dilis',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
    ),
  ),
  'am_snacks' => 
  array (
    'standard' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'Ham & Cheese Sandwich',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Buko w/ Gulaman',
          'type' => 'drink',
        ),
        2 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Distilled Water',
          'type' => 'drink',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'Pimiento Sandwich',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Buko w/ Gulaman',
          'type' => 'drink',
        ),
        2 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Distilled Water',
          'type' => 'drink',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'Chicken Sandwich',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'P/A Juice',
          'type' => 'drink',
        ),
        2 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Distilled Water',
          'type' => 'drink',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'Cheese Burger',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Iced Tea w/ Tanglad',
          'type' => 'drink',
        ),
        2 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
    ),
    'special' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'Lomi',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Puto Cheese',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Orange Juice',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        4 => 
        array (
          'name' => 'Distilled Water',
          'type' => 'drink',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'Bihon Guisado',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Kutsinta w/ Latik',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Buko Juice',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        4 => 
        array (
          'name' => 'Distilled Water',
          'type' => 'drink',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'Spaghetti w/ Meat Balls',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'P/A Orange Juice',
          'type' => 'drink',
        ),
        2 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Distilled Water',
          'type' => 'drink',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'Carbonara w/ Chicken Fillet',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => '4 Season Juice',
          'type' => 'drink',
        ),
        2 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Distilled Water',
          'type' => 'drink',
        ),
      ),
    ),
  ),
  'lunch' => 
  array (
    'standard' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'Chickenn Soup',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Pork Karekare w/ Binagoongan',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Lumpia Frito',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Bolabola w/ P/A Sauce',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Molded Gulaman',
          'type' => 'dessert',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'Crab & Corn Soup',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Pork w/ Mushroom',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Chinese Veg. w/ Quail Egg',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Fish Fillet w/ Sweet Chilli Sauce',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Fruit in Season',
          'type' => 'dessert',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'Onion Soup',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Cordon Bleu w/Creamy Mushroom-Sauce',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Pork Bistick',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Toge Guisado',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Fruit in Season',
          'type' => 'dessert',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'Corn Soup',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Pork Sarciado',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Gising-gising',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Fish Bolabola',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Fruit in Season',
          'type' => 'dessert',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
    ),
    'special' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'Egg Drop Soup',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Pork Caldereta',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Chinese Vegetables',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Sweet and Sour Fish',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Leche Flan',
          'type' => 'dessert',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'Crab & Corn Soup',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Steamed Veg. w/Butter Garlic Sauce',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Chicken Pork Adobo w/Coco Cream',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Fish Escabeche',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Fruit Salad',
          'type' => 'dessert',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'Sinigang na Hipon',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Fried Chicken',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Gising-gising',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Slice Fruits',
          'type' => 'dessert',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'Bolabola Fish w/ Misua',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Pork Karekare',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Lumpia Shanghai',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Breaded Chicken w/ P/A Sauce',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Fruit Cocktail',
          'type' => 'dessert',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
    ),
  ),
  'pm_snacks' => 
  array (
    'standard' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'Cheese Burger Sandwich',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Sago\'t Gulaman',
          'type' => 'drink',
        ),
        2 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'Chicken Sandwich',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'P/A Juice',
          'type' => 'drink',
        ),
        2 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'Tuna Sandwich',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Iced Tea',
          'type' => 'drink',
        ),
        2 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'Cheese Pimiento Sandwich',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Black Gulaman',
          'type' => 'drink',
        ),
        2 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
    ),
    'special' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'Carbonara',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Toasted Bread',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => '4 Season Juice',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        4 => 
        array (
          'name' => 'Distilled Water',
          'type' => 'drink',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'Sotanghon Guisado',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Maja',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Iced Tea w/ Lemon',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        4 => 
        array (
          'name' => 'Distilled Water',
          'type' => 'drink',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'Bihon Guisado',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Kutsinta w/ Latik',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Sago\'t Gulaman',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        4 => 
        array (
          'name' => 'Distilled Water',
          'type' => 'drink',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'Spaghetti',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Garlic Bread',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'P/A Juice',
          'type' => 'drink',
        ),
        3 => 
        array (
          'name' => 'Tea/Coffee',
          'type' => 'drink',
        ),
        4 => 
        array (
          'name' => 'Distilled Water',
          'type' => 'drink',
        ),
      ),
    ),
  ),
  'dinner' => 
  array (
    'standard' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'Egg Drop Soup',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Mushroom Cabbage w/ Pork Balls',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Chicken Caldereta',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Fried Tilapia',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Banana',
          'type' => 'dessert',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'Bolabola w/ Misua',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Breaded Pork',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Bean w/ Ham Strips',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Fried Bangus',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Fruit in Season',
          'type' => 'dessert',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'Batchoy w/ Meat',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Pinakbet',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Fried Hito',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Leche Flan',
          'type' => 'dessert',
        ),
        5 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'Chicken Tinola',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Inihaw na Tilapia',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Lagalaga Veg. w/ Buro',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Fruit Salad',
          'type' => 'dessert',
        ),
        5 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
    ),
    'special' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'Chicken Swam',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Broiled Fish w/ Mango Salad',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Lechon Kawali w/ Sauce',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Lagalaga Veg. Delight w/ Buro',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Banana',
          'type' => 'dessert',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'Sinampalukang Manok',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Pork Sisig',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Pinakbet',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Fruit in Season',
          'type' => 'dessert',
        ),
        5 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'Batchoy Soup',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Fried Tilapia w/ Mango Sisig',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Broiled Eggplant w/ Binagoongan',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Chicken Barbeque',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        5 => 
        array (
          'name' => 'Buko Pandan',
          'type' => 'dessert',
        ),
        6 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'Tinolang Manok',
          'type' => 'food',
        ),
        1 => 
        array (
          'name' => 'Pork Asado Chinese Style',
          'type' => 'food',
        ),
        2 => 
        array (
          'name' => 'Relleno Bangus',
          'type' => 'food',
        ),
        3 => 
        array (
          'name' => 'Rice',
          'type' => 'food',
        ),
        4 => 
        array (
          'name' => 'Sweetened Banana',
          'type' => 'dessert',
        ),
        5 => 
        array (
          'name' => 'Bottled Water',
          'type' => 'drink',
        ),
      ),
    ),
  ),
);
    }
}
