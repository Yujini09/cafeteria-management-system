@extends('layouts.sidebar')

@section('page-title', 'Account Settings')

@section('content')
<div x-data="{}" class="admin-page-shell bg-white rounded-admin-lg shadow-admin border border-admin-neutral-200 p-6 max-w-full relative overflow-hidden">
    {{-- Header matches admin cards: accent bar + gradient icon badge --}}
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-admin-primary to-admin-primary-hover"></div>
    <div class="page-header items-start pt-2">
        <div class="header-content">
            <div class="header-icon">
                <x-admin.ui.icon name="user" style="fas" />
            </div>
            <div class="header-text">
                <h1 class="header-title">Account Settings</h1>
                <p class="header-subtitle">Manage your profile and password securely.</p>
            </div>
        </div>
    </div>

    {{-- Success modals: use unified admin modal styles for consistency --}}
    <x-success-modal name="password-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
        <p class="text-sm text-admin-neutral-600">Password successfully changed.</p>
    </x-success-modal>

    <x-success-modal name="profile-success" title="Success!" maxWidth="sm">
        <p class="text-sm text-admin-neutral-600">Profile information successfully updated.</p>
    </x-success-modal>

    {{-- Options list --}}
    <div class="space-y-4">
        <button type="button" @click="$dispatch('open-admin-modal', 'profile-settings')"
                class="group w-full bg-admin-neutral-50 border border-admin-neutral-200 rounded-admin p-4 text-left hover:bg-admin-neutral-100 transition-colors duration-admin">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-admin bg-admin-primary-light flex items-center justify-center">
                        <x-admin.ui.icon name="user" style="far" class="text-admin-primary" />
                    </span>
                    <div>
                        <span class="block text-lg font-medium text-admin-neutral-900">Profile Settings</span>
                        <span class="text-sm text-admin-neutral-500">Update your name and contact info.</span>
                    </div>
                </div>
                <x-admin.ui.icon name="chevron-right" style="fas" class="text-admin-neutral-400 group-hover:text-admin-neutral-600 transition-colors duration-admin" />
            </div>
        </button>

        <button type="button" @click="$dispatch('open-admin-modal', 'change-password')"
                class="group w-full bg-admin-neutral-50 border border-admin-neutral-200 rounded-admin p-4 text-left hover:bg-admin-neutral-100 transition-colors duration-admin">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-admin bg-admin-primary-light flex items-center justify-center">
                        <x-admin.ui.icon name="lock" style="fas" class="text-admin-primary" />
                    </span>
                    <div>
                        <span class="block text-lg font-medium text-admin-neutral-900">Change Password</span>
                        <span class="text-sm text-admin-neutral-500">Keep your account secure.</span>
                    </div>
                </div>
                <x-admin.ui.icon name="chevron-right" style="fas" class="text-admin-neutral-400 group-hover:text-admin-neutral-600 transition-colors duration-admin" />
            </div>
        </button>
    </div>

    {{-- Profile Settings Modal --}}
    <x-admin.ui.modal name="profile-settings" title="Profile Settings" variant="info" maxWidth="md">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            <x-admin.forms.input name="name" label="Full Name" :value="old('name', $user->name)" required />

            @if($user->hasRole('admin'))
                <x-admin.forms.input
                    name="email"
                    label="Personal Email"
                    :value="old('email', $user->email)"
                    helper="Contact superadmin to change email address."
                    disabled
                    readonly
                    class="bg-admin-neutral-100 text-admin-neutral-500 cursor-not-allowed"
                />
            @else
                <x-admin.forms.input name="email" label="Personal Email" type="email" :value="old('email', $user->email)" required />
            @endif

            <div class="flex justify-end gap-3">
                <x-admin.ui.button.secondary type="button" @click="show = false">Cancel</x-admin.ui.button.secondary>
                <x-admin.ui.button.primary type="submit">Update Profile</x-admin.ui.button.primary>
            </div>
        </form>
    </x-admin.ui.modal>

    {{-- Change Password Modal --}}
    <x-admin.ui.modal name="change-password" title="Change Password" variant="info" maxWidth="md">
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <x-admin.forms.password name="current_password" label="Current Password" required />
            <x-admin.forms.password name="password" label="New Password" :showRequirements="true" required />
            <x-admin.forms.password name="password_confirmation" label="Confirm New Password" required />

            <div class="flex justify-end gap-3">
                <x-admin.ui.button.secondary type="button" @click="show = false">Cancel</x-admin.ui.button.secondary>
                <x-admin.ui.button.primary type="submit">Update Password</x-admin.ui.button.primary>
            </div>
        </form>
    </x-admin.ui.modal>
</div>

@if(session('status') == 'password-updated')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        requestAnimationFrame(() => {
            window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'password-success' }));
        });
    });
</script>
@endif
@if(session('status') == 'profile-updated')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        requestAnimationFrame(() => {
            window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'profile-success' }));
        });
    });
</script>
@endif
@if($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        requestAnimationFrame(() => {
            window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'change-password' }));
        });
    });
</script>
@endif
@endsection
