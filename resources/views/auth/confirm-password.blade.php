<!-- confirm-password.blade.php -->
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-clsu-green to-ret-dark px-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-500 hover:scale-105">
            <div class="p-8">
                <div class="text-center mb-2">
                    <div class="w-16 h-16 bg-gradient-to-r from-clsu-green to-ret-green-light rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-ret-dark font-fugaz">Confirm Password</h2>
                </div>
                
                <div class="mb-6 text-center text-sm text-gray-600 bg-green-50 p-3 rounded-lg border-l-4 border-ret-green-light animate-pulse">
                    {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                </div>

                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                    @csrf

                    <!-- Password -->
                    <div class="group">
                        <x-input-label for="password" :value="__('Password')" class="text-ret-dark font-poppins font-medium mb-2" />
                        <div class="relative transform transition-all duration-300 group-hover:scale-[1.02]">
                            <x-text-input id="password" class="block mt-1 w-full pl-10 pr-10 h-12 transition-all duration-300 focus:ring-2 focus:ring-ret-green-light focus:border-transparent border-gray-300"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 transition-colors duration-300 group-hover:text-ret-green-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex justify-end mt-6 transform transition-transform duration-300 hover:scale-105">
                        <x-primary-button class="w-full bg-gradient-to-r from-clsu-green to-ret-green-light hover:from-ret-green-light hover:to-clsu-green text-white font-poppins font-semibold py-3 shadow-lg">
                            {{ __('Confirm') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>