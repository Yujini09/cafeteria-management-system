<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
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

        $items = $query->orderBy($sort, $direction)->get();

        // Get distinct categories for the dropdown
        $categories = InventoryItem::distinct()->pluck('category')->sort();

        return view('admin.inventory.index', compact('items', 'sort', 'direction', 'category', 'categories'));
    }

    public function create(): View
    {
        return view('admin.inventory.index');
    }

    public function store(Request $request): RedirectResponse
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
        $this->createAdminNotification('inventory_item_added', 'inventory', 'A new inventory item has been added by ' . Auth::user()->name, [
            'item_name' => $item->name,
            'category' => $item->category,
            'quantity' => $item->qty,
            'unit' => $item->unit,
            'updated_by' => Auth::user()->name,
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Item added successfully.');
    }

    public function edit(InventoryItem $inventory): View
    {
        return view('admin.inventory.index', compact('inventory'));
    }

    public function update(Request $request, InventoryItem $inventory): RedirectResponse
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
        $this->createAdminNotification('inventory_item_updated', 'inventory', 'An inventory item has been updated by ' . Auth::user()->name, [
            'item_name' => $inventory->name,
            'category' => $inventory->category,
            'old_quantity' => $oldQty,
            'new_quantity' => $inventory->qty,
            'unit' => $inventory->unit,
            'updated_by' => Auth::user()->name,
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(InventoryItem $inventory): RedirectResponse
    {
        $itemName = $inventory->name;
        $inventory->delete();

        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Deleted Inventory Item',
            'module'      => 'inventory',
            'description' => 'deleted an inventory item',
        ]);

        // Create notification for admins/superadmin about inventory item deletion
        $this->createAdminNotification('inventory_item_deleted', 'inventory', 'An inventory item has been deleted by ' . Auth::user()->name, [
            'item_name' => $itemName,
            'updated_by' => Auth::user()->name,
        ]);

        return back()->with('success', 'Item deleted.');
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
