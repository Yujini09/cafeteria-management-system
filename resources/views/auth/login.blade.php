<x-guest-layout>
    @php
        $showForgotModal = session('forgot') || $errors->passwordReset->any();
    @endphp
    {{-- Main Container: Dark Green (bg-green-950) --}}
    <div class="min-h-screen flex items-center justify-center bg-green-950 relative overflow-hidden"> 
        
        {{-- External Background Bubble Designs: Orange and NEW RANDOMIZED POSITIONS --}}
        <div class="absolute inset-0 opacity-30">
            {{-- Large Bubble: Top-Center, slightly left --}}
            <div class="w-96 h-96 bg-orange-700 rounded-full absolute -top-20 left-1/4 mix-blend-screen opacity-50 transform -translate-x-1/2"></div>
            {{-- Mid Bubble: Bottom-Right edge --}}
            <div class="w-64 h-64 bg-orange-700 rounded-full absolute bottom-0 right-0 mix-blend-screen opacity-50 transform translate-x-1/4 translate-y-1/4"></div>
            {{-- Large Bubble: Mid-Left edge --}}
            <div class="w-80 h-80 bg-orange-700 rounded-full absolute top-1/4 left-0 mix-blend-screen opacity-50 transform -translate-x-1/2"></div>
            {{-- Small Bubble: Bottom-Center, slightly right --}}
            <div class="w-40 h-40 bg-orange-700 rounded-full absolute -bottom-10 left-1/2 mix-blend-screen opacity-50 transform translate-x-1/4"></div>
        </div>

        {{-- Card Container --}}
        <div class="bg-white rounded-xl shadow-2xl flex overflow-hidden w-full max-w-5xl z-10"> 
            
            {{-- Left side (Logo Display) - White background with subtle left shift --}}
            <div class="hidden md:flex w-1/2 items-center justify-center bg-white p-8 relative"> 
                <img src="{{ asset('images/caf-logo.png') }}" alt="RET Cafeteria Logo"
                     class="max-h-64 object-contain w-auto -ml-6"> 

                <a href="{{ route('marketing.home') }}"
                    class="absolute bottom-8 left-8 right-8 inline-flex items-center justify-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800 transition hover:border-orange-400 hover:text-orange-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Landing Page
                </a>
            </div>

            {{-- Right side (Login Form) - Light Green (bg-green-100) --}}
            <div class="w-full md:w-1/2 p-8 md:p-12 relative bg-green-100 h-[500px] overflow-y-auto"> 
                
                {{-- Internal bubbles: GREEN, RESTORED ORIGINAL POSITIONS --}}
                <div class="absolute inset-0 opacity-50 overflow-hidden">
                    {{-- Larger bubble: Top-right (Original position) --}}
                    <div class="w-64 h-64 bg-green-200 rounded-full absolute -top-24 -right-24"></div> 
                    {{-- Smaller bubble: Bottom-left (Original position) --}}
                    <div class="w-48 h-48 bg-green-200 rounded-full absolute -bottom-16 -left-16"></div> 
                </div>

                <div class="relative z-10"> 
                    
                    <div class="text-left mb-10">
                        <h2 class="text-green-900 text-4xl font-extrabold mb-2">Welcome!</h2> 
                        <p class="text-green-700 text-lg">Log in to your account</p> 
                    </div>

                    @php
                        $passwordResetStatus = __('passwords.reset');
                    @endphp

                    @if(session('status') && session('status') !== $passwordResetStatus)
                        <x-auth-session-status class="mb-6" :status="session('status')" />
                    @endif

                    <x-success-modal name="password-reset-success" title="Success!" maxWidth="sm">
                        <p class="text-sm text-admin-neutral-600">Your password has been reset. You can now log in.</p>
                    </x-success-modal>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        {{-- Email --}}
                        <div class="mb-6">
                            <div class="relative">
                                <x-text-input id="email" type="email" name="email"
                                    class="block mt-1 w-full pl-10 h-12 border-green-400 focus:border-orange-500 focus:ring-orange-500 rounded-lg placeholder-green-500 text-green-900"
                                    placeholder="Enter clsu email" 
                                    :value="old('email')" required autofocus autocomplete="username" />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" /> 
                        </div>

                        {{-- Password --}}
                        <div class="mb-6">
                            <div class="relative">
                                <x-text-input id="password" type="password" name="password"
                                    class="block mt-1 w-full pl-10 pr-10 h-12 border-green-400 focus:border-orange-500 focus:ring-orange-500 rounded-lg placeholder-green-500 text-green-900"
                                    placeholder="Enter password"
                                    required autocomplete="current-password" />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-green-600 hover:text-orange-500">
                                    <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" /> 
                        </div>

                        {{-- Forgot + Register CONSOLIDATED --}}
                        <div class="flex justify-between items-center text-sm mb-8">
                            @if (Route::has('password.request'))
                                <a id="openForgotPassword" href="{{ route('password.request') }}" class="text-orange-600 hover:text-orange-700 hover:underline transition duration-200">
                                    {{ __('Forgot Password?') }}
                                </a>
                            @endif
                            <p class="text-green-700">
                                {{ __("Don't have an Account?") }} 
                                <a href="{{ route('register') }}" class="text-orange-600 hover:text-orange-700 hover:underline transition duration-200 font-semibold">
                                    {{ __('Register') }}
                                </a>
                            </p>
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center mb-6">
                            <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-green-700">
                                Remember me
                            </label>
                        </div>

                        {{-- Login Button (Orange remains the CTA color for max contrast) --}}
                        <div>
                            <x-primary-button class="w-full justify-center bg-orange-500 hover:bg-orange-600 focus:ring-orange-500 h-12 text-lg font-semibold rounded-lg shadow-md transition duration-200 text-white">
                                {{ __('Login') }}
                            </x-primary-button>
                        </div>

                        {{-- Google OAuth Button --}}
                        <x-google-oauth-button />

                        {{-- Continue to site (landing page) --}}
                        <div class="mt-6 pt-6 border-t border-green-300/60">
                            <a href="{{ url('/') }}" class="flex items-center justify-center gap-2 w-full py-3 px-4 rounded-lg border-2 border-green-500 text-green-700 font-medium hover:bg-green-50 hover:border-green-600 transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                {{ __('Continue to site') }}
                            </a>
                            <p class="text-center text-sm text-green-600/80 mt-2">Browse the cafeteria without signing in</p>
                        </div>
                    </form>
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
                        };

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
