<x-guest-layout>
    {{-- Main Container: Dark Green (bg-green-950) --}}
    <div class="min-h-screen flex items-center justify-center bg-green-950 relative overflow-hidden"> 
        
        {{-- External Background Bubble Designs: Orange (Shifted) --}}
        <div class="absolute inset-0 opacity-30">
            <div class="w-96 h-96 bg-orange-700 rounded-full absolute -top-20 left-1/4 mix-blend-screen opacity-50 transform -translate-x-1/2"></div>
            <div class="w-64 h-64 bg-orange-700 rounded-full absolute bottom-0 right-0 mix-blend-screen opacity-50 transform translate-x-1/4 translate-y-1/4"></div>
            <div class="w-80 h-80 bg-orange-700 rounded-full absolute top-1/4 left-0 mix-blend-screen opacity-50 transform -translate-x-1/2"></div>
            <div class="w-40 h-40 bg-orange-700 rounded-full absolute -bottom-10 left-1/2 mix-blend-screen opacity-50 transform translate-x-1/4"></div>
        </div>

        {{-- Card Container --}}
        <div class="bg-white rounded-xl shadow-2xl flex overflow-hidden w-full max-w-5xl z-10"> 
            
            {{-- Left side (Logo Display) - White background with subtle left shift --}}
            <div class="hidden md:flex w-1/2 items-center justify-center bg-white p-8 relative">
                <img src="{{ asset('images/caf-logo.png') }}" alt="RET Cafeteria Logo"
                     class="max-h-64 object-contain w-auto -ml-8"> 

                <a href="{{ route('marketing.home') }}"
                    class="absolute bottom-8 left-8 right-8 inline-flex items-center justify-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800 transition hover:border-orange-400 hover:text-orange-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Landing Page
                </a>
            </div>

            {{-- Right side (Form) - Light Green (bg-green-100), SCROLL ENABLED --}}
            <div class="w-full md:w-1/2 p-8 md:p-12 relative bg-green-100 h-[500px] overflow-y-auto"> 
                
                {{-- Internal bubbles: Light Green (Restored original diagonal positions) --}}
                <div class="absolute inset-0 opacity-50 overflow-hidden">
                    <div class="w-64 h-64 bg-green-200 rounded-full absolute -top-24 -right-24"></div>
                    <div class="w-48 h-48 bg-green-200 rounded-full absolute -bottom-16 -left-16"></div>
                </div>

                <div class="relative z-10">
                    
                    <div class="text-left mb-8">
                        <h2 class="text-green-900 text-4xl font-extrabold mb-2">Create Account</h2> 
                        <p class="text-green-700 text-lg">Join our cafeteria community</p> 
                    </div>

                    <form method="POST" action="{{ route('register') }}" id="registerForm">
                        @csrf

                        <div class="mb-6">
                            <x-input-label for="name" :value="__('Name')" class="text-green-700 font-medium" />
                            <div class="relative">
                                <x-text-input id="name" name="name" type="text"
                                    class="block mt-1 w-full pl-10 h-12 border-green-400 focus:border-orange-500 focus:ring-orange-500 rounded-lg placeholder-green-500 text-green-900" 
                                    required autofocus />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="address" :value="__('Address')" class="text-green-700 font-medium" />
                            <div class="relative">
                                <x-text-input id="address" name="address" type="text"
                                    class="block mt-1 w-full pl-10 h-12 border-green-400 focus:border-orange-500 focus:ring-orange-500 rounded-lg placeholder-green-500 text-green-900" />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="email" :value="__('Email')" class="text-green-700 font-medium" />
                                <div class="relative">
                                    <x-text-input id="email" name="email" type="email"
                                        class="block mt-1 w-full pl-10 h-12 border-green-400 focus:border-orange-500 focus:ring-orange-500 rounded-lg placeholder-green-500 text-green-900" required />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <x-input-label for="contact_no" :value="__('Contact No')" class="text-green-700 font-medium" />
                                <div class="relative">
                                    <x-text-input id="contact_no" name="contact_no" type="text"
                                        class="block mt-1 w-full pl-10 h-12 border-green-400 focus:border-orange-500 focus:ring-orange-500 rounded-lg placeholder-green-500 text-green-900" />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="department" :value="__('Department/Office')" class="text-green-700 font-medium" />
                            <div class="relative">
                                <x-text-input id="department" name="department" type="text"
                                    class="block mt-1 w-full pl-10 h-12 border-green-400 focus:border-orange-500 focus:ring-orange-500 rounded-lg placeholder-green-500 text-green-900" />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="mb-6">
                            {!! app('livewire')->mount('password-with-rules', [
                                'name' => 'password',
                                'label' => __('Password'),
                                'showRequirements' => true,
                                'required' => true,
                                'variant' => 'auth',
                            ]) !!}
                        </div>

                        <div class="mb-6">
                            {!! app('livewire')->mount('password-with-rules', [
                                'name' => 'password_confirmation',
                                'label' => __('Confirm Password'),
                                'showRequirements' => false,
                                'required' => true,
                                'variant' => 'auth',
                            ]) !!}
                        </div>

                        <div class="mb-6">
                            <x-primary-button class="w-full justify-center bg-orange-500 hover:bg-orange-600 focus:ring-orange-500 h-12 text-lg font-semibold rounded-lg shadow-md transition duration-200 text-white" id="registerBtn">
                                {{ __('Register') }}
                            </x-primary-button>
                        </div>
                    </form>
                    

                {{-- Google OAuth Button --}}
                        <x-google-oauth-button />

                    {{-- CORRECTED LOCATION: Already have account link moved outside of the form --}}
                    <div class="flex justify-center text-sm mt-4"> 
                        <a href="{{ route('login') }}" class="text-orange-600 hover:text-orange-700 hover:underline transition duration-200">
                            {{ __('Have an account already?') }}
                        </a>
                    </div>
                </div> {{-- End of z-10 wrapper --}}
            </div>
        </div>
    </div>

    {{-- Modal: Verification Success --}}
    <div id="verificationModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 mb-4">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Account Created Successfully!</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Please check your email for verification. You must verify your email address before you can log in.
                </p>
            </div>

            <div class="flex justify-center">
                <button id="proceedToVerification" class="px-6 py-2 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition duration-200 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Proceed to Email Verification
                </button>
            </div>
        </div>
    </div>

    {{-- Modal: Error Alert --}}
    <div id="errorModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2" id="errorModalTitle">Error</h3>
                <div id="errorModalContent" class="text-sm text-gray-500 mb-6">
                    <!-- Error messages will be inserted here -->
                </div>
            </div>

            <div class="flex justify-center">
                <button onclick="document.getElementById('errorModal').classList.add('hidden')" class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-300">
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
                            errorHtml += `<li class="flex items-start"><span class="text-red-500 mr-2">•</span><span>${data.errors[field][0]}</span></li>`;
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
