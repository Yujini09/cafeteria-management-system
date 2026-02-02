# Auto-Generated Menu Naming System

## Overview
The menu creation system now supports **automatic menu name generation**. When a user creates a menu without providing a custom name, the system automatically assigns a sequential name in the format `Menu #X`.

## Features

### 1. Automatic Sequential Naming
- When a user leaves the "Display Name" field empty and creates a menu, the system automatically generates a name
- The generated name follows the pattern: `Menu #X` where X is a sequential number
- Example sequence: `Menu #1`, `Menu #2`, `Menu #3`, etc.

### 2. Intelligent Number Sequencing
The system **does not** permanently track numbers. Instead, it:
- Scans all existing menus in the database
- Looks for menus with names matching the `Menu #X` pattern
- Extracts the highest number currently in use
- Assigns the next available number (highest + 1)

### 3. Deletion Handling
When a menu is deleted:
- The deleted menu's number is removed from consideration
- The next auto-generated menu uses the current highest number + 1
- **Example**: If you have `Menu #1`, `Menu #2`, `Menu #3`, delete `Menu #2`, then:
  - Next auto-generated menu will be `Menu #4` (not `Menu #2`)
  - This ensures no gaps in the sequence while preventing accidental number reuse

### 4. User Flexibility
- Users can still provide custom names if desired
- Only empty name fields trigger auto-generation
- Both Create and Update operations support auto-naming

## Implementation Details

### Backend Changes
**File**: `app/Http/Controllers/MenuController.php`

#### New Method: `getNextDefaultMenuName()`
```php
private function getNextDefaultMenuName(): string
```

This method:
1. Queries all menus with names matching the pattern `Menu #%`
2. Extracts numeric values from each name using regex: `'/^Menu #(\d+)$/'`
3. Finds the maximum number
4. Returns the next sequential name: `Menu #(max + 1)`
5. Returns `Menu #1` if no auto-generated menus exist

#### Updated Methods
- `store()`: Now auto-generates names for new menus when name is empty
- `update()`: Also supports auto-naming if name is cleared during updates

### Frontend Changes
**File**: `resources/views/admin/menus/index.blade.php`

- Added helper text below the name input fields (both create and edit forms)
- Text: "If left empty, the menu will be named automatically as 'Menu #X'"
- Helps users understand the auto-naming behavior

## Usage Examples

### Example 1: Creating Multiple Menus Without Names
```
1. Create menu (no name provided) → Auto-assigned: Menu #1
2. Create menu (no name provided) → Auto-assigned: Menu #2
3. Create menu (no name provided) → Auto-assigned: Menu #3
```

### Example 2: Creating Menu With Custom Name
```
1. Create menu: "Breakfast Special"
2. Create menu (no name) → Auto-assigned: Menu #1
3. Create menu (no name) → Auto-assigned: Menu #2
```

### Example 3: Deletion Scenario
```
Existing menus:
- Menu #1
- Menu #2
- Menu #3

Delete Menu #2

Create new menu (no name) → Auto-assigned: Menu #4
(NOT Menu #2, because the system always uses highest + 1)
```

## Technical Notes

### Regex Pattern
The system uses the regex pattern `/^Menu #(\d+)$/` to match auto-generated names.
- Matches: `Menu #1`, `Menu #5`, `Menu #100`
- Does NOT match: `Menu #`, `Menu#1`, ` Menu #1`, `Menu #1 `, `My Menu #1`

### Database Query
Uses Laravel's Eloquent query builder:
```php
Menu::query()
    ->where('name', 'like', 'Menu #%')
    ->pluck('name')
    ->all()
```

This efficiently retrieves only relevant menu names without loading full records.

### Performance
- **O(n) complexity**: Where n = number of menus with "Menu #" pattern
- **Minimal database impact**: Only scans name column with LIKE query
- Suitable for typical cafeteria systems with hundreds of menus

## Edge Cases Handled

| Scenario | Behavior |
|----------|----------|
| First menu, no name | Returns `Menu #1` |
| No existing auto-named menus | Returns `Menu #1` |
| Mixed custom and auto names | Only considers auto-named menus |
| Gap in numbering (1, 3, 5) | Returns `Menu #6` (not filling gaps) |
| Non-numeric "Menu #" names | Ignored (regex won't match) |
| Empty name after update | Re-generates based on current highest |

## Future Enhancement Ideas

1. **Customizable Prefix**: Allow admins to set a custom prefix instead of "Menu #"
   - Examples: "Breakfast #", "BF-", "M-"

2. **Smart Formatting**: Add date/time components to auto-generated names
   - Examples: "Menu_2024-01-31_#1", "M20240131001"

3. **Category-Based Numbering**: Separate numbering per meal time or menu type
   - Examples: "Breakfast #1", "Lunch #1" (separate sequences)

4. **Audit Trail**: Log auto-generated menu names in audit trail for transparency

5. **Bulk Operations**: Batch rename existing custom menus to auto format
   - Useful for standardizing legacy data

## Testing Checklist

- [ ] Create menu without name → Verify auto-named as `Menu #1`
- [ ] Create second menu without name → Verify auto-named as `Menu #2`
- [ ] Delete `Menu #1` → Create new menu without name → Verify auto-named as `Menu #3`
- [ ] Create menu with custom name → Verify custom name is used
- [ ] Update menu to remove name → Verify auto-generation on save
- [ ] Create many menus → Verify numbering remains sequential
- [ ] Edit menu that was auto-named → Verify name is preserved unless cleared

## Code References

- **Controller**: [app/Http/Controllers/MenuController.php](app/Http/Controllers/MenuController.php)
  - Lines 148-177: `getNextDefaultMenuName()` method
  - Line 217-219: Auto-naming in `store()` method
  - Line 335-337: Auto-naming in `update()` method

- **Model**: [app/Models/Menu.php](app/Models/Menu.php)
  - Name field is already nullable and fillable

- **View**: [resources/views/admin/menus/index.blade.php](resources/views/admin/menus/index.blade.php)
  - Lines 487-489: Create form help text
  - Lines 815-817: Edit form help text
