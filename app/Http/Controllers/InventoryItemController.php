<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

class InventoryItemController extends Controller
{
    public function index(): View
    {
        // Sorting options: name, qty, expiry_date
        $sort = request('sort', 'name');
        $direction = request('direction', 'asc');
        $category = request('category');

        $query = InventoryItem::query();

        if ($category) {
            $query->where('category', $category);
        }

        $items = $query->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        // Get distinct categories for the dropdown
        $categories = InventoryItem::distinct()->pluck('category')->sort();

        return view('admin.inventory.index', compact('items', 'sort', 'direction', 'category', 'categories'));
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

        $item = InventoryItem::create($data);

        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Added Inventory Item',
            'module'      => 'inventory',
            'description' => 'added an inventory item',
        ]);

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

        $oldQty = $inventory->qty;
        $inventory->update($data);

        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Updated Inventory Item',
            'module'      => 'inventory',
            'description' => 'updated an inventory item',
        ]);

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

        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Deleted Inventory Item',
            'module'      => 'inventory',
            'description' => 'deleted an inventory item',
        ]);

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
}
