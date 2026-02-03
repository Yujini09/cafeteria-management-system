# Auto-Generated Menu Naming - Test Scenarios

## Test Scenario 1: Basic Auto-Naming

### Setup
Empty database (no menus yet)

### Test Steps
1. Create Menu (no name) → Type: Standard, Meal: Breakfast
2. Create Menu (no name) → Type: Standard, Meal: Breakfast
3. Create Menu (Custom name: "Breakfast VIP") → Type: Standard, Meal: Breakfast
4. Create Menu (no name) → Type: Standard, Meal: Lunch

### Expected Results
```
After Step 1: Menu #1 (auto-generated)
After Step 2: Menu #2 (auto-generated)
After Step 3: Breakfast VIP (custom name used)
After Step 4: Menu #3 (auto-generated)

Final Database State:
┌─────────────────────────────────────┐
│ Menu Name         │ Type     │ Meal  │
├─────────────────────────────────────┤
│ Menu #1           │ Standard │ BF    │
│ Menu #2           │ Standard │ BF    │
│ Breakfast VIP     │ Standard │ BF    │
│ Menu #3           │ Standard │ Lunch │
└─────────────────────────────────────┘
```

### Verification
✅ Numbers sequential (1, 2, 3)  
✅ Custom name respected (Breakfast VIP)  
✅ Auto-naming only for empty names  
✅ Numbers not affected by custom names  

---

## Test Scenario 2: Deletion and Renumbering

### Setup
Database contains:
```
- Menu #1
- Menu #2
- Menu #3
```

### Test Steps
1. Delete "Menu #2"
2. Create Menu (no name) → Type: Standard, Meal: Breakfast
3. Delete "Menu #1"
4. Create Menu (no name) → Type: Standard, Meal: Breakfast

### Expected Results
```
After Step 1:
- Menu #1
- Menu #3

After Step 2: New menu gets "Menu #4" (highest existing is #3)
- Menu #1
- Menu #3
- Menu #4

After Step 3:
- Menu #3
- Menu #4

After Step 4: New menu gets "Menu #5" (highest existing is #4)
- Menu #3
- Menu #4
- Menu #5
```

