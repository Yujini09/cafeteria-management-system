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
                            <x-input-label for="password" :value="__('Password')" class="text-green-700 font-medium" />
                            <div class="relative">
                                <x-text-input id="password" name="password" type="password"
                                    class="block mt-1 w-full pl-10 pr-10 h-12 border-green-400 focus:border-orange-500 focus:ring-orange-500 rounded-lg placeholder-green-500 text-green-900" required />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <button type="button" id="togglePassword1" class="absolute right-3 top-1/2 -translate-y-1/2 text-green-600 hover:text-orange-500">
                                    <svg id="eyeIcon1" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-green-700 font-medium" />
                            <div class="relative">
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                    class="block mt-1 w-full pl-10 pr-10 h-12 border-green-400 focus:border-orange-500 focus:ring-orange-500 rounded-lg placeholder-green-500 text-green-900" required />
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <button type="button" id="togglePassword2" class="absolute right-3 top-1/2 -translate-y-1/2 text-green-600 hover:text-orange-500">
                                    <svg id="eyeIcon2" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-primary-button class="w-full justify-center bg-orange-500 hover:bg-orange-600 focus:ring-orange-500 h-12 text-lg font-semibold rounded-lg shadow-md transition duration-200 text-white" id="registerBtn">
                                {{ __('Register') }}
                            </x-primary-button>
                        </div>
                    </form>
                    
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

    <div id="verificationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Account Created Successfully!</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Please check your email for verification. You must verify your email address before you can log in.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="proceedToVerification" class="px-4 py-2 bg-orange-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-300">
                        Proceed to Email Verification
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle for password
        document.getElementById('togglePassword1').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon1');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        });

        // Toggle for confirm password
        document.getElementById('togglePassword2').addEventListener('click', function () {
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const eyeIcon = document.getElementById('eyeIcon2');
            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>';
            } else {
                confirmPasswordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        });

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
                        let errorMessage = 'Please fix the following errors:\n';
                        for (let field in data.errors) {
                            errorMessage += `- ${data.errors[field][0]}\n`;
                        }
                        alert(errorMessage);
                    } else {
                        alert(data.message || 'Registration failed. Please try again.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
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