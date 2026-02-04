<x-guest-layout>
    {{-- Main Container --}}
    <div class="min-h-screen flex items-center justify-center bg-admin-neutral-100 relative overflow-hidden font-admin text-admin-neutral-900"> 
        <div class="absolute inset-0">
            <div class="absolute -top-24 -right-20 h-80 w-80 rounded-full bg-admin-primary/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-ret-dark/10 blur-3xl"></div>
        </div>

        {{-- Card Container --}}
        <div class="relative w-full max-w-5xl overflow-hidden rounded-admin-lg border border-admin-neutral-200 bg-white shadow-admin z-10 mx-4 flex flex-col md:flex-row md:h-[600px]"> 
            
            {{-- Left side (Logo Display) --}}
            <div class="hidden md:flex md:w-5/12 items-center justify-center bg-ret-dark p-10 relative">
                <div class="absolute inset-0 bg-gradient-to-br from-ret-dark via-[#1f2937] to-black/70"></div>
                <div class="relative z-10 flex flex-col items-center gap-8 text-center text-white">
                    <img src="{{ asset('images/caf-logo.png') }}" alt="RET Cafeteria Logo"
                         class="max-h-56 object-contain w-auto"> 
                    <div class="space-y-1">
                        <h3 class="text-xl font-semibold">Create your account</h3>
                        <p class="text-sm text-white/70">Join the cafeteria community today.</p>
                    </div>
                    <a href="{{ route('marketing.home') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-admin border border-white/20 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                         Continue to site
                    </a>
                </div>
            </div>

            {{-- Right side (Form) --}}
            <div class="w-full md:w-7/12 p-8 pb-16 md:px-12 md:pt-12 md:pb-20 relative bg-white overflow-y-auto overflow-x-hidden scroll-pb-10 md:h-full"> 
                <div class="absolute inset-0 pointer-events-none">
                    <div class="w-56 h-56 bg-admin-primary/5 rounded-full absolute -top-20 -right-16"></div>
                    <div class="w-40 h-40 bg-ret-dark/5 rounded-full absolute -bottom-16 -left-16"></div>
                </div>

                <div class="relative z-10 flex h-full flex-col">
                    <div class="flex flex-col gap-6 mb-8">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="md:hidden h-12 w-12 rounded-admin bg-ret-dark/90 flex items-center justify-center">
                                    <img src="{{ asset('images/caf-logo.png') }}" alt="RET Cafeteria Logo"
                                         class="h-8 w-8 object-contain"> 
                                </div>
                                <div>
                                    <h2 class="text-admin-neutral-900 text-3xl font-semibold mt-1">Create Account</h2> 
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
                        <p class="text-admin-neutral-600 text-base">Join our cafeteria community</p> 
                    </div>

                    <form method="POST" action="{{ route('register') }}" id="registerForm" class="flex flex-col gap-6">
                        @csrf

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="name" :value="__('Name')" class="text-admin-neutral-700 font-medium" />
                                <div class="relative">
                                    <x-text-input id="name" name="name" type="text"
                                        class="block mt-1 w-full pl-10 h-12 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 focus:border-admin-primary focus:ring-admin-primary/20" 
                                        required autofocus />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="contact_no" :value="__('Contact No')" class="text-admin-neutral-700 font-medium" />
                                <div class="relative">
                                    <x-text-input id="contact_no" name="contact_no" type="text"
                                        class="block mt-1 w-full pl-10 h-12 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 focus:border-admin-primary focus:ring-admin-primary/20" />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="address" :value="__('Address')" class="text-admin-neutral-700 font-medium" />
                                <div class="relative">
                                    <x-text-input id="address" name="address" type="text"
                                        class="block mt-1 w-full pl-10 h-12 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 focus:border-admin-primary focus:ring-admin-primary/20" />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" class="text-admin-neutral-700 font-medium" />
                                <div class="relative">
                                    <x-text-input id="email" name="email" type="email"
                                        class="block mt-1 w-full pl-10 h-12 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 focus:border-admin-primary focus:ring-admin-primary/20" required />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="department" :value="__('Department/Office')" class="text-admin-neutral-700 font-medium" />
                                <div class="relative">
                                    <x-text-input id="department" name="department" type="text"
                                        class="block mt-1 w-full pl-10 h-12 !rounded-admin !shadow-none border border-admin-neutral-300 bg-admin-neutral-50 text-admin-neutral-900 placeholder-admin-neutral-400 focus:border-admin-primary focus:ring-admin-primary/20" />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-admin-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
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

                        <div>
                            <x-primary-button class="w-full justify-center !rounded-admin bg-admin-primary hover:bg-admin-primary-hover focus:ring-admin-primary h-12 text-base font-semibold shadow-admin transition duration-200 text-white" id="registerBtn">
                                {{ __('Register') }}
                            </x-primary-button>
                        </div>
                    </form>
                    
                    <div class="mt-6">
                        <div class="flex items-center gap-3 text-xs uppercase tracking-[0.2em] text-admin-neutral-400">
                            <span class="h-px flex-1 bg-admin-neutral-200"></span>
                            <span>or</span>
                            <span class="h-px flex-1 bg-admin-neutral-200"></span>
                        </div>
                        <div class="mt-3">
                            {{-- Google OAuth Button --}}
                            <x-google-oauth-button />
                        </div>

                        {{-- CORRECTED LOCATION: Already have account link moved outside of the form --}}
                        <div class="flex justify-center text-sm mt-4"> 
                            <a href="{{ route('login') }}" class="text-admin-primary hover:text-admin-primary-hover hover:underline transition duration-200">
                                {{ __('Have an account already?') }}
                            </a>
                        </div>
                    </div>
                </div> {{-- End of z-10 wrapper --}}
            </div>
        </div>
    </div>

    {{-- Modal: Verification Success --}}
    <div id="verificationModal" class="hidden fixed inset-0 z-50 bg-admin-neutral-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-admin-lg shadow-admin-modal border border-admin-neutral-200 p-6 w-full max-w-md">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-admin bg-admin-success-light mb-4 text-admin-success">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-admin-neutral-900 mb-2">Account Created Successfully!</h3>
                <p class="text-sm text-admin-neutral-600 mb-6">
                    Please check your email for verification. You must verify your email address before you can log in.
                </p>
            </div>

            <div class="flex justify-center">
                <button id="proceedToVerification" class="px-6 py-2 bg-admin-primary text-white font-medium rounded-admin hover:bg-admin-primary-hover transition duration-200 focus:outline-none focus:ring-2 focus:ring-admin-primary/20">
                    Proceed to Email Verification
                </button>
            </div>
        </div>
    </div>

    {{-- Modal: Error Alert --}}
    <div id="errorModal" class="hidden fixed inset-0 z-50 bg-admin-neutral-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-admin-lg shadow-admin-modal border border-admin-neutral-200 p-6 w-full max-w-md">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-admin bg-admin-danger-light mb-4 text-admin-danger">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-admin-neutral-900 mb-2" id="errorModalTitle">Error</h3>
                <div id="errorModalContent" class="text-sm text-admin-neutral-600 mb-6">
                    <!-- Error messages will be inserted here -->
                </div>
            </div>

            <div class="flex justify-center">
                <button onclick="document.getElementById('errorModal').classList.add('hidden')" class="px-6 py-2 bg-admin-danger text-white font-medium rounded-admin hover:bg-admin-danger-hover transition duration-200 focus:outline-none focus:ring-2 focus:ring-admin-danger/20">
                    Dismiss
                </button>
            </div>
        </div>
    </div>

    <script>
        // Handle form submission with modal
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);
            const registerBtn = document.getElementById('registerBtn');
            const originalText = registerBtn.innerHTML;

            // Disable button and show loading
            registerBtn.disabled = true;
            registerBtn.innerHTML = 'Creating Account...';

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // Check if response is not JSON (e.g., HTML from a standard redirect/error)
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    // If Laravel redirects/throws a non-JSON error, reload the page normally
                    window.location.reload(); 
                    return; // Exit
                }
                return response.json();
            })
            .then(data => {
                if (!data) return;
                
                if (data.success) {
                    // Success! Show verification modal
                    document.getElementById('verificationModal').classList.remove('hidden');
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        let errorHtml = '<ul class="text-left space-y-2">';
                        for (let field in data.errors) {
                            errorHtml += `<li class="flex items-start"><span class="text-admin-danger mr-2">•</span><span>${data.errors[field][0]}</span></li>`;
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
                // Re-enable button
                registerBtn.disabled = false;
                registerBtn.innerHTML = originalText;
            });
        });

        // Handle modal proceed button
        document.getElementById('proceedToVerification').addEventListener('click', function() {
            window.location.href = '{{ route("verification.notice") }}';
        });
    </script>
</x-guest-layout>
