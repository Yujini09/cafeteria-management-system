<!-- ADMIN UI COMPONENTS - QUICK REFERENCE CARD -->

═══════════════════════════════════════════════════════════════════════════════
COMPONENT ANATOMY & COPY-PASTE TEMPLATES
═══════════════════════════════════════════════════════════════════════════════

BUTTONS (Minimal Copy-Paste)
───────────────────────────

<!-- Primary Button (Green gradient) -->
<x-admin.buttons.primary type="submit">Save</x-admin.buttons.primary>
<x-admin.buttons.primary @click="doAction()">Create New</x-admin.buttons.primary>

<!-- Secondary Button (Gray) -->
<x-admin.buttons.secondary onclick="window.history.back()">Cancel</x-admin.buttons.secondary>

<!-- Danger Button (Red) -->
<x-admin.buttons.danger onclick="if(confirm('Delete?')) deleteItem()">Delete</x-admin.buttons.danger>

<!-- Icon-Only Button -->
<x-admin.buttons.icon-only class="bg-blue-100 text-blue-600 hover:bg-blue-200">
    <x-admin.icons.icon type="edit" class="w-4 h-4" />
</x-admin.buttons.icon-only>


