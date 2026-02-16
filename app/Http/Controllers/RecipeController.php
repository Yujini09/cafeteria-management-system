<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Recipe;
use App\Models\InventoryItem;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\AuditTrail;
use App\Support\AuditDictionary;
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
        if (!$inventoryItem) {
            return back()->with('error', 'Inventory item not found.');
        }

        $recipe = $menuItem->recipes()->updateOrCreate(
            ['inventory_item_id' => $data['inventory_item_id']],
            ['quantity_needed'   => $data['quantity_needed'], 'unit' => $inventoryItem->unit ?? null]
        );

        $recipeAction = $recipe->wasRecentlyCreated
            ? AuditDictionary::ADDED_RECIPE_INGREDIENT
            : AuditDictionary::UPDATED_RECIPE_INGREDIENT;
        $recipeDescription = $recipe->wasRecentlyCreated
            ? "added recipe ingredient {$inventoryItem->name} for menu item {$menuItem->name}"
            : "updated recipe ingredient {$inventoryItem->name} for menu item {$menuItem->name}";

        AuditTrail::record(
            Auth::id(),
            $recipeAction,
            AuditDictionary::MODULE_RECIPES,
            $recipeDescription
        );

        // Create notification for admins/superadmin about recipe ingredient addition/update
        $this->createAdminNotification('recipe_ingredient_added_updated', 'recipes', 'A recipe ingredient has been added/updated by ' . (Auth::user()?->name ?? 'System'), [
            'menu_item_name' => $menuItem->name,
            'inventory_item_name' => $inventoryItem->name,
            'quantity_needed' => $data['quantity_needed'],
            'unit' => $inventoryItem->unit,
            'updated_by' => Auth::user()?->name ?? 'System',
        ]);

        return back()->with('success','Ingredient added/updated.');
    }

    public function destroy(MenuItem $menuItem, Recipe $recipe): RedirectResponse
    {
        abort_unless($recipe->menu_item_id === $menuItem->id, 404);

        $ingredientName = $recipe->inventoryItem->name ?? 'Unknown';
        $menuItemName = $menuItem->name;

        $recipe->delete();

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::REMOVED_RECIPE_INGREDIENT,
            AuditDictionary::MODULE_RECIPES,
            "removed recipe ingredient {$ingredientName} from menu item {$menuItemName}"
        );

        // Create notification for admins/superadmin about recipe ingredient removal
        $this->createAdminNotification('recipe_ingredient_removed', 'recipes', 'A recipe ingredient has been removed by ' . Auth::user()?->name ?? 'System', [
            'menu_item_name' => $menuItemName,
            'inventory_item_name' => $ingredientName,
            'updated_by' => Auth::user()?->name ?? 'System',
        ]);

        return back()->with('success','Ingredient removed.');
    }

    /** Create notification for admins/superadmin */
    protected function createAdminNotification(string $action, string $module, string $description, array $metadata = []): void
    {
        $notificationService = new NotificationService();
        $notificationService->createAdminNotification($action, $module, $description, $metadata);
    }
}