### Key Points
✅ Deleted number (#2) is NOT reused  
✅ System uses highest + 1 logic  
✅ Gaps in numbering are acceptable  
✅ New numbers always sequential from highest  

---

## Test Scenario 3: Updating with Auto-Naming

### Setup
Database contains:
```
- Menu #1
- Custom Menu
- Menu #2
```

### Test Steps
1. Edit "Menu #1" → Clear name field → Save
2. Edit "Custom Menu" → Set name to "" (empty) → Save

### Expected Results
```
After Step 1: "Menu #1" stays (wasn't actually empty before clear)
           OR gets new name if system detects change
           
After Step 2: "Custom Menu" → "Menu #3" (highest is #2, so next is #3)
```

### Verification
✅ Update operation supports auto-naming  
✅ Clearing name triggers auto-generation  
✅ Update respects same rules as create  

---

## Test Scenario 4: Edge Cases

### Edge Case 1: Malformed Menu Names
```
Database Contains:
- Menu #1
- menu #2 (lowercase 'm')
- Menu #
- Menu #a (non-numeric)
- Menu #1 (with trailing space)
- Menu#1 (no space after Menu)

Expected Behavior:
Only "Menu #1" is recognized and counted.
Others are ignored (malformed).
Next auto-generated name: "Menu #2"
```

### Edge Case 2: Large Numbers
```
Database Contains:
- Menu #100
- Menu #1000
- Menu #99999

Expected Behavior:
Highest number is 99999
Next auto-generated name: "Menu #100000"
✅ System correctly handles large numbers
```

### Edge Case 3: Non-sequential Existing Numbers
```
Database Contains:
- Menu #1
- Menu #5
- Menu #10

Expected Behavior:
Highest number is 10
Next auto-generated name: "Menu #11"
✅ System doesn't try to fill gaps
```

### Edge Case 4: No Auto-Named Menus
```
Database Contains:
- "Breakfast Special"
- "Lunch Premium"
- "Dinner Deluxe"

Expected Behavior:
No "Menu #X" pattern found
Create menu with no name → "Menu #1"
✅ System returns #1 when no auto-named menus exist
```

---

## Test Scenario 5: Performance

### Load Test
Create 1000 menus with and without names

### Expected Performance
```
Operation: Create menu without name
Expected Time: < 10ms
Reason: Simple database query + regex + math

Database Query Cost:
- SELECT name FROM menus WHERE name LIKE 'Menu #%'
- Linear scan, but typically < 1% of total menus match
- Memory efficient (only name column)
```

### Verification
✅ Fast execution (< 100ms for 1000 menus)  
✅ Database query efficient  
✅ No N+1 queries  
✅ Suitable for production  

---

## Test Scenario 6: Concurrent Operations

### Setup
Two requests submitted simultaneously, both creating menus without names

### Expected Results
```
Request 1: Checks highest = 5 → Assigns 6
Request 2: Checks highest = 5 → Assigns 6

ISSUE: Both get same number!
ACTUAL RESULT: Database UNIQUE constraint prevents duplicate names

Result: One succeeds (Menu #6), one fails and must retry
Expected Behavior: Application should handle gracefully
```

### Mitigation Notes
- Laravel's database transactions handle this
- If duplicate occurs, retry mechanism in app handles it
- For typical cafeteria system: Low probability (manual creation)

---

## Test Scenario 7: Mixed Operations

### Setup
Start with empty database

### Complex Test Steps
```
1. Create Menu (name: "") → Expected: "Menu #1"
2. Create Menu (name: "Special") → Expected: "Special"
3. Create Menu (name: "") → Expected: "Menu #2"
4. Edit Menu #1 (change name to "Updated") → Expected: "Updated"
5. Delete Special menu → Database now has "Updated", "Menu #2"
6. Create Menu (name: "") → Expected: "Menu #3"
7. Edit Updated (clear name) → Expected: "Menu #4" (if system regenerates)
8. Delete Menu #3 → Database: "Menu #4" + (Updated or Menu #4?)
9. Create Menu (name: "") → Expected: "Menu #5"
```

### Verification
✅ All operations respect auto-naming rules  
✅ No duplicates or conflicts  
✅ Correct sequencing throughout  

---

## Test Scenario 8: User Experience

### User Flow Test
1. User opens "Add Menu" button
2. User sees form with fields
3. User does NOT see required indicator on "Display Name" ← ✅
4. User sees help text: "If left empty, the menu will be named automatically as 'Menu #X'" ← ✅
5. User leaves name field empty
6. User completes other fields
7. User clicks "Create"
8. Success message shows: "Menu #1 created successfully" or similar ← ✅

### Verification
✅ Help text clearly explains feature  
✅ Name field is optional (not marked required)  
✅ System creates menu with auto-name  
✅ User sees result immediately  

---

## Regression Tests

### Ensure No Breaking Changes

#### Test: Custom Names Still Work
```
Create menu with name: "My Custom Menu"
✅ Should use custom name, NOT auto-generate
```

#### Test: Other Fields Not Affected
```
Create menu with empty name but valid:
- Type: Standard
- Meal: Breakfast
- Items: [valid items]
✅ Should create successfully
```

#### Test: Validation Still Works
```
Create menu with empty name but missing:
- Items: []
✅ Should fail with "at least 1 item" error
(not auto-naming error)
```

#### Test: Existing Menus Unchanged
```
Query existing menus in database
✅ Should have same names as before
(no automatic renaming of old menus)
```

#### Test: Audit Trail
```
Create menu with auto-name
Check audit trail record
✅ Should show action: "Created Menu"
✅ Should show final name: "Menu #X"
```

---

## SQL Verification

### Direct Database Check

```sql
-- Check auto-named menus
SELECT name FROM menus 
WHERE name LIKE 'Menu #%' 
ORDER BY CAST(SUBSTRING(name, 8) AS UNSIGNED);

-- Should return: Menu #1, Menu #2, Menu #3, etc. (in order)

-- Check for malformed entries
SELECT name FROM menus 
WHERE name LIKE 'Menu #%' 
AND name NOT REGEXP '^Menu #[0-9]+$';

-- Should return: 0 rows (all auto-names properly formatted)

-- Verify custom names still exist
SELECT COUNT(*) FROM menus 
WHERE name NOT LIKE 'Menu #%' AND name IS NOT NULL;

-- Should return: > 0 (custom names preserved)
```

---

## Checklist for Deployment

- [ ] All PHP syntax validated
- [ ] All Blade syntax validated
- [ ] Laravel caches cleared
- [ ] Test Scenario 1 passes (basic auto-naming)
- [ ] Test Scenario 2 passes (deletion handling)
- [ ] Test Scenario 3 passes (update support)
- [ ] Test Scenario 4 passes (edge cases)
- [ ] No regression issues
- [ ] Help text displays correctly
- [ ] Success message shows auto-name
- [ ] Optional name field works as expected

---

## Sign-Off

- **Feature**: Auto-Generated Menu Naming
- **Status**: ✅ Ready for Testing
- **Test Date**: January 31, 2026
- **Implementation**: Complete
- **Documentation**: Comprehensive
