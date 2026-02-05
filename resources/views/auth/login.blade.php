<x-guest-layout>
    @php
        $showForgotModal = session('forgot') || $errors->passwordReset->any();
        $passwordResetStatus = __('passwords.reset');
        $passwordLinkStatus = __('passwords.sent');
    @endphp
    {{-- Main Container --}}
    <div class="min-h-screen flex items-center justify-center bg-admin-neutral-100 relative overflow-hidden font-admin text-admin-neutral-900"> 
        <div class="absolute inset-0 opacity-30">
            {{-- Large Bubble: Top-Center, slightly left --}}
            <div class="w-96 h-96 bg-green-700 rounded-full absolute -top-20 left-1/4 mix-blend-screen opacity-50 transform -translate-x-1/2"></div>
            {{-- Mid Bubble: Bottom-Right edge --}}
            <div class="w-64 h-64 bg-green-700 rounded-full absolute bottom-0 right-0 mix-blend-screen opacity-50 transform translate-x-1/4 translate-y-1/4"></div>
            {{-- Large Bubble: Mid-Left edge --}}
            <div class="w-80 h-80 bg-green-700 rounded-full absolute top-1/4 left-0 mix-blend-screen opacity-50 transform -translate-x-1/2"></div>
            {{-- Small Bubble: Bottom-Center, slightly right --}}
            <div class="w-40 h-40 bg-green-700 rounded-full absolute -bottom-10 left-1/2 mix-blend-screen opacity-50 transform translate-x-1/4"></div>
        </div>

        {{-- Card Container --}}
            <div class="relative w-full max-w-5xl overflow-hidden rounded-admin-lg border border-admin-neutral-200 bg-white shadow-admin z-10 mx-4 flex flex-col md:flex-row md:h-[560px]"> 
            
            {{-- Left side (Logo Display) --}}
            <div class="hidden md:flex w-1/2 items-center justify-center bg-white p-8 relative"> 
                <a href="{{ route('marketing.home') }}"
                class="absolute top-6 left-6 inline-flex items-center justify-center rounded-lg p-2 text-green-800 transition hover:border-orange-400 hover:text-orange-600 z-20">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <img src="{{ asset('images/caf-logo.png') }}" alt="RET Cafeteria Logo"
                     class="max-h-64 object-contain w-auto -ml-6"> 

            </div>

            {{-- Right side (Login Form) --}}
            <div class="w-full md:w-7/12 p-8 md:p-12 relative  bg-green-100 overflow-hidden overflow-x-hidden md:h-full"> 
                <div class="absolute inset-0 pointer-events-none">
                    <div class="w-56 h-56 bg-green-200 rounded-full absolute -top-20 -right-16"></div> 
                    <div class="w-40 h-40 bg-green-200 rounded-full absolute -bottom-16 -left-16"></div> 
                </div>

                <div class="relative z-10 flex h-full flex-col"> 
                    <div class="flex flex-col gap-3 mb-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="md:hidden h-12 w-12 rounded-admin bg-ret-dark/90 flex items-center justify-center">
                                    <img src="{{ asset('images/caf-logo.png') }}" alt="RET Cafeteria Logo"
                                         class="h-8 w-8 object-contain"> 
                                </div>
                                <div>
                                    <h2 class="text-green-900 text-4xl font-extrabold mb-1">Welcome!</h2> 
                                        <p class="text-green-700 text-lg">Log in to your account</p> 
                                </div>
                            </div>
                            <a href="{{ route('marketing.home') }}"
                                class="md:hidden inline-flex items-center justify-center gap-2 rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2 text-xs font-semibold text-admin-neutral-700 transition hover:bg-admin-neutral-100">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Continue
                            </a>
                        </div>
                    </div>

                    @if(session('status') && session('status') !== $passwordResetStatus && session('status') !== $passwordLinkStatus)
                        <div class="mb-6 rounded-admin border border-admin-success/20 bg-admin-success-light px-4 py-3">
                            <x-auth-session-status class="!text-admin-success" :status="session('status')" />
                        </div>
                    @endif

                    <x-success-modal name="password-reset-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
                        <p class="text-sm text-admin-neutral-600">Your password has been reset. You can now log in.</p>
                    </x-success-modal>

                    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6">
                        @csrf
                        
                        {{-- Email --}}
                        <div>
                            <div class="relative">
                                <x-text-input id="email" type="email" name="email"
                                    class="block mt-1 w-full pl-10 h-12 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 focus:border-admin-primary focus:ring-admin-primary/20"
                                    placeholder="Enter clsu email" 
                                    :value="old('email')" required autofocus autocomplete="username" />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 !text-admin-danger" /> 
                        </div>

                        {{-- Password --}}
                        <div>
                            <div class="relative">
                                <x-text-input id="password" type="password" name="password"
                                    class="block mt-1 w-full pl-10 pr-10 h-12 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 focus:border-admin-primary focus:ring-admin-primary/20"
                                    placeholder="Enter password"
                                    required autocomplete="current-password" />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-500 hover:text-admin-primary">
                                    <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 !text-admin-danger" /> 
                        </div>

                        {{-- Remember + Forgot --}}
                        <div class="flex items-center justify-between text-sm">
                            <label for="remember_me" class="flex items-center gap-2 text-admin-neutral-600">
                                <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-admin-primary focus:ring-admin-primary/20 border-admin-neutral-300 rounded">
                                <span>Remember me</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a id="openForgotPassword" href="{{ route('password.request') }}" class="text-admin-primary hover:text-admin-primary-hover hover:underline transition duration-200">
                                    {{ __('Forgot Password?') }}
                                </a>
                            @endif
                        </div>

                        {{-- Login Button (Orange remains the CTA color for max contrast) --}}
                        <div>
                            <x-primary-button class="w-full justify-center !rounded-admin bg-admin-primary hover:bg-admin-primary-hover focus:ring-admin-primary h-12 text-base font-semibold shadow-admin transition duration-200 text-white">
                                {{ __('Login') }}
                            </x-primary-button>
                        </div>
                        </form>
                        
                        <div class="mt-3">
                        <div class="flex items-center gap-3 text-xs uppercase tracking-[0.2em] text-admin-neutral-400">
                            <span class="h-px flex-1 bg-admin-neutral-200"></span>
                            <span>or</span>
                            <span class="h-px flex-1 bg-admin-neutral-200"></span>
                        </div>
                        <div class="mt-4">
                            {{-- Google OAuth Button --}}
                            <x-google-oauth-button />
                        </div>
                    </div>
                    

                    <div class="mt-6 text-center text-sm text-admin-neutral-600">
                        {{ __("Don't have an Account?") }} 
                        <a href="{{ route('register') }}" class="text-admin-primary hover:text-admin-primary-hover hover:underline transition duration-200 font-semibold">
                            {{ __('Register') }}
                        </a>
                    </div>
                </div> 

                <div id="forgotPasswordModal"
                    class="{{ $showForgotModal ? '' : 'hidden' }} fixed inset-0 z-50 flex items-center justify-center p-4"
                    role="dialog"
                    aria-modal="true"
                    aria-hidden="{{ $showForgotModal ? 'false' : 'true' }}"
                    aria-labelledby="forgotPasswordTitle">
                    <div id="forgotPasswordBackdrop" class="absolute inset-0 bg-admin-neutral-900/50 backdrop-blur-sm"></div>
                    <div class="relative w-full max-w-md overflow-hidden rounded-admin-lg border border-admin-neutral-200 bg-white shadow-admin-modal">
                        <div class="flex items-start justify-between gap-4 border-b border-admin-neutral-100 bg-admin-neutral-50 px-6 py-4">
                            <div>
                                <h2 id="forgotPasswordTitle" class="text-lg font-semibold text-admin-neutral-900">Reset Password</h2>
                                <p class="text-xs text-admin-neutral-600 mt-1">We will email you a reset link.</p>
                            </div>
                            <button id="closeForgotPassword" type="button" class="rounded-full p-1 text-admin-neutral-500 hover:text-admin-primary">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="px-6 py-5">
                            <div class="mb-4 text-sm text-admin-neutral-600 bg-admin-neutral-50 p-3 rounded-admin border border-admin-neutral-200 border-l-4 border-l-admin-primary">
                                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                            </div>

                            @if(session('status') === $passwordLinkStatus)
                                <div class="mb-4 text-sm text-admin-success bg-admin-success-light p-3 rounded-admin border border-admin-success/20">
                                    {{ session('status') }}
                                </div>
                            @endif

                            @php
                                $emailError = $errors->passwordReset->first('email');
                                $isThrottled = false;
                                foreach (['passwords.throttled', 'throttled', 'too many', 'wait before retrying', 'please wait'] as $needle) {
                                    if ($emailError && stripos($emailError, $needle) !== false) {
                                        $isThrottled = true;
                                        break;
                                    }
                                }
                                $throttleSeconds = (int) config('auth.passwords.users.throttle');
                                $throttleMinutes = $throttleSeconds > 0 ? (int) ceil($throttleSeconds / 60) : null;
                            @endphp

                            @if($emailError && $isThrottled)
                                <div class="mb-4 text-sm text-admin-danger bg-admin-danger-light p-3 rounded-admin border border-admin-danger/20">
                                    You just have a recent reset request. Please wait
                                    @if($throttleMinutes)
                                        {{ $throttleMinutes }} minute{{ $throttleMinutes === 1 ? '' : 's' }}
                                    @else
                                        a few minutes
                                    @endif
                                    and try again.
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}" class="space-y-4" x-data="{ sending: false }" @submit="if (sending) { $event.preventDefault(); return; } sending = true">
                                @csrf

                                <div class="relative">
                                    <x-input-label for="forgot_email" :value="__('Email')" class="text-admin-neutral-700 font-medium mb-2" />
                                    <div class="relative">
                                        <x-text-input id="forgot_email"
                                            class="block mt-1 w-full pl-10 h-12 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 transition-all duration-300 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                                            type="email"
                                            name="email"
                                            :value="old('email')"
                                            required
                                            autocomplete="email" />
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                        </svg>
                                    </div>
                                    <x-input-error :messages="$errors->passwordReset->get('email')" class="mt-2 !text-admin-danger" />
                                </div>

                                <div class="flex items-center justify-end">
                                    <x-primary-button class="w-full justify-center !rounded-admin bg-admin-primary hover:bg-admin-primary-hover focus:ring-admin-primary h-12 text-base font-semibold shadow-admin transition duration-300"
                                        x-bind:disabled="sending"
                                        x-bind:class="sending ? 'opacity-70 cursor-not-allowed' : ''">
                                        <span x-text="sending ? 'Email is currently sending...' : 'Email Password Reset Link'">Email Password Reset Link</span>
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if(session('status') === $passwordResetStatus)
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'password-reset-success' }));
                        setTimeout(() => {
                            window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'password-reset-success' }));
                        }, 2500);
                    });
                </script>
                @endif

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const togglePassword = document.getElementById('togglePassword');
                        const passwordInput = document.getElementById('password');
                        const eyeIcon = document.getElementById('eyeIcon');

                        togglePassword?.addEventListener('click', () => {
                            if (!passwordInput || !eyeIcon) return;
                            if (passwordInput.type === 'password') {
                                passwordInput.type = 'text';
                                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>';
                            } else {
                                passwordInput.type = 'password';
                                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                            }
                        });

                        const forgotModal = document.getElementById('forgotPasswordModal');
                        const openForgotPassword = document.getElementById('openForgotPassword');
                        const closeForgotPassword = document.getElementById('closeForgotPassword');
                        const forgotPasswordBackdrop = document.getElementById('forgotPasswordBackdrop');

                        const setForgotModalOpen = (isOpen) => {
                            if (!forgotModal) return;
                            forgotModal.classList.toggle('hidden', !isOpen);
                            forgotModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
                            if (isOpen) {
                                document.getElementById('forgot_email')?.focus();
                            }
                        };

                        const shouldOpenForgotModal = @json($showForgotModal);
                        if (shouldOpenForgotModal) {
                            setForgotModalOpen(true);
                        }

                        openForgotPassword?.addEventListener('click', (event) => {
                            event.preventDefault();
                            setForgotModalOpen(true);
                        });

                        closeForgotPassword?.addEventListener('click', () => setForgotModalOpen(false));
                        forgotPasswordBackdrop?.addEventListener('click', () => setForgotModalOpen(false));
                        document.addEventListener('keydown', (event) => {
                            if (event.key === 'Escape' && forgotModal && !forgotModal.classList.contains('hidden')) {
                                setForgotModalOpen(false);
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</x-guest-layout>
