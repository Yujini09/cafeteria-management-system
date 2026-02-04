<x-guest-layout>
    @php
        $passwordResetStatus = __('passwords.reset');
    @endphp
    <div class="min-h-screen flex items-center justify-center bg-admin-neutral-100 relative overflow-hidden font-admin text-admin-neutral-900 px-4">
        <div class="absolute inset-0">
            <div class="absolute -top-24 -right-20 h-80 w-80 rounded-full bg-admin-primary/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-ret-dark/10 blur-3xl"></div>
        </div>

        <div class="relative w-full max-w-md overflow-hidden rounded-admin-lg border border-admin-neutral-200 bg-white shadow-admin-modal z-10">
            <div class="flex items-start justify-between gap-4 border-b border-admin-neutral-100 bg-admin-neutral-50 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-admin-neutral-900">Reset Password</h2>
                    <p class="text-xs text-admin-neutral-600 mt-1">Create your new password.</p>
                </div>
            </div>

            <div class="px-6 py-5">
                <div class="mb-4 text-sm text-admin-neutral-600 bg-admin-neutral-50 p-3 rounded-admin border border-admin-neutral-200 border-l-4 border-l-admin-primary">
                    Enter and confirm your new password to continue.
                </div>

                <x-success-modal name="password-reset-success" title="Success!" maxWidth="sm">
                    <p class="text-sm text-admin-neutral-600">Password reset successfully. Redirecting to login...</p>
                </x-success-modal>

                @if(session('status') !== $passwordResetStatus)
                    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                        @csrf

                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="relative">
                            <x-input-label for="email" :value="__('Email')" class="text-admin-neutral-700 font-medium mb-2" />
                            <div class="relative">
                                <x-text-input id="email"
                                    class="block mt-1 w-full pl-10 h-12 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 transition-all duration-300 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary cursor-not-allowed"
                                    type="email"
                                    name="email"
                                    :value="old('email', $request->email)"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    readonly />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 !text-admin-danger" />
                        </div>

                        <div>
                            {!! app('livewire')->mount('password-with-rules', [
                                'name' => 'password',
                                'label' => __('Password'),
                                'showRequirements' => true,
                                'required' => true,
                                'variant' => 'auth',
                            ]) !!}
                        </div>

                        <div>
                            {!! app('livewire')->mount('password-with-rules', [
                                'name' => 'password_confirmation',
                                'label' => __('Confirm Password'),
                                'showRequirements' => false,
                                'required' => true,
                                'variant' => 'auth',
                            ]) !!}
                            @php
                                $confirmPasswordErrors = $errors->get('password_confirmation');
                                $confirmedError = collect($errors->get('password'))
                                    ->first(fn ($message) => \Illuminate\Support\Str::contains(strtolower($message), 'confirmation'));
                            @endphp
                            @if (!empty($confirmPasswordErrors))
                                <x-input-error :messages="$confirmPasswordErrors" class="mt-2 !text-admin-danger" />
                            @elseif (!empty($confirmedError))
                                <x-input-error :messages="[$confirmedError]" class="mt-2 !text-admin-danger" />
                            @endif
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button class="w-full justify-center !rounded-admin bg-admin-primary hover:bg-admin-primary-hover focus:ring-admin-primary h-12 text-base font-semibold shadow-admin transition duration-300">
                                {{ __('Reset Password') }}
                            </x-primary-button>
                        </div>
                    </form>
                @endif

                <div class="mt-4 flex justify-center text-sm">
                    <a href="{{ route('login') }}" class="text-admin-primary hover:text-admin-primary-hover hover:underline transition duration-200">
                        Back to login
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('status') === $passwordResetStatus)
    <script>
        window.addEventListener('load', function () {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'password-reset-success' }));
            }, 50);
        });
    </script>
    @endif
</x-guest-layout>
