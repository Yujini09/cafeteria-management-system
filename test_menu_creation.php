<?php
/**
 * Test script to debug menu creation issue
 * This simulates a menu creation request
 */

// Get Laravel bootstrapped
require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Simulate a test request
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\InventoryItem;

// Use Artisan to handle the request
$request = Request::create('/admin/menus', 'POST', [
    'type' => 'standard',
    'meal_time' => 'breakfast',
    'name' => 'Test Menu',
    'description' => 'Test Description',
    'items' => [
        [
            'name' => 'Rice',
            'type' => 'food',
            'recipes' => [
                [
                    'inventory_item_id' => 1,
                    'quantity_needed' => 1,
                    'unit' => 'Kgs'
                ]
            ]
        ]
    ]
]);

// Make sure we're authenticated
$user = \App\Models\User::firstOrCreate(
    ['email' => 'test@example.com'],
    ['name' => 'Test User', 'password' => bcrypt('password')]
);

$request->setUserResolver(function () use ($user) {
    return $user;
});

try {
    echo "Testing menu creation...\n";
    echo "Request data: " . json_encode($request->all(), JSON_PRETTY_PRINT) . "\n\n";
    
    // Test database connection
    echo "Database connection test: ";
    try {
        $count = DB::table('menus')->count();
        echo "OK (found $count menus)\n\n";
    } catch (\Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n\n";
    }
    
    // Test if tables exist
    echo "Table existence check:\n";
    echo "  - menus: " . (DB::connection()->getDoctrineSchemaManager()->tablesExist('menus') ? "YES" : "NO") . "\n";
    echo "  - menu_items: " . (DB::connection()->getDoctrineSchemaManager()->tablesExist('menu_items') ? "YES" : "NO") . "\n";
    echo "  - recipes: " . (DB::connection()->getDoctrineSchemaManager()->tablesExist('recipes') ? "YES" : "NO") . "\n";
    echo "  - inventory_items: " . (DB::connection()->getDoctrineSchemaManager()->tablesExist('inventory_items') ? "YES" : "NO") . "\n\n";
    
    // Check menu table columns
    echo "Menu table columns:\n";
    $columns = DB::getSchemaBuilder()->getColumnListing('menus');
    foreach ($columns as $col) {
        echo "  - $col\n";
    }
    echo "\n";
    
    // Manually test the creation logic
    echo "Testing manual menu creation...\n";
    $menu = Menu::create([
        'type' => 'standard',
        'meal_time' => 'breakfast',
        'name' => 'Test Menu Manual',
        'description' => 'Manual test',
        'price' => 150
    ]);
    
    echo "Menu created with ID: " . $menu->id . "\n";
    echo "Menu data: " . json_encode($menu->toArray(), JSON_PRETTY_PRINT) . "\n";
    
    // Test menu item creation
    echo "\nTesting menu item creation...\n";
    $menuItem = MenuItem::create([
        'menu_id' => $menu->id,
        'name' => 'Rice',
        'type' => 'food'
    ]);
    
    echo "Menu item created with ID: " . $menuItem->id . "\n";
    
    // Get first inventory item
    $invItem = InventoryItem::first();
    if ($invItem) {
        echo "Using inventory item: " . $invItem->name . " (ID: " . $invItem->id . ")\n";
        
        // Create recipe
        $recipe = $menuItem->recipes()->create([
            'inventory_item_id' => $invItem->id,
            'quantity_needed' => 1,
            'unit' => 'Kgs'
        ]);
        
        echo "Recipe created with ID: " . $recipe->id . "\n";
    } else {
        echo "ERROR: No inventory items found in database!\n";
    }
    
    echo "\nTest completed successfully!\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
