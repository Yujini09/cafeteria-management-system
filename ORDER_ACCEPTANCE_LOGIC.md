# Order/Reservation Acceptance Logic - Complete Analysis

## Overview
The system has a multi-step approval process for reservations with inventory validation and comprehensive rule checking.

---

## 1. RESERVATION CREATION (Initial Order Placement)

### Location: `ReservationController@store()`
### File: [app/Http/Controllers/ReservationController.php](app/Http/Controllers/ReservationController.php#L318)

#### Validation Rules:
```php
$validated = $request->validate([
    'notes' => 'nullable|string|max:1000',
    'reservations' => 'required|array',
    'reservations.*.*.category' => 'required|string',
    'reservations.*.*.menu' => 'required|integer',
    'reservations.*.*.qty' => 'required|integer|min:0',
]);
```

#### Conditions for Creating a Reservation:
1. **Must have at least one menu item selected** (reservations array required)
2. **Each menu selection must have:**
   - A valid category (string)
   - A valid menu ID (must exist in database - integer)
   - A quantity (integer, minimum 0)
3. **Optional fields:**
   - Notes/Special requests (max 1000 characters)

#### Automatic Values Set:
- **Status**: `pending` (initial state)
- **User ID**: Authenticated user ID
- **Event Name**: Activity name from reservation data
- **Event Date**: Start date from calendar selection
- **End Date**: Optional multi-day support
- **Number of Persons**: Sum of all quantities selected
- **Contact Information**: Captured from customer form
- **Department, Address, Email, Phone**: Optional fields

#### Post-Creation Actions:
1. Creates reservation items (menu selections)
2. Logs in AuditTrail: "Placed Order"
3. Creates admin notification: "order_placed"
4. Stores receipt reservation ID in session
5. Returns success response with redirect to reservation details

---

## 2. RESERVATION APPROVAL/ACCEPTANCE

### Location: `ReservationController@approve()`
### File: [app/Http/Controllers/ReservationController.php](app/Http/Controllers/ReservationController.php#L92)

### 2.1 Approval Conditions

#### Initial Checks (Before Approval):
1. **Status Check**: Only `pending` reservations can be approved
   - View condition: `@if($r->status !== 'approved' && $r->status !== 'declined')`

2. **Inventory Validation** (Only if NOT force_approve):
   ```php
   if (!$forceApprove) {
       $reservation->load(['items.menu.items.recipes.inventoryItem']);
       $insufficientItems = $this->getInsufficientItems($reservation);
       
       if (!empty($insufficientItems)) {
           // Show warning modal with insufficient items
           return redirect()->with('inventory_warning', true)
                           ->with('insufficient_items', $insufficientItems);
       }
   }
   ```

### 2.2 Inventory Calculation Rules

**How Inventory is Calculated:**

#### Guest Count Determination (Priority Order):
```php
$guests = $reservation->guests 
        ?? $reservation->attendees 
        ?? $reservation->number_of_persons 
        ?? 1;
```

#### Required Quantity Calculation:
```php
$required = (float)($recipe->quantity_needed ?? 0) 
          * $bundleQty        // Quantity of menu ordered
          * $guests;          // Number of persons
```

#### Availability Check:
```php
$available = (float)($ingredient->qty ?? 0);

if ($available < $required) {
    // INSUFFICIENT - Show warning
    insufficientItems[] = [
        'name' => $ingredient->name,
        'required' => $required,
        'available' => $available,
        'shortage' => $required - $available,
        'unit' => $ingredient->unit ?? 'units',
    ];
}
```

### 2.3 User Options When Inventory is Insufficient

#### Option 1: Cancel Approval
- User clicks "Cancel" in inventory warning modal
- Reservation remains in `pending` status
- No inventory deduction occurs

#### Option 2: Proceed Anyway (Force Approval)
- User clicks "Proceed Anyway" button
- Sets `force_approve = 1` in form
- Bypasses inventory check
- Inventory is deducted as much as possible
- Items with insufficient stock reduced to zero

