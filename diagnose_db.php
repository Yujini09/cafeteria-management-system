<?php

// Diagnostic script to check database schema
require __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "=== Database Schema Diagnostics ===\n\n";
    
    // Check if tables exist
    $tables = ['menus', 'menu_items', 'recipes', 'inventory_items'];
    foreach ($tables as $table) {
        $exists = Schema::hasTable($table);
        echo "Table '{$table}': " . ($exists ? "✓ EXISTS" : "✗ MISSING") . "\n";
        
        if ($exists) {
            $columns = Schema::getColumnListing($table);
            echo "  Columns: " . implode(', ', $columns) . "\n";
        }
    }
    
    echo "\n=== Table Row Counts ===\n";
    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            $count = DB::table($table)->count();
            echo "  {$table}: {$count} rows\n";
        }
    }
    
    echo "\n=== Checking Foreign Keys ===\n";
    
    if (Schema::hasTable('menu_items')) {
        $menus = DB::table('menus')->first();
        if ($menus) {
            echo "✓ Found menu with ID: " . $menus->id . "\n";
            
            // Try to create a test menu item
            try {
                DB::table('menu_items')->insert([
                    'menu_id' => $menus->id,
                    'name' => 'Test Item',
                    'type' => 'food',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                echo "✓ Successfully inserted test menu_item\n";
                
                // Clean up
                DB::table('menu_items')->where('name', 'Test Item')->delete();
            } catch (\Exception $e) {
                echo "✗ Failed to insert menu_item: " . $e->getMessage() . "\n";
            }
        } else {
            echo "✗ No menus found in database\n";
        }
    }
    
    echo "\n=== Inventory Items Check ===\n";
    if (Schema::hasTable('inventory_items')) {
        $count = DB::table('inventory_items')->count();
        echo "Found {$count} inventory items\n";
        
        if ($count == 0) {
            echo "WARNING: No inventory items found! Menu recipes need inventory items.\n";
        }
    }
    
    echo "\n✓ Diagnostics complete\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
