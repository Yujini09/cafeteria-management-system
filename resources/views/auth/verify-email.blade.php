<!-- verify-email.blade.php -->
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-50 to-amber-100 px-4">
        <div class="bg-white rounded-2xl shadow-2xl flex overflow-hidden w-full max-w-4xl transform transition-all duration-700 hover:shadow-2xl">
            <!-- Left Logo -->
            <div class="hidden md:flex w-1/2 items-center justify-center bg-gradient-to-br from-orange-100 to-amber-200 p-8 relative overflow-hidden">
                <div class="absolute inset-0 bg-black/5"></div>
                <div class="relative z-10 text-center">
                    <img src="{{ asset('images/caf-logo.png') }}" alt="RET Cafeteria"
                         class="max-h-64 object-contain mx-auto mb-6 transform transition-transform duration-500 hover:scale-110">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Email Verification</h2>
                    <p class="text-gray-600">Almost there!</p>
                </div>
                <!-- Animated elements -->
                <div class="absolute top-0 right-0 w-20 h-20 bg-orange-200/30 rounded-full animate-ping"></div>
                <div class="absolute bottom-0 left-0 w-16 h-16 bg-amber-200/30 rounded-full animate-bounce"></div>
            </div>

            <!-- Right Form -->
            <div class="w-full md:w-1/2 p-8 md:p-12 h-[500px] overflow-y-auto bg-white">
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-gradient-to-r from-orange-400 to-amber-500 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Verify Your Email</h2>
                    <p class="text-gray-600">One last step to complete your registration</p>
                </div>

                <div class="mb-6 text-sm text-gray-600 bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500 animate-pulse">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email, we will gladly send you another.') }}
                </div>

                @if (session('resent'))
                    <div class="mb-6 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg border border-green-200 animate-fade-in">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}" class="mb-6 transform transition-transform duration-300 hover:scale-105">
                    @csrf

                    <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-300">
                        {{ __('Resend Verification Email') }}
                    </button>
                </form>

                <div class="text-center space-y-4">
                    <p class="text-sm text-gray-600 mb-4">Or verify manually if you have the verification code:</p>
                    <a href="{{ route('verification.manual') }}" class="inline-block w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-300 text-center">
                        {{ __('Verify Manually') }}
                    </a>
                </div>

                <div class="mt-8 text-center">
                    <a href="{{ route('logout') }}" class="text-orange-600 hover:text-orange-800 hover:underline transition-all duration-300 transform hover:translate-x-1 inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        {{ __('Logout') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Automatically send verification email on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route("verification.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                console.log('Verification email sent automatically');
            })
            .catch(error => {
                console.error('Error sending verification email:', error);
            });
        });
    </script>
</x-guest-layout>