### 2.4 Inventory Deduction (When Approved)

**Transaction Protection**: All operations wrapped in `DB::transaction()`

**Deduction Logic:**
```php
foreach ($reservation->items as $resItem) {
    $menu = $resItem->menu;
    $bundleQty = $resItem->quantity ?? 1;
    
    foreach ($menu->items as $food) {
        foreach ($food->recipes as $recipe) {
            $ingredient = $recipe->inventoryItem;
            
            $deduct = (float)($recipe->quantity_needed ?? 0) 
                    * $bundleQty 
                    * $guests;
            
            if ($deduct <= 0) continue;
            
            // Deduct but never go below 0
            $ingredient->qty = max(0, ($ingredient->qty ?? 0) - $deduct);
            $ingredient->save();
        }
    }
}
```

### 2.5 Status Change & Notifications

When Approved Successfully:
1. **Status Updated**: `pending` → `approved`
2. **Saved to Database**
3. **Customer Notification Sent** (Email + SMS if configured)
4. **Admin Notification Created**:
   ```
   Action: 'reservation_approved'
   Module: 'reservations'
   Description: "Reservation #{id} has been approved"
   Metadata: {
       reservation_id,
       customer_name,
       updated_by
   }
   ```
5. **User Redirected** with success message: "Reservation approved and inventory updated."

---

## 3. RESERVATION DECLINE/REJECTION

### Location: `ReservationController@decline()`
### File: [app/Http/Controllers/ReservationController.php](app/Http/Controllers/ReservationController.php#L197)

### 3.1 Decline Conditions

1. **Status Check**: Only `pending` reservations can be declined
   - View condition: Same as approve

2. **Reason Required**: 
   ```php
   $data = $request->validate([
       'reason' => 'required|string|max:1000'
   ]);
   ```
   - Must provide a reason (string, max 1000 characters)
   - No decline without reason

### 3.2 Decline Actions

When Declined:
1. **Status Updated**: `pending` → `declined`
2. **Reason Stored**: `decline_reason` field updated
3. **Saved to Database**
4. **Customer Notification Sent** (Email + SMS if configured)
   - Email/SMS includes decline reason
5. **Admin Notification Created**:
   ```
   Action: 'reservation_declined'
   Module: 'reservations'
   Description: "Reservation #{id} has been declined"
   Metadata: {
       reservation_id,
       customer_name,
       reason,
       updated_by
   }
   ```
6. **NO Inventory Changes**: Decline does NOT affect inventory

### 3.3 Display Decline Reason

View Condition: `@if($r->status === 'declined' && !empty($r->decline_reason))`
- Decline reason card is displayed only if status is declined AND reason exists

---

## 4. INVENTORY CHECK ENDPOINT

### Location: `ReservationController@checkInventory()`
### File: [app/Http/Controllers/ReservationController.php](app/Http/Controllers/ReservationController.php#L47)

**Purpose**: AJAX endpoint for checking inventory without approval

**Response Format:**
```json
{
    "sufficient": true|false,
    "insufficient_items": [
        {
            "name": "Ingredient Name",
            "required": 100.00,
            "available": 50.00,
            "shortage": 50.00,
            "unit": "kg"
        }
    ]
}
```

**Uses Same Calculation Logic** as approval process

---

## 5. RULES & CONDITIONS SUMMARY TABLE

| Rule | Condition | Result |
|------|-----------|--------|
| **Initial Status** | When created | Always `pending` |
| **Approval Only** | If status is `pending` | Can approve or decline |
| **Approval Block** | If status is `approved` or `declined` | No action buttons shown |
| **Inventory Check** | On approval (unless force) | Shows warning if insufficient |
| **Force Approval** | When insufficient inventory | Deducts what's available, zeros rest |
| **Decline Reason** | Always required | Validation fails without reason |
| **Notification** | Both approve & decline | Customer notified via email/SMS |
| **Audit Trail** | Order placement | "Placed Order" logged |
| **Admin Alert** | All status changes | Admin notifications created |
| **Multi-day Support** | Day numbers in items | Calculated per day |
| **Guest Multiplier** | Various field names | guests > attendees > number_of_persons |

