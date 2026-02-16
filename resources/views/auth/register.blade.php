<x-guest-layout>
    {{-- Main Container --}}
    <div class="min-h-screen flex items-center justify-center bg-admin-neutral-100 relative overflow-hidden font-admin text-admin-neutral-900"> 
        <div class="absolute inset-0 opacity-30">
            <div class="w-96 h-96 bg-green-700 rounded-full absolute -top-20 left-1/4 mix-blend-screen opacity-50 transform -translate-x-1/2"></div>
            <div class="w-64 h-64 bg-green-700 rounded-full absolute bottom-0 right-0 mix-blend-screen opacity-50 transform translate-x-1/4 translate-y-1/4"></div>
            <div class="w-80 h-80 bg-green-700 rounded-full absolute top-1/4 left-0 mix-blend-screen opacity-50 transform -translate-x-1/2"></div>
            <div class="w-40 h-40 bg-green-700 rounded-full absolute -bottom-10 left-1/2 mix-blend-screen opacity-50 transform translate-x-1/4"></div>
        </div>

        {{-- Card Container --}}
        <div class="relative w-full max-w-5xl overflow-hidden rounded-admin-lg border border-admin-neutral-200 bg-white shadow-admin z-10 mx-4 flex flex-col md:flex-row md:h-[650px]"> 
            
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

            {{-- Right side (Form) --}}
            <div class="w-full md:w-7/12 p-8 pb-16 md:px-12 md:pt-12 md:pb-20 relative bg-green-100 overflow-y-auto overflow-x-hidden scroll-pb-10 md:h-full"> 
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
                                    <h2 class="text-green-900 text-4xl font-extrabold mb-1">Create Account</h2> 
                                    <p class="text-green-700 text-lg">Join our cafeteria community</p>
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

                    <form method="POST" action="{{ route('register') }}" id="registerForm" class="flex flex-col gap-5">
                        @csrf

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="name" :value="__('Name')" class="text-admin-neutral-700 font-medium" />
                                <div class="relative">
                                    <x-text-input id="name" name="name" type="text" class="block mt-1 w-full pl-10 h-11 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 focus:border-admin-primary focus:ring-admin-primary/20" required autofocus />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="contact_no" :value="__('Contact No')" class="text-admin-neutral-700 font-medium" />
                                <div class="relative">
                                    <x-text-input id="contact_no" name="contact_no" type="text" class="block mt-1 w-full pl-10 h-11 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 focus:border-admin-primary focus:ring-admin-primary/20" />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="address" :value="__('Address')" class="text-admin-neutral-700 font-medium" />
                                <div class="relative">
                                    <x-text-input id="address" name="address" type="text" class="block mt-1 w-full pl-10 h-11 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 focus:border-admin-primary focus:ring-admin-primary/20" />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" class="text-admin-neutral-700 font-medium" />
                                <div class="relative">
                                    <x-text-input id="email" name="email" type="email" class="block mt-1 w-full pl-10 h-11 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 focus:border-admin-primary focus:ring-admin-primary/20" required />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="department" :value="__('Department/Office')" class="text-admin-neutral-700 font-medium" />
                                <div class="relative">
                                    <x-text-input id="department" name="department" type="text" class="block mt-1 w-full pl-10 h-11 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 focus:border-admin-primary focus:ring-admin-primary/20" />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- CUSTOM REAL-TIME PASSWORD FIELD WITH EYE ICON --}}
                        <div class="space-y-2" x-data="{ show: false }">
                            <x-input-label for="password" :value="__('Password')" class="text-admin-neutral-700 font-medium" />
                            <div class="relative">
                                <x-text-input id="password" name="password" x-bind:type="show ? 'text' : 'password'"
                                    class="block w-full pl-10 pr-10 h-11 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 focus:border-admin-primary focus:ring-admin-primary/20" 
                                    required onkeyup="checkPasswordStrength(this.value)"/>
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                
                                {{-- Eye Toggle Button --}}
                                <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-500 hover:text-admin-primary focus:outline-none">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path></svg>
                                </button>
                            </div>
                            
                            {{-- Visual Strength Bar --}}
                            <div class="flex gap-1 h-1.5 mt-2">
                                <div id="bar-1" class="flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                                <div id="bar-2" class="flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                                <div id="bar-3" class="flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                                <div id="bar-4" class="flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                            </div>

                            {{-- Compact Requirement Pills --}}
                            <div class="flex flex-wrap gap-2 mt-1">
                                <span id="req-len" class="text-[10px] px-2 py-0.5 rounded-full border border-gray-300 text-gray-400 bg-white transition-all font-bold uppercase">8+ CHARS</span>
                                <span id="req-up" class="text-[10px] px-2 py-0.5 rounded-full border border-gray-300 text-gray-400 bg-white transition-all font-bold uppercase">UPPERCASE</span>
                                <span id="req-num" class="text-[10px] px-2 py-0.5 rounded-full border border-gray-300 text-gray-400 bg-white transition-all font-bold uppercase">NUMBER</span>
                                <span id="req-spec" class="text-[10px] px-2 py-0.5 rounded-full border border-gray-300 text-gray-400 bg-white transition-all font-bold uppercase">SYMBOL</span>
                            </div>
                        </div>

                        {{-- CONFIRM PASSWORD WITH EYE ICON --}}
                        <div x-data="{ showConfirm: false }">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-admin-neutral-700 font-medium" />
                            <div class="relative">
                                <x-text-input id="password_confirmation" name="password_confirmation" x-bind:type="showConfirm ? 'text' : 'password'"
                                    class="block mt-1 w-full pl-10 pr-10 h-11 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 focus:border-admin-primary focus:ring-admin-primary/20" 
                                    required />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                
                                {{-- Eye Toggle Button --}}
                                <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-500 hover:text-admin-primary focus:outline-none">
                                    <svg x-show="!showConfirm" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="showConfirm" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <x-primary-button class="w-full justify-center !rounded-admin bg-admin-primary hover:bg-admin-primary-hover focus:ring-admin-primary h-12 text-base font-semibold shadow-admin transition duration-200 text-white" id="registerBtn" data-loading-text="Creating Account...">
                                {{ __('Register') }}
                            </x-primary-button>
                        </div>
                    </form>
                    
                    <div class="mt-3">
                        <div class="flex items-center gap-3 text-xs uppercase tracking-[0.2em] text-admin-neutral-400">
                            <span class="h-px flex-1 bg-admin-neutral-200"></span><span>or</span><span class="h-px flex-1 bg-admin-neutral-200"></span>
                        </div>
                        <div class="mt-4">
                            <x-google-oauth-button />
                        </div>
                        <div class="flex justify-center text-sm mt-4 pb-8"> 
                            <a href="{{ route('login') }}" class="text-admin-primary hover:text-admin-primary-hover hover:underline transition duration-200">
                                {{ __('Have an account already?') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Verification & Error Modals (Kept Same) --}}
    <div id="verificationModal" class="hidden fixed inset-0 z-50 bg-admin-neutral-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-admin-lg shadow-admin-modal border border-admin-neutral-200 p-6 w-full max-w-md">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-admin bg-admin-success-light mb-4 text-admin-success">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-admin-neutral-900 mb-2">Account Created Successfully!</h3>
                <p class="text-sm text-admin-neutral-600 mb-6">Please check your email for verification. You must verify your email address before you can log in.</p>
            </div>
            <div class="flex justify-center">
                <button id="proceedToVerification" class="px-6 py-2 bg-admin-primary text-white font-medium rounded-admin hover:bg-admin-primary-hover transition duration-200 focus:outline-none focus:ring-2 focus:ring-admin-primary/20">Proceed to Email Verification</button>
            </div>
        </div>
    </div>

    <div id="errorModal" class="hidden fixed inset-0 z-50 bg-admin-neutral-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-admin-lg shadow-admin-modal border border-admin-neutral-200 p-6 w-full max-w-md">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-admin bg-admin-danger-light mb-4 text-admin-danger">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-admin-neutral-900 mb-2" id="errorModalTitle">Error</h3>
                <div id="errorModalContent" class="text-sm text-admin-neutral-600 mb-6"></div>
            </div>
            <div class="flex justify-center">
                <button onclick="document.getElementById('errorModal').classList.add('hidden')" class="px-6 py-2 bg-admin-danger text-white font-medium rounded-admin hover:bg-admin-danger-hover transition duration-200 focus:outline-none focus:ring-2 focus:ring-admin-danger/20">Dismiss</button>
            </div>
        </div>
    </div>

    {{-- Validation Logic Script --}}
    <script>
        function checkPasswordStrength(password) {
            const requirements = {
                len: password.length >= 8,
                up: /[A-Z]/.test(password),
                num: /[0-9]/.test(password),
                spec: /[^A-Za-z0-9]/.test(password)
            };

            // Update Pills
            updatePill('req-len', requirements.len);
            updatePill('req-up', requirements.up);
            updatePill('req-num', requirements.num);
            updatePill('req-spec', requirements.spec);

            // Update Strength Bars
            let score = Object.values(requirements).filter(Boolean).length;
            const colors = ['#e5e7eb', '#ef4444', '#f59e0b', '#10b981', '#059669']; // Gray, Red, Orange, Emerald, Green
            
            for(let i = 1; i <= 4; i++) {
                document.getElementById('bar-' + i).style.backgroundColor = (i <= score) ? colors[score] : '#e5e7eb';
            }
        }

        function updatePill(id, isValid) {
            const el = document.getElementById(id);
            if (isValid) {
                el.classList.remove('text-gray-400', 'bg-white', 'border-gray-300');
                el.classList.add('text-green-700', 'bg-green-100', 'border-green-300');
            } else {
                el.classList.add('text-gray-400', 'bg-white', 'border-gray-300');
                el.classList.remove('text-green-700', 'bg-green-100', 'border-green-300');
            }
        }

        // Form Submit Logic
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const registerBtn = document.getElementById('registerBtn');
            if (!registerBtn) return;
            const originalText = registerBtn.innerHTML;

            registerBtn.disabled = true;
            registerBtn.innerHTML = registerBtn.dataset.loadingText || 'Creating Account...';

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    window.location.reload(); 
                    return; 
                }
                return response.json();
            })
            .then(data => {
                if (!data) return;
                if (data.success) {
                    document.getElementById('verificationModal').classList.remove('hidden');
                } else {
                    if (data.errors) {
                        let errorHtml = '<ul class="text-left space-y-2">';
                        for (let field in data.errors) {
                            errorHtml += `<li class="flex items-start"><span class="text-admin-danger mr-2">&bull;</span><span>${data.errors[field][0]}</span></li>`;
                        }
                        errorHtml += '</ul>';
                        document.getElementById('errorModalTitle').textContent = 'Registration Error';
                        document.getElementById('errorModalContent').innerHTML = errorHtml;
                    } else {
                        document.getElementById('errorModalTitle').textContent = 'Registration Failed';
                        document.getElementById('errorModalContent').innerHTML = `<p>${data.message || 'Registration failed. Please try again.'}</p>`;
                    }
                    document.getElementById('errorModal').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('errorModalTitle').textContent = 'Error';
                document.getElementById('errorModalContent').innerHTML = '<p>An error occurred. Please try again.</p>';
                document.getElementById('errorModal').classList.remove('hidden');
            })
            .finally(() => {
                registerBtn.disabled = false;
                registerBtn.innerHTML = originalText;
            });
        });

        document.getElementById('proceedToVerification').addEventListener('click', function() {
            window.location.href = '{{ route("verification.notice") }}';
        });
    </script>
</x-guest-layout>