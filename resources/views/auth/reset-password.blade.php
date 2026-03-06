<x-guest-layout>
    @php
        $passwordResetStatus = __('passwords.reset');
    @endphp
    <div class="min-h-screen flex items-center justify-center bg-admin-neutral-100 relative overflow-hidden font-admin text-admin-neutral-900 px-4">
        <div class="absolute inset-0">
            <div class="absolute -top-24 -right-20 h-80 w-80 rounded-full bg-admin-primary/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-ret-dark/10 blur-3xl"></div>
        </div>

        <x-success-modal name="password-reset-success" title="Success!" maxWidth="sm">
            <p class="text-sm text-admin-neutral-600">Password reset successfully. Redirecting to login...</p>
        </x-success-modal>

        @if(session('status') !== $passwordResetStatus)
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

                    <form method="POST" action="{{ route('password.store') }}" class="space-y-4" data-action-loading>
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

                        <div class="space-y-2" x-data="{ show: false }">
                            <x-input-label for="password" :value="__('Password')" class="text-admin-neutral-700 font-medium" />
                            <div class="relative">
                                <x-text-input id="password"
                                    name="password"
                                    x-bind:type="show ? 'text' : 'password'"
                                    class="block w-full pl-10 pr-10 h-11 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 focus:border-admin-primary focus:ring-admin-primary/20"
                                    required
                                    autocomplete="new-password"
                                    oninput="checkResetPasswordStrength(this.value)" />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-500 hover:text-admin-primary focus:outline-none">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                            @php
                                $passwordErrors = collect($errors->get('password'))
                                    ->reject(fn ($message) => \Illuminate\Support\Str::contains(strtolower($message), 'confirmation'))
                                    ->values()
                                    ->all();
                            @endphp
                            @if (!empty($passwordErrors))
                                <x-input-error :messages="$passwordErrors" class="mt-2 !text-admin-danger" />
                            @endif
                            <div class="flex gap-1 h-1.5 mt-1">
                                <div id="reset-bar-1" class="flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                                <div id="reset-bar-2" class="flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                                <div id="reset-bar-3" class="flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                                <div id="reset-bar-4" class="flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <span id="reset-req-len" class="text-[10px] px-2 py-0.5 rounded-full border border-gray-300 text-gray-400 bg-white transition-all font-bold uppercase">8+ CHARS</span>
                                <span id="reset-req-up" class="text-[10px] px-2 py-0.5 rounded-full border border-gray-300 text-gray-400 bg-white transition-all font-bold uppercase">UPPERCASE</span>
                                <span id="reset-req-num" class="text-[10px] px-2 py-0.5 rounded-full border border-gray-300 text-gray-400 bg-white transition-all font-bold uppercase">NUMBER</span>
                                <span id="reset-req-spec" class="text-[10px] px-2 py-0.5 rounded-full border border-gray-300 text-gray-400 bg-white transition-all font-bold uppercase">SYMBOL</span>
                            </div>
                        </div>

                        <div x-data="{ showConfirm: false }">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-admin-neutral-700 font-medium" />
                            <div class="relative">
                                <x-text-input id="password_confirmation"
                                    name="password_confirmation"
                                    x-bind:type="showConfirm ? 'text' : 'password'"
                                    class="block mt-1 w-full pl-10 pr-10 h-11 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 focus:border-admin-primary focus:ring-admin-primary/20"
                                    required
                                    autocomplete="new-password" />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-500 hover:text-admin-primary focus:outline-none">
                                    <svg x-show="!showConfirm" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg x-show="showConfirm" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                    </svg>
                                </button>
                            </div>
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
                            <x-primary-button class="w-full justify-center !rounded-admin bg-admin-primary hover:bg-admin-primary-hover focus:ring-admin-primary h-12 text-base font-semibold shadow-admin transition duration-300" data-loading-text="Resetting Password...">
                                {{ __('Reset Password') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <script>
        function checkResetPasswordStrength(password) {
            const requirements = {
                len: password.length >= 8,
                up: /[A-Z]/.test(password),
                num: /[0-9]/.test(password),
                spec: /[^A-Za-z0-9]/.test(password)
            };

            updateResetPill('reset-req-len', requirements.len);
            updateResetPill('reset-req-up', requirements.up);
            updateResetPill('reset-req-num', requirements.num);
            updateResetPill('reset-req-spec', requirements.spec);

            let score = Object.values(requirements).filter(Boolean).length;
            const colors = ['#e5e7eb', '#ef4444', '#f59e0b', '#10b981', '#059669'];

            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById('reset-bar-' + i);
                if (bar) {
                    bar.style.backgroundColor = (i <= score) ? colors[score] : '#e5e7eb';
                }
            }
        }

        function updateResetPill(id, isValid) {
            const el = document.getElementById(id);
            if (!el) return;

            if (isValid) {
                el.classList.remove('text-gray-400', 'bg-white', 'border-gray-300');
                el.classList.add('text-green-700', 'bg-green-100', 'border-green-300');
            } else {
                el.classList.add('text-gray-400', 'bg-white', 'border-gray-300');
                el.classList.remove('text-green-700', 'bg-green-100', 'border-green-300');
            }
        }
    </script>

    @if(session('status') === $passwordResetStatus)
    <script>
        window.addEventListener('load', function () {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'password-reset-success' }));
                setTimeout(() => {
                    window.location.href = "{{ route('login') }}";
                }, 2200);
            }, 50);
        });
    </script>
    @endif
</x-guest-layout>