---

## 6. NOTIFICATION SYSTEM

### Customer Notifications

**On Approval:**
- Method: Email + SMS (if configured)
- Class: `ReservationStatusChanged`
- Includes: Reservation details

**On Decline:**
- Method: Email + SMS (if configured)
- Class: `ReservationStatusChanged`
- Includes: Decline reason

**Fallback Logic:**
```php
// Primary: Use user relationship
if ($reservation->user) {
    $reservation->user->notify($notification);
}
// Fallback: Use email field
elseif ($reservation->email) {
    NotificationFacade::route('mail', $reservation->email)->notify($notification);
}

// SMS only if Vonage is configured
$hasVonage = (bool)(config('services.vonage.key') && config('services.vonage.secret'));
if ($hasVonage && $phone) {
    NotificationFacade::route('vonage', $phone)->notify($notification);
}
```

### Admin Notifications

**Created in database** with metadata:
- Action type (order_placed, reservation_approved, reservation_declined)
- Module: 'reservations'
- User who triggered action
- Reservation details

---

## 7. UI/FORM ELEMENTS

### Approval Modal Sequence:

1. **Initial Confirmation Modal**
   - Title: "Confirm Approval"
   - Message: "Are you sure you want to approve this reservation?"
   - Buttons: Cancel | Yes, Approve

2. **If Inventory Warning**
   - Title: "Insufficient Inventory"
   - Shows: Table of insufficient items (Required, Available, Shortage)
   - Info: "If you approve, items will be deducted as much as possible"
   - Buttons: Cancel | Proceed Anyway

3. **Success Message**
   - Title: "Reservation Accepted"
   - Message: "Inventory was updated and customer was notified"
   - Button: OK

### Decline Modal Sequence:

1. **Decline Confirmation Modal**
   - Title: "Confirm Decline"
   - Message: "Are you sure you want to decline this reservation?"
   - Buttons: Cancel | Yes, Decline

2. **Decline Reason Form**
   - Textarea: Required reason (max 1000 chars)
   - Buttons: Cancel | Submit

---

## 8. KEY OBSERVATIONS & NOTES

### Potential Issues to Monitor:

1. **Inventory Calculation**
   - Uses three different field names for guest count (redundancy)
   - Should consolidate to single field

2. **Force Approval**
   - Can accept orders with zero inventory
   - No warning logged when forced
   - Consider adding audit trail for force approvals

3. **Status Lock**
   - Once approved/declined, no status change possible
   - No re-opening or correction mechanism
   - Consider adding edit capability for pending status

4. **Decline Reason Required**
   - Good UX for tracking
   - But no pre-defined reasons (free text)
   - Could benefit from predefined reasons dropdown

5. **Multi-day Complexity**
   - Day numbers stored in reservation items
   - Times stored as JSON in day_times field
   - Calculation based on quantity per day, not actual days

---

## 9. DATABASE RELATIONSHIPS

```
Reservation (1)
├── User (1)
├── ReservationItem (Many)
│   └── Menu (1)
│       └── MenuItem (Many)
│           └── Recipe (Many)
│               └── InventoryItem (1)
```

---

## 10. API/ROUTE SUMMARY

| Endpoint | Method | Action | Auth |
|----------|--------|--------|------|
| `/admin/reservations` | GET | List all reservations | Admin |
| `/admin/reservations/{id}` | GET | View reservation details | Admin |
| `/admin/reservations/{id}/approve` | PATCH | Approve/Accept order | Admin |
| `/admin/reservations/{id}/decline` | PATCH | Decline order | Admin |
| `/admin/reservations/{id}/check-inventory` | GET | Check inventory (AJAX) | Admin |

---

## Generated: January 30, 2026
## System: Cafeteria Management System
## Version: Current Production
