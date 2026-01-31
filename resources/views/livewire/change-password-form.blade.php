@php
    $ruleLabels = \App\Support\PasswordRules::ruleLabels();
    $ruleKeys = array_keys($ruleLabels);
    $rulesJson = json_encode($ruleLabels);
    $ruleKeysJson = json_encode($ruleKeys);
@endphp
<form wire:submit.prevent="updatePassword" class="space-y-4">
    {{-- Current Password --}}
    <div class="space-y-1" x-data="{ show: false }">
        <label for="current_password" class="block text-sm font-medium text-admin-neutral-700">Current Password <span class="text-red-500">*</span></label>
        <div class="relative">
            <input type="password" :type="show ? 'text' : 'password'" id="current_password" wire:model="current_password"
                   class="w-full rounded-admin border border-admin-neutral-300 px-admin-input py-2.5 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-admin-primary/20"
                   autocomplete="current-password" required>
            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-500 hover:text-admin-neutral-700">
                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
            </button>
        </div>
        @error('current_password')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- New Password with rules (Alpine passwordWithRules reads from Livewire via x-effect) --}}
    <div class="space-y-1" x-data="passwordWithRules({{ $rulesJson }}, {{ $ruleKeysJson }})" x-effect="password = ($wire.password ?? '')">
        <label for="password" class="block text-sm font-medium text-admin-neutral-700">New Password <span class="text-red-500">*</span></label>
        <div class="relative">
            <input type="password" :type="show ? 'text' : 'password'" id="password" wire:model.live="password"
                   class="w-full rounded-admin border border-admin-neutral-300 px-admin-input py-2.5 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-admin-primary/20"
                   autocomplete="new-password" required>
            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-500 hover:text-admin-neutral-700">
                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
            </button>
        </div>
        <ul class="mt-2 space-y-1.5 text-xs" role="list">
            <template x-for="key in ruleKeys" :key="key">
                <li class="flex items-center gap-2">
                    <span x-show="passed(key)" class="text-emerald-600">✔</span>
                    <span x-show="!passed(key)" x-cloak class="text-red-500">✖</span>
                    <span :class="passed(key) ? 'text-admin-neutral-600' : 'text-admin-neutral-500'" x-text="ruleLabels[key]"></span>
                </li>
            </template>
        </ul>
        @error('password')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Confirm New Password --}}
    <div class="space-y-1" x-data="{ show: false }">
        <label for="password_confirmation" class="block text-sm font-medium text-admin-neutral-700">Confirm New Password <span class="text-red-500">*</span></label>
        <div class="relative">
            <input type="password" :type="show ? 'text' : 'password'" id="password_confirmation" wire:model="password_confirmation"
                   class="w-full rounded-admin border border-admin-neutral-300 px-admin-input py-2.5 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-admin-primary/20"
                   autocomplete="new-password" required>
            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-500 hover:text-admin-neutral-700">
                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
            </button>
        </div>
        @error('password_confirmation')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex justify-end gap-3">
        <x-admin.ui.button.secondary type="button" @click="$dispatch('close-admin-modal', 'change-password')">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.primary type="submit">Update Password</x-admin.ui.button.primary>
    </div>
</form>
