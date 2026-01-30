{{--
  Admin toast container: lives in sidebar layout. Listens for toast events and shows success/error/warning.
  Dispatch from JS: window.dispatchEvent(new CustomEvent('admin-toast', { detail: { type: 'success', message: 'Saved.' } }));
  Or from Alpine: $dispatch('admin-toast', { type: 'success', message: 'Saved.' })
  Removes browser alerts and duplicated notification logic.
--}}
<div
    x-data="{
        toasts: [],
        addToast(payload) {
            const id = Date.now();
            this.toasts.push({ id, type: payload.type || 'success', message: payload.message });
            setTimeout(() => this.remove(id), payload.duration ?? 5000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    x-on:admin-toast.window="addToast($event.detail)"
    @keydown.escape.window="toasts = []"
    aria-live="polite"
    class="fixed top-4 right-4 z-[60] flex flex-col gap-3 max-w-sm w-full pointer-events-none"
>
    <template x-for="t in toasts" :key="t.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            :class="{
                'bg-admin-success-light border-admin-success text-admin-neutral-800': t.type === 'success',
                'bg-admin-danger-light border-admin-danger text-admin-neutral-800': t.type === 'error',
                'bg-admin-warning-light border-admin-warning text-admin-neutral-800': t.type === 'warning'
            }"
            class="pointer-events-auto rounded-admin border px-4 py-3 shadow-admin flex items-start gap-3"
        >
            <span class="shrink-0 mt-0.5" x-show="t.type === 'success'">
                <svg class="w-5 h-5 text-admin-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <span class="shrink-0 mt-0.5" x-show="t.type === 'error'">
                <svg class="w-5 h-5 text-admin-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </span>
            <span class="shrink-0 mt-0.5" x-show="t.type === 'warning'">
                <svg class="w-5 h-5 text-admin-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </span>
            <p class="text-sm font-medium flex-1" x-text="t.message"></p>
            <button type="button" @click="remove(t.id)" class="shrink-0 text-admin-neutral-500 hover:text-admin-neutral-700 focus:outline-none">
                <span class="sr-only">Dismiss</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>
