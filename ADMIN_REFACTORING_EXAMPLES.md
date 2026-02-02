<!-- ADMIN COMPONENT REFACTORING EXAMPLES -->

This file demonstrates how to refactor common admin pages to use the new component system.

═══════════════════════════════════════════════════════════════════════════════
EXAMPLE 1: SIMPLE USER FORM (Create/Edit)
═══════════════════════════════════════════════════════════════════════════════

BEFORE (Old Style):
─────────────────
<form method="POST" action="/admin/users" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
        <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
        @error('name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
        <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
        @error('email')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
    </div>
    <div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
        <a href="/admin/users" class="text-gray-600 px-4 py-2">Cancel</a>
    </div>
</form>

AFTER (New Style):
─────────────────
<form method="POST" action="/admin/users" class="space-y-6">
    @csrf
    
    <x-admin.forms.text-input 
        label="Full Name"
        name="name"
        placeholder="Enter full name"
        required
    />
    
    <x-admin.forms.text-input 
        label="Email Address"
        name="email"
        type="email"
        placeholder="user@example.com"
        required
    />
    
    <div class="flex gap-3 justify-start pt-6 border-t border-gray-200">
        <x-admin.buttons.secondary onclick="window.history.back()">
            Cancel
        </x-admin.buttons.secondary>
        <x-admin.buttons.primary type="submit">
            Save User
        </x-admin.buttons.primary>
    </div>
</form>

Benefits:
✓ Consistent spacing and layout
✓ Built-in error message handling
✓ Uniform input styling
✓ Better accessibility
✓ Cleaner, more readable code

═══════════════════════════════════════════════════════════════════════════════
EXAMPLE 2: DATA TABLE WITH ACTIONS
═══════════════════════════════════════════════════════════════════════════════

BEFORE (Old Style):

─────────────────
