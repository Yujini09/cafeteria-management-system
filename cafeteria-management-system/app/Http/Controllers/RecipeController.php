<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Recipe;
use App\Models\InventoryItem;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function index(MenuItem $menuItem): View
    {
        $menuItem->load('recipes.inventoryItem','menu');
        $inventory = InventoryItem::orderBy('name')->get();
        return view('admin.recipes.index', compact('menuItem','inventory'));
    }

    public function store(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $data = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity_needed'   => 'required|numeric|min:0.001',
        ]);

        $inventoryItem = InventoryItem::find($data['inventory_item_id']);

        $menuItem->recipes()->updateOrCreate(
            ['inventory_item_id' => $data['inventory_item_id']],
            ['quantity_needed'   => $data['quantity_needed'], 'unit' => $inventoryItem->unit]
        );

        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Added/Updated Recipe Ingredient',
            'module'      => 'recipes',
            'description' => 'added/updated a recipe ingredient',
        ]);

        // Create notification for admins/superadmin about recipe ingredient addition/update
        $this->createAdminNotification('recipe_ingredient_added_updated', 'recipes', 'A recipe ingredient has been added/updated by ' . Auth::user()->name, [
            'menu_item_name' => $menuItem->name,
            'inventory_item_name' => $inventoryItem->name,
            'quantity_needed' => $data['quantity_needed'],
            'unit' => $inventoryItem->unit,
            'updated_by' => Auth::user()->name,
        ]);

        return back()->with('success','Ingredient added/updated.');
    }

    public function destroy(MenuItem $menuItem, Recipe $recipe): RedirectResponse
    {
        abort_unless($recipe->menu_item_id === $menuItem->id, 404);

        $ingredientName = $recipe->inventoryItem->name ?? 'Unknown';
        $menuItemName = $menuItem->name;

        $recipe->delete();

        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Removed Recipe Ingredient',
            'module'      => 'recipes',
            'description' => 'removed a recipe ingredient',
        ]);

        // Create notification for admins/superadmin about recipe ingredient removal
        $this->createAdminNotification('recipe_ingredient_removed', 'recipes', 'A recipe ingredient has been removed by ' . Auth::user()->name, [
            'menu_item_name' => $menuItemName,
            'inventory_item_name' => $ingredientName,
            'updated_by' => Auth::user()->name,
        ]);

        return back()->with('success','Ingredient removed.');
    }

    /** Create notification for admins/superadmin */
    protected function createAdminNotification(string $action, string $module, string $description, array $metadata = []): void
    {
        // Get all admin and superadmin users
        $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
        
        // Create a notification for each admin/superadmin
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        }
    }
}
