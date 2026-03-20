<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryUsageLog;
use App\Services\InventoryAlertService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Models\AuditTrail;
use App\Support\RecipeUnit;
use App\Support\AuditDictionary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class InventoryItemController extends Controller
{
    public function alerts(InventoryAlertService $inventoryAlertService): JsonResponse
    {
        $alerts = $inventoryAlertService->getAlerts()->values();

        return response()->json([
            'alerts' => $alerts->all(),
            'count' => $alerts->count(),
        ]);
    }

    public function usageLogs(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', 'in:' . InventoryUsageLog::TYPE_AUTO_DEDUCT . ',' . InventoryUsageLog::TYPE_MANUAL_ADJUSTMENT],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $query = InventoryUsageLog::query()
            ->with([
                'inventoryItem:id,name,unit',
                'reservation:id',
                'user:id,name',
            ])
            ->latest();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $searchLike = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search) . '%';
            $searchReservationId = ctype_digit($search) ? (int) $search : null;

            $query->where(function ($innerQuery) use ($searchLike, $searchReservationId) {
                $innerQuery->where('item_name', 'like', $searchLike)
                    ->orWhereHas('inventoryItem', function ($inventoryQuery) use ($searchLike) {
                        $inventoryQuery->where('name', 'like', $searchLike);
                    })
                    ->orWhereHas('user', function ($userQuery) use ($searchLike) {
                        $userQuery->where('name', 'like', $searchLike);
                    });

                if ($searchReservationId !== null) {
                    $innerQuery->orWhere('reservation_id', $searchReservationId);
                }
            });
        }

        $logs = $query->get();

        return response()->json([
            'logs' => $logs,
        ]);
    }

    public function index(Request $request): View
    {
        // Sorting options: name, qty, expiry_date
        $sort = $request->string('sort')->value();
        if (!in_array($sort, ['name', 'qty', 'expiry_date'], true)) {
            $sort = 'name';
        }

        $direction = $request->string('direction')->lower()->value();
        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $category = trim($request->string('category')->value());
        if ($category === '') {
            $category = null;
        }

        $search = trim($request->string('search')->value());

        $query = InventoryItem::query();

        if ($category) {
            $query->where('category', $category);
        }

        if ($search !== '') {
            $searchLike = "%{$search}%";

            $query->where(function ($inventoryQuery) use ($search, $searchLike) {
                $inventoryQuery->where('name', 'like', $searchLike)
                    ->orWhere('category', 'like', $searchLike)
                    ->orWhere('unit', 'like', $searchLike);

                if (is_numeric($search)) {
                    $inventoryQuery->orWhere('qty', (float) $search);
                }

                $searchDate = $this->normalizeInventorySearchDate($search);
                if ($searchDate !== null) {
                    $inventoryQuery->orWhereDate('expiry_date', $searchDate);
                }
            });
        }

        if ($sort === 'expiry_date') {
            $query->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END ASC')
                ->orderBy('expiry_date', $direction)
                ->orderBy('name');
        } elseif ($sort === 'qty') {
            $query->orderBy('qty', $direction)
                ->orderBy('name');
        } else {
            $query->orderBy('name', $direction);
        }

        $items = $query->paginate(10)->withQueryString();

        // Get distinct categories for the dropdown
        $categories = InventoryItem::distinct()->pluck('category')->sort();

        return view('admin.inventory.index', compact('items', 'sort', 'direction', 'category', 'categories', 'search'));
    }

    private function normalizeInventorySearchDate(string $value): ?string
    {
        $value = trim($value);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    public function create(): View
    {
        return view('admin.inventory.index');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'qty'   => 'required|numeric|min:0',
            'unit'  => 'required|string|max:50',
            'expiry_date' => 'nullable|date',
            'category' => 'required|string|max:100'
        ]);

        $data['unit'] = $this->normalizeAndValidateInventoryUnit($data['unit']);

        $item = InventoryItem::create($data);

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::ADDED_INVENTORY_ITEM,
            AuditDictionary::MODULE_INVENTORY,
            "added inventory item {$item->name}"
        );

        // Create notification for admins/superadmin about inventory item addition
        $this->createAdminNotification('inventory_item_added', 'inventory', 'A new inventory item has been added by ' . Auth::user()?->name ?? 'Unknown', [
            'item_name' => $item->name,
            'category' => $item->category,
            'quantity' => $item->qty,
            'unit' => $item->unit,
            'updated_by' => Auth::user()?->name ?? 'Unknown',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'item' => $item
            ]);
        }

        return redirect()->route('admin.inventory.index');
    }

    public function edit(InventoryItem $inventory): View
    {
        return view('admin.inventory.index', compact('inventory'));
    }

    public function update(Request $request, InventoryItem $inventory): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'qty'   => 'required|numeric|min:0',
            'unit'  => 'required|string|max:50',
            'expiry_date' => 'nullable|date',
            'category' => 'required|string|max:100'
        ]);

        $data['unit'] = $this->normalizeAndValidateInventoryUnit($data['unit']);
        $this->validateLinkedRecipeUnits($inventory, $data['unit']);

        $oldQty = $inventory->qty;
        $inventory->update($data);

        $oldQtyValue = (float) $oldQty;
        $newQtyValue = (float) $inventory->qty;
        $quantityChange = $newQtyValue - $oldQtyValue;
        if (abs($quantityChange) > 0.000001) {
            InventoryUsageLog::create([
                'inventory_item_id' => $inventory->id,
                'item_name' => $inventory->name,
                'type' => InventoryUsageLog::TYPE_MANUAL_ADJUSTMENT,
                'quantity_change' => round($quantityChange, 3),
                'new_balance' => round($newQtyValue, 3),
                'user_id' => Auth::id(),
            ]);
        }

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::UPDATED_INVENTORY_ITEM,
            AuditDictionary::MODULE_INVENTORY,
            "updated inventory item {$inventory->name}"
        );

        // Create notification for admins/superadmin about inventory item update
        $this->createAdminNotification('inventory_item_updated', 'inventory', 'An inventory item has been updated by ' . Auth::user()?->name ?? 'Unknown', [
            'item_name' => $inventory->name,
            'category' => $inventory->category,
            'old_quantity' => $oldQty,
            'new_quantity' => $inventory->qty,
            'unit' => $inventory->unit,
            'updated_by' => Auth::user()?->name ?? 'Unknown',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'item' => $inventory
            ]);
        }

        return redirect()->route('admin.inventory.index');
    }

    public function destroy($id)
    {
        // Find the item. If model uses SoftDeletes, include trashed records.
        $uses = class_uses(InventoryItem::class) ?: [];
        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, $uses)) {
            $inventory = InventoryItem::withTrashed()->find($id);
        } else {
            $inventory = InventoryItem::find($id);
        }

        if (!$inventory) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Item not found'], 404);
            }
            abort(404);
        }

        // Server-side role guard (extra safety beyond route middleware)
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $itemName = $inventory->name;
        $inventory->delete();

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::DELETED_INVENTORY_ITEM,
            AuditDictionary::MODULE_INVENTORY,
            "deleted inventory item {$itemName}"
        );

        // Create notification for admins/superadmin about inventory item deletion
        $this->createAdminNotification('inventory_item_deleted', 'inventory', 'An inventory item has been deleted by ' . Auth::user()?->name ?? 'Unknown', [
            'item_name' => $itemName,
            'updated_by' => Auth::user()?->name ?? 'Unknown',
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully',
            ]);
        }

        return back()->with('success', 'Item deleted.');
    }

    /** Create notification for admins/superadmin */
    protected function createAdminNotification(string $action, string $module, string $description, array $metadata = []): void
    {
        $notificationService = new NotificationService();
        $notificationService->createAdminNotification($action, $module, $description, $metadata);
    }

    private function normalizeAndValidateInventoryUnit(?string $unit): string
    {
        $normalizedUnit = RecipeUnit::normalize($unit);

        if (!RecipeUnit::isAllowedRecipeUnit($normalizedUnit)) {
            throw ValidationException::withMessages([
                'unit' => [
                    'Inventory unit must be one of: ' . implode(', ', RecipeUnit::RECIPE_UNITS) . '.',
                ],
            ]);
        }

        return $normalizedUnit;
    }

    private function validateLinkedRecipeUnits(InventoryItem $inventory, string $targetStockUnit): void
    {
        $recipes = $inventory->recipes()->with('menuItem:id,name')->get();
        $errors = [];

        foreach ($recipes as $recipe) {
            $recipeUnit = RecipeUnit::normalize($recipe->unit) ?? $targetStockUnit;

            if (!RecipeUnit::isAllowedRecipeUnit($recipeUnit) || !RecipeUnit::areCompatible($recipeUnit, $targetStockUnit)) {
                $menuItemName = $recipe->menuItem?->name ?? ('Menu item #' . ($recipe->menu_item_id ?? '?'));
                $errors[] = "Cannot change stock unit to {$targetStockUnit}. Linked recipe in \"{$menuItemName}\" uses unit " . (RecipeUnit::display($recipe->unit) ?: ($recipe->unit ?? 'unknown')) . '.';
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages([
                'unit' => $errors,
            ]);
        }
    }
}
