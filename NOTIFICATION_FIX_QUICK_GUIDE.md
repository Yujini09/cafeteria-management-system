# Quick Reference: Notification System Fix

## What Was the Bug?
Notifications appeared multiple times based on the number of admin/superadmin accounts.

**Example:** With 2 admins, creating an inventory item would show **2 identical notifications** instead of 1.

## How Is It Fixed?

### Core Change: Use System-Level Notifications (user_id = 0)

Instead of creating N notifications for N admins:
```php
// ❌ OLD - Creates N notifications
$admins = User::whereIn('role', ['admin', 'superadmin'])->get();
foreach ($admins as $admin) {
    Notification::create(['user_id' => $admin->id, ...]);
}
```

We now create ONE system notification:
```php
// ✅ NEW - Creates 1 notification
Notification::create(['user_id' => 0, ...]);  // user_id = 0 = system notification
```

### Update Any Code That Creates Admin Notifications

**Before:**
```php
// ❌ This creates duplicates
$admins = User::whereIn('role', ['admin', 'superadmin'])->get();
foreach ($admins as $admin) {
    Notification::create([...]);
}
```

**After:**
```php
// ✅ Use the NotificationService
$service = new NotificationService();
$service->createAdminNotification($action, $module, $description, $metadata);
```

## Using NotificationService

### Creating Notifications (In Any Controller)
```php
use App\Services\NotificationService;

class YourController extends Controller
{
    public function store(Request $request)
    {
        // Your business logic...
        
        // Create a notification
        $service = new NotificationService();
        $service->createAdminNotification(
            action: 'Created Menu',
            module: 'menus',
            description: 'New menu added: Breakfast Special',
            metadata: [
                'menu_name' => 'Breakfast Special',
                'created_by' => auth()->user()->name,
            ]
        );
    }
}
```

### Fetching Notifications (In Controllers/APIs)
```php
$service = new NotificationService();
$notifications = $service->getNotificationsForUser(auth()->user(), limit: 20);

return response()->json($notifications);
```

### Check if Notification Exists (Prevent Duplicates)
```php
$exists = $service->notificationExists(
    action: 'Created Item',
    module: 'inventory',
    description: 'Milk stock updated'
);

if (!$exists) {
    $service->createAdminNotification(...);
}
```

## Database Schema (No Changes Needed)
The existing notifications table works perfectly:
```sql
-- user_id = 0 now means "system/admin notification"
-- (previously it was individual admin user IDs)

SELECT * FROM notifications WHERE user_id = 0;  -- All admin notifications
```

## Testing

### Quick Test
```bash
php artisan tinker

# Create a test notification
$service = new \App\Services\NotificationService();
$service->createAdminNotification('Test', 'testing', 'This is a test');

# Count notifications (should be 1, not N)
\App\Models\Notification::where('user_id', 0)->where('action', 'Test')->count()
# Output: 1 ✓
```

### UI Test
1. Add 2+ admin accounts
2. Create an inventory item
3. Check notification list - should show **1** notification (not 2+)
4. Reload page - still shows **1** notification ✓

## Migration Path

If you have existing duplicate notifications:

```php
// Optional: Clean up old duplicates (but not required to function)
// Before running, back up your database!

// Keep one notification per action, delete rest
$notifications = Notification::whereIn('user_id', [1, 7])->get();
$notifications->groupBy(['action', 'module', 'description'])
    ->each(function($group) {
        // Keep first, delete others
        $group->skip(1)->each->delete();
    });

// Convert kept notifications to system notifications
Notification::whereIn('user_id', [1, 7])->update(['user_id' => 0]);
```

## Why This Works

| Problem | Solution |
|---------|----------|
| Creates N notifications | Creates 1, visible to all via `user_id = 0` |
| Database bloat | 1 record instead of N |
| Query filtering | Simple: `where user_id = 0` |
| Hardcoded user IDs | Uses sentinel value `0` for system notifications |
| Difficult to maintain | Centralized `NotificationService` |

## Files Affected
- `app/Services/NotificationService.php` (NEW)
- `app/Http/Controllers/SuperAdminController.php`
- `app/Http/Controllers/InventoryItemController.php`
- `app/Http/Controllers/MenuController.php`
- `app/Http/Controllers/RecipeController.php`
- `app/Http/Controllers/ReportsController.php`
- `resources/views/layouts/sidebar.blade.php`

## Need to Add This to Another Controller?

Simply inject/use the service:

```php
use App\Services\NotificationService;

class SomeNewController extends Controller
{
    public function create()
    {
        $service = new NotificationService();
        $service->createAdminNotification(
            'Action Name',
            'module_name',
            'Description of what happened',
            ['metadata' => 'values']
        );
    }
}
```

That's it! The notification will be created once and visible to all admins.
