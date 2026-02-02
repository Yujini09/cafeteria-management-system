# Admin UI Components (Superadmin / Admin)

Unified design system for the admin/superadmin interface. Use these components only in admin/sidebar views.

## Design tokens (Tailwind)

- **Colors**: `admin-primary`, `admin-primary-hover`, `admin-secondary`, `admin-danger`, `admin-neutral-50` … `admin-neutral-900`, `admin-success`, `admin-warning`, etc.
- **Radius**: `rounded-admin` (10px), `rounded-admin-lg` (16px)
- **Spacing**: `px-admin-input`, `admin-form-gap`
- **Shadow**: `shadow-admin`, `shadow-admin-modal`
- **Transition**: `duration-admin` (200ms)

## Buttons

- `<x-admin.ui.button.primary>` — main actions (submit, create, save)
- `<x-admin.ui.button.secondary>` — cancel, close, neutral
- `<x-admin.ui.button.danger>` — delete, destructive
- `<x-admin.ui.button.icon variant="secondary|primary|danger">` — icon-only (consistent size)

Override `type` when needed: `type="button"` for non-submit.

## Forms

- `<x-admin.forms.input name="..." label="..." type="text|email" required />` — optional `id`, `value`, `helper`
- `<x-admin.forms.password name="password" :showRequirements="true" />` — only on create/reset password forms (not login)
- `<x-admin.forms.select name="..." label="..." :options="[...]" placeholder="..." />`
- `<x-admin.forms.textarea name="..." label="..." rows="4" />`

Labels, spacing, errors, and helper text are consistent. Password has built-in eye toggle (Alpine).

## Modal

- `<x-admin.ui.modal name="uniqueName" title="..." variant="confirmation|warning|error|info" maxWidth="md">`
  - Body: default slot. Footer: `<x-slot:footer>...</x-slot:footer>`
  - **Open**: `$dispatch('open-admin-modal', 'uniqueName')` or `window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'uniqueName' }))`
  - **Close**: `@click="show = false"` in footer (same Alpine scope), or ESC / click overlay
  - Body scroll is locked when open.

## Toast

- Layout includes `<x-admin.ui.toast-container />`. Session flash `success`/`error`/`warning` are shown as toasts automatically.
- From JS: `window.dispatchEvent(new CustomEvent('admin-toast', { detail: { type: 'success', message: 'Saved.' } }))`
- From Alpine: `$dispatch('admin-toast', { type: 'success', message: 'Saved.' })`

## Icon

- `<x-admin.ui.icon name="user" />` or `name="fa-plus"` — Font Awesome, single size/style. Optional `style="fas|far"`, `size="sm|default|lg"`.

## UX

- Modals: overlay with blur, ESC and click-outside close, body scroll disabled when open.
- Buttons and modals use short transitions (`duration-admin`).
- No browser `alert()`; use toasts for feedback.

## Example (superadmin users)

See `resources/views/superadmin/users.blade.php` for full usage of buttons, forms, modals, toasts, and icons.
