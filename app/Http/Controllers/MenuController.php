<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuPrice;
use App\Models\InventoryItem;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Models\AuditTrail;
use App\Support\AuditDictionary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    private const TYPES = ['standard' => 'Standard', 'special' => 'Special'];
    private const MEALS = [
        'breakfast'  => 'Breakfast',
        'am_snacks'  => 'AM Snacks',
        'lunch'      => 'Lunch',
        'pm_snacks'  => 'PM Snacks',
        'dinner'     => 'Dinner',
    ];

    private static function getPriceMap()
    {
        return MenuPrice::getPriceMap();
    }

    public function index(Request $request): View
    {
        // Defaults: standard + breakfast
        $type = $request->query('type', 'standard');
        $meal = $request->query('meal', 'breakfast');

        // Sanitize type (only allow our keys)
        if (!array_key_exists($type, self::TYPES)) {
            $type = 'standard';
        }
        // Sanitize meal: allow 'all' or a valid key
        if ($meal !== 'all' && !array_key_exists($meal, self::MEALS)) {
            $meal = 'breakfast';
        }

        // Base query with relations
        $base = Menu::with('items.recipes');

        // Apply type filter if column exists
        if (Schema::hasColumn('menus', 'type')) {
            $base->where('type', $type);
        }

        // Build current list depending on meal
        $currentQuery = clone $base;
        if ($meal !== 'all' && Schema::hasColumn('menus', 'meal_time')) {
            $currentQuery->where('meal_time', $meal);
        }
        $currentMenus = $currentQuery->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Menus by day (for convenience; 'all' = all meals for this type)
        $menusByDay = [
            'all'        => (clone $base)->orderBy('created_at', 'desc')->get(),
            'breakfast'  => (clone $base)->where('meal_time', 'breakfast')->orderBy('created_at', 'desc')->get(),
            'am_snacks'  => (clone $base)->where('meal_time', 'am_snacks')->orderBy('created_at', 'desc')->get(),
            'lunch'      => (clone $base)->where('meal_time', 'lunch')->orderBy('created_at', 'desc')->get(),
            'pm_snacks'  => (clone $base)->where('meal_time', 'pm_snacks')->orderBy('created_at', 'desc')->get(),
            'dinner'     => (clone $base)->where('meal_time', 'dinner')->orderBy('created_at', 'desc')->get(),
        ];

        // Counts per meal (for current type)
        if (Schema::hasColumn('menus', 'meal_time')) {
            $counts = Menu::selectRaw('meal_time, COUNT(*) as total')
                ->when(Schema::hasColumn('menus', 'type'), fn($qq) => $qq->where('type', $type))
                ->groupBy('meal_time')
                ->pluck('total', 'meal_time');
        } else {
            $counts = collect();
        }
        $totalCount = (int) ($counts->sum() ?? 0);

        // Active price only when a single meal is chosen
        $priceMap = self::getPriceMap();
        $activePrice = ($meal !== 'all' && isset($priceMap[$type][$meal])) ? $priceMap[$type][$meal] : null;

        $inventoryItems = InventoryItem::orderBy('name')->get();

        return view('admin.menus.index', [
            'type'         => $type,
            'meal'         => $meal,
            'types'        => self::TYPES,
            'meals'        => self::MEALS,
            'activePrice'  => $activePrice,
            'menusByDay'   => $menusByDay,
            'currentMenus' => $currentMenus,
            'counts'       => $counts,
            'totalCount'   => $totalCount,
            'priceMap'     => $priceMap,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    public function create(Request $request): View
    {
        $type = $request->query('type', 'standard');
        $meal = $request->query('meal', 'breakfast');

        if (!array_key_exists($type, self::TYPES)) $type = 'standard';
        if (!array_key_exists($meal, self::MEALS)) $meal = 'breakfast';

        $has = [
            'type'        => Schema::hasColumn('menus', 'type'),
            'meal_time'   => Schema::hasColumn('menus', 'meal_time'),
            'name'        => Schema::hasColumn('menus', 'name'),
            'description' => Schema::hasColumn('menus', 'description'),
            'price'       => Schema::hasColumn('menus', 'price'),
        ];

        $priceMap = self::getPriceMap();
        $activePrice = ($has['price'] && isset($priceMap[$type][$meal])) ? $priceMap[$type][$meal] : null;

        $inventoryItems = InventoryItem::orderBy('name')->get();

        // Reuse index view to keep UX consistent
        return view('admin.menus.index', [
            'type'        => $type,
            'meal'        => $meal,
            'types'       => self::TYPES,
            'meals'       => self::MEALS,
            'activePrice' => $activePrice,
            'has'         => $has,
            'priceMap'    => $priceMap,
            'menusByDay'  => ['all' => collect()],
            'currentMenus'=> collect(),
            'counts'      => collect(),
            'totalCount'  => 0,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    /**
     * Generate the next default menu name
     * Finds all menus with "Menu #X" format and returns "Menu #(highest+1)"
     * 
     * @return string The next auto-generated menu name
     */
    private function getNextDefaultMenuName(): string
    {
        // Get all menus with names matching "Menu #X" format
        $menus = Menu::query()
            ->where('name', 'like', 'Menu #%')
            ->pluck('name')
            ->all();

        if (empty($menus)) {
            return 'Menu #1';
        }

        // Extract numbers from menu names
        $numbers = [];
        foreach ($menus as $name) {
            if (preg_match('/^Menu #(\d+)$/', $name, $matches)) {
                $numbers[] = (int) $matches[1];
            }
        }

        if (empty($numbers)) {
            return 'Menu #1';
        }

        // Find the smallest missing positive integer (reuses gaps after deletions)
        $used = array_flip(array_unique($numbers));
        $candidate = 1;
        while (isset($used[$candidate])) {
            $candidate++;
        }
        return 'Menu #' . $candidate;
    }

    private function assertNoDuplicateIngredients(array $items): void
    {
        $errors = [];
        foreach ($items as $itemIndex => $itemData) {
            $recipes = $itemData['recipes'] ?? [];
            $seen = [];
            foreach ($recipes as $recipeIndex => $recipeData) {
                $id = $recipeData['inventory_item_id'] ?? null;
                if (!$id) {
                    continue;
                }
                $key = (string) $id;
                if (isset($seen[$key])) {
                    $itemLabel = $itemData['name'] ?? ('Item ' . ($itemIndex + 1));
                    $errors["items.$itemIndex.recipes.$recipeIndex.inventory_item_id"] =
                        "Duplicate ingredient in item \"{$itemLabel}\" (ingredient " . ($recipeIndex + 1) . ").";
                    $errors["items.$itemIndex.recipes." . $seen[$key] . ".inventory_item_id"] =
                        "Duplicate ingredient in item \"{$itemLabel}\" (ingredient " . ($seen[$key] + 1) . ").";
                    continue;
                }
                $seen[$key] = $recipeIndex;
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $hasType   = Schema::hasColumn('menus', 'type');
            $hasMeal   = Schema::hasColumn('menus', 'meal_time');
            $hasName   = Schema::hasColumn('menus', 'name');
            $hasDesc   = Schema::hasColumn('menus', 'description');
            $hasPrice  = Schema::hasColumn('menus', 'price');

            $rules = [];
            if ($hasType) $rules['type'] = 'required|in:standard,special';
            if ($hasMeal) $rules['meal_time'] = 'required|in:breakfast,am_snacks,lunch,pm_snacks,dinner';
            if ($hasName) $rules['name']      = 'nullable|string|max:255';
            if ($hasDesc) $rules['description']= 'nullable|string';
            $rules['items'] = 'required|array|min:1';
            $rules['items.*.name'] = 'required|string|max:255';
            $rules['items.*.type'] = 'required|in:food,drink,dessert,other';
            $rules['items.*.recipes'] = 'array';
            $rules['items.*.recipes.*.inventory_item_id'] = 'required|exists:inventory_items,id';
            $rules['items.*.recipes.*.quantity_needed'] = 'required|numeric|min:0.01';
            $rules['items.*.recipes.*.unit'] = 'required|string|max:50';

            $data = $request->validate($rules);
            $this->assertNoDuplicateIngredients($data['items'] ?? []);

            if ($hasPrice && $hasType && $hasMeal) {
                $type = $data['type'] ?? 'standard';
                $meal = $data['meal_time'] ?? 'breakfast';
                $priceMap = self::getPriceMap();
                $data['price'] = $priceMap[$type][$meal] ?? 0;
            }

            $payload = [];
            foreach (['type','meal_time','name','description','price'] as $f) {
                if (isset($data[$f])) $payload[$f] = $data[$f];
            }

            // Auto-generate menu name if not provided
            if (empty($payload['name'])) {
                $payload['name'] = $this->getNextDefaultMenuName();
            }

            $menu = Menu::create($payload);

            AuditTrail::record(
                Auth::id(),
                AuditDictionary::CREATED_MENU,
                AuditDictionary::MODULE_MENUS,
                "created menu {$menu->name}"
            );

            $items = $data['items'] ?? [];
            if (!empty($items) && is_array($items)) {
                foreach ($items as $itemData) {
                    $menuItem = $menu->items()->create([
                        'name' => $itemData['name'],
                        'type' => $itemData['type'],
                    ]);
                    // Create recipes for this menu item
                    if (isset($itemData['recipes']) && is_array($itemData['recipes'])) {
                        foreach ($itemData['recipes'] as $recipeData) {
                            $menuItem->recipes()->create($recipeData);
                        }
                    }

                    // Auto-detect and copy recipes from existing menu items with the same name
                    if (!$menuItem->recipes()->exists()) {
                        $existingItem = MenuItem::where('name', $itemData['name'])
                            ->where('menu_id', '!=', $menu->id)
                            ->with('recipes')
                            ->first();
                        if ($existingItem && $existingItem->recipes->isNotEmpty()) {
                            $menuItem->copyRecipesFrom($existingItem);
                        }
                    }
                }
            }
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            \Log::error('Menu creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating menu: ' . $e->getMessage(),
                    'error' => $e->getMessage()
                ], 422);
            }
            
            return back()->withErrors(['error' => 'Error creating menu: ' . $e->getMessage()]);
        }

        // Create notification for admins/superadmin about menu creation
        try {
            $this->createAdminNotification('menu_created', 'menus', 'A new menu has been created by ' . Auth::user()?->name ?? 'Unknown', [
                'menu_id' => $menu->id,
                'menu_name' => $menu->name ?? 'Unnamed',
                'type' => $menu->type,
                'meal_time' => $menu->meal_time,
                'updated_by' => Auth::user()?->name ?? 'Unknown',
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Failed to create menu notification: ' . $e->getMessage());
            // Don't fail the whole request if notification fails
        }

        // Return JSON if AJAX request, otherwise redirect
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu created successfully',
                'menu' => $menu
            ]);
        }

        return redirect()->route('admin.menus.index', ['type' => $payload['type'] ?? 'standard', 'meal' => $payload['meal_time'] ?? 'breakfast'])
            ->with('menu_success', 'Menu created. Add at least 5 foods to complete the bundle.');
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $hasType   = Schema::hasColumn('menus', 'type');
        $hasMeal   = Schema::hasColumn('menus', 'meal_time');
        $hasName   = Schema::hasColumn('menus', 'name');
        $hasDesc   = Schema::hasColumn('menus', 'description');
        $hasPrice  = Schema::hasColumn('menus', 'price');

        $rules = [];
        if ($hasType) $rules['type'] = 'required|in:standard,special';
        if ($hasMeal) $rules['meal_time'] = 'required|in:breakfast,am_snacks,lunch,pm_snacks,dinner';
        if ($hasName) $rules['name']      = 'nullable|string|max:255';
        if ($hasDesc) $rules['description']= 'nullable|string';
        $rules['items'] = 'array';
        $rules['items.*.name'] = 'required|string|max:255';
        $rules['items.*.type'] = 'required|in:food,drink,dessert,other';
        $rules['items.*.recipes'] = 'array';
        $rules['items.*.recipes.*.inventory_item_id'] = 'required|exists:inventory_items,id';
        $rules['items.*.recipes.*.quantity_needed'] = 'required|numeric|min:0.01';
        $rules['items.*.recipes.*.unit'] = 'required|string|max:50';

        $data = $request->validate($rules);
        $this->assertNoDuplicateIngredients($data['items'] ?? []);

        if ($hasPrice && $hasType && $hasMeal) {
            $type = $data['type'] ?? 'standard';
            $meal = $data['meal_time'] ?? 'breakfast';
            $priceMap = self::getPriceMap();
            $data['price'] = $priceMap[$type][$meal] ?? 0;
        }

        $payload = [];
        foreach (['type','meal_time','name','description','price'] as $f) {
            if (isset($data[$f])) $payload[$f] = $data[$f];
        }

        // Auto-generate menu name if not provided (for updates too)
        if (empty($payload['name'])) {
            $payload['name'] = $this->getNextDefaultMenuName();
        }

        $menu->update($payload);

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::UPDATED_MENU,
            AuditDictionary::MODULE_MENUS,
            "updated menu {$menu->name}"
        );

        $menu->items()->delete();
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                $menuItem = $menu->items()->create([
                    'name' => $itemData['name'],
                    'type' => $itemData['type'],
                ]);
                // Create recipes for this menu item
                if (isset($itemData['recipes']) && is_array($itemData['recipes'])) {
                    foreach ($itemData['recipes'] as $recipeData) {
                        $menuItem->recipes()->create($recipeData);
                    }
                }

                // Auto-detect and copy recipes from existing menu items with the same name
                if (!$menuItem->recipes()->exists()) {
                    $existingItem = MenuItem::where('name', $itemData['name'])
                        ->where('menu_id', '!=', $menu->id)
                        ->with('recipes')
                        ->first();
                    if ($existingItem && $existingItem->recipes->isNotEmpty()) {
                        $menuItem->copyRecipesFrom($existingItem);
                    }
                }
            }
        }

        // Create notification for admins/superadmin about menu update
        $this->createAdminNotification('menu_updated', 'menus', 'A menu has been updated by ' . Auth::user()?->name ?? 'Unknown', [
            'menu_id' => $menu->id,
            'menu_name' => $menu->name ?? 'Unnamed',
            'type' => $menu->type,
            'meal_time' => $menu->meal_time,
            'updated_by' => Auth::user()?->name ?? 'Unknown',
        ]);

        return back()->with('menu_success', 'Menu updated.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menuName = $menu->name;
        $menu->delete();

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::DELETED_MENU,
            AuditDictionary::MODULE_MENUS,
            "deleted menu {$menuName}"
        );

        // Create notification for admins/superadmin about menu deletion
        $this->createAdminNotification('menu_deleted', 'menus', 'A menu has been deleted by ' . Auth::user()?->name ?? 'Unknown', [
            'menu_name' => $menuName,
            'updated_by' => Auth::user()?->name ?? 'Unknown',
        ]);

        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted.');
    }

    public function addItem(Request $request, Menu $menu): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:food,drink,dessert',
        ]);

        $menu->items()->create([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return back()->with('success', 'Menu item added.');
    }

    public function prices(Request $request): View
    {
        $priceMap = self::getPriceMap();
        $type = $request->query('type', 'standard');
        $meal = $request->query('meal', 'breakfast');

        // Sanitize type and meal
        if (!array_key_exists($type, self::TYPES)) {
            $type = 'standard';
        }
        if (!array_key_exists($meal, self::MEALS)) {
            $meal = 'breakfast';
        }

        return view('admin.menus.prices', [
            'priceMap' => $priceMap,
            'types' => self::TYPES,
            'meals' => self::MEALS,
            'selectedType' => $type,
            'selectedMeal' => $meal,
        ]);
    }

    public function updatePrices(Request $request): RedirectResponse
    {
        $request->validate([
            'prices' => 'required|array',
            'prices.*.*' => 'required|numeric|min:0',
        ]);

        $updatedPrices = [];
        foreach ($request->prices as $type => $meals) {
            foreach ($meals as $meal => $price) {
                MenuPrice::updateOrCreate(
                    ['type' => $type, 'meal_time' => $meal],
                    ['price' => $price]
                );
                $updatedPrices[] = "{$type} {$meal}: ₱{$price}";
            }
        }

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::UPDATED_MENU_PRICES,
            AuditDictionary::MODULE_MENUS,
            'updated menu prices'
        );

        // Create notification for admins/superadmin about price changes
        $this->createAdminNotification('menu_prices_modified', 'menus', 'Menu prices have been updated by ' . Auth::user()?->name ?? 'Unknown', [
            'updated_prices' => $updatedPrices,
            'updated_by' => Auth::user()?->name ?? 'Unknown',
        ]);

        return back()->with('menu_success', 'Menu prices updated successfully.');
    }

    public function customerIndex(): View
    {
        $allMenus = Menu::with('items')->get();

        $menus = $allMenus->groupBy('meal_time')->map(function ($mealsByMeal) {
            return $mealsByMeal->groupBy('type');
        });

        return view('customer.menu', compact('menus'));
    }

    /** Create notification for admins/superadmin only */
    protected function createAdminNotification(string $action, string $module, string $description, array $metadata = []): void
    {
        $notificationService = new NotificationService();
        $notificationService->createAdminNotification($action, $module, $description, $metadata);
    }
}
