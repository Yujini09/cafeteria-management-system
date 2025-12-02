<x-guest-layout>
    {{-- Main Container: Dark Green Background (bg-green-950) --}}
    <div class="min-h-screen flex items-center justify-center bg-green-950 relative overflow-hidden px-4">
        
        {{-- External Background Bubble Designs: Orange --}}
        <div class="absolute inset-0 opacity-30">
            <div class="w-96 h-96 bg-orange-700 rounded-full absolute -top-40 -right-40 mix-blend-screen opacity-50"></div>
            <div class="w-64 h-64 bg-orange-700 rounded-full absolute top-1/2 -left-32 mix-blend-screen opacity-50 transform -translate-y-1/2"></div>
            <div class="w-80 h-80 bg-orange-700 rounded-full absolute -bottom-60 -left-60 mix-blend-screen opacity-50"></div>
            <div class="w-40 h-40 bg-orange-700 rounded-full absolute bottom-1/4 right-0 mix-blend-screen opacity-50"></div>
        </div>

        {{-- Card Container: Light Green Form (bg-green-100) --}}
        <div class="max-w-md w-full bg-green-100 rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-500 hover:shadow-3xl z-10">
            <div class="p-8">
                <div class="text-center mb-6">
                    
                    {{-- Icon styling changed to use Orange accent --}}
                    <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>

                    {{-- Text color changed to dark green --}}
                    <h2 class="text-3xl font-bold text-green-900">Reset Password</h2>
                </div>
                
                {{-- Information Box: Style changed to accent orange text/border on light green form --}}
                <div class="mb-6 text-center text-sm text-orange-700 bg-orange-50 p-3 rounded-lg border-l-4 border-orange-500">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </div>

                <x-auth-session-status class="mb-6 bg-orange-100 text-orange-800 p-3 rounded-lg border border-orange-300" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <div class="relative">
                        <x-input-label for="email" :value="__('Email')" class="text-green-700 font-medium mb-2" />
                        <div class="relative">
                            {{-- Input style adjusted for green/orange theme --}}
                            <x-text-input id="email" class="block mt-1 w-full pl-10 h-12 border-green-400 transition-all duration-300 focus:ring-2 focus:ring-orange-500 focus:border-transparent" type="email" name="email" :value="old('email')" required autofocus />
                            {{-- Icon color adjusted to dark green --}}
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-green-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        {{-- Button styled to use primary orange accent --}}
                        <x-primary-button class="w-full justify-center bg-orange-500 hover:bg-orange-600 focus:ring-orange-500 h-12 text-lg font-semibold rounded-lg shadow-md transition duration-300 transform hover:scale-[1.02]">
                            {{ __('Email Password Reset Link') }}
                        </x-primary-button>
                    </div>
                </form>

                {{-- Added a discrete link back to login for better UX --}}
                <div class="text-center text-sm mt-4">
                    <a href="{{ route('login') }}" class="text-green-700 hover:text-orange-600 hover:underline transition duration-200">
                        Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>