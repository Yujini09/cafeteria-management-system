<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Smart Cafeteria'))</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'clsu-green': '#00462E',
                        'ret-green-light': '#057C3C',
                        'cafeteria-orange': '#FB3E05',
                        'ret-dark': '#1F2937',
                        'menu-orange': '#EA580C',
                        'menu-dark': '#131820',
                    },
                    fontFamily: {
                        fugaz: ['"Fugaz One"', 'sans-serif'],
                        damion: ['"Damion"', 'cursive'],
                        poppins: ['"Poppins"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Fugaz+One&family=Damion&display=swap" rel="stylesheet" />

    <style>
        @yield('styles')
    </style>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="font-poppins antialiased bg-gray-200">
    <div class="min-h-screen">
        
        @include('partials.header')

        @auth
            @if(Auth::user()->role === 'customer')
                <div x-data="paymentPrompt({
                        oldReference: @json(old('reference_number')),
                        oldDepartment: @json(old('department_office')),
                        oldPayer: @json(old('payer_name')),
                        oldAccountCode: @json(old('account_code'))
                    })"
                    x-init="init()"
                    x-cloak>
                    <div x-show="open"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        @keydown.escape.window="dismiss()">
                        <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-white rounded-xl shadow-2xl border border-gray-100 p-5 sm:p-6">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Payment Required</h2>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-1">
                                        Your reservation has finished. Please submit your payment details now.
                                    </p>
                                </div>
                                <button type="button"
                                        class="text-gray-400 hover:text-gray-600 transition"
                                        @click="dismiss()"
                                        aria-label="Close">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm">
                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                                    <div class="text-xs uppercase tracking-wide text-gray-400">Reservation</div>
                                    <div class="text-gray-900 font-semibold" x-text="reservationLabel"></div>
                                    <div class="text-gray-600 mt-1" x-text="reservationPeriod"></div>
                                </div>
                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                                    <div class="text-xs uppercase tracking-wide text-gray-400">Venue</div>
                                    <div class="text-gray-900 font-semibold" x-text="reservationVenue"></div>
                                    <div class="text-gray-600 mt-1" x-text="reservationContact"></div>
                                </div>
                            </div>

                            <div class="mt-3 p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                                <div class="text-xs uppercase tracking-wide text-emerald-700">Amount Due</div>
                                <div class="text-xl sm:text-2xl font-bold text-clsu-green" x-text="formattedAmount"></div>
                            </div>

                            <form method="POST"
                                :action="actionUrl"
                                x-ref="paymentForm"
                                @submit.prevent="submitPayment"
                                enctype="multipart/form-data"
                                class="mt-4 space-y-3">
                                @csrf

                                <div>
                                    <label for="payment_reference_number" class="block text-xs font-semibold text-gray-700">Reference Number</label>
                                    <input id="payment_reference_number" name="reference_number" type="text" required
                                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-600 focus:ring-green-600"
                                        placeholder="Enter payment reference number"
                                        x-model="form.reference_number">
                                    @error('reference_number')
                                        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label for="payment_department_office" class="block text-xs font-semibold text-gray-700">Department/Office</label>
                                        <input id="payment_department_office" name="department_office" type="text"
                                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-600 focus:ring-green-600"
                                            placeholder="Department or office"
                                            x-model="form.department_office">
                                        @error('department_office')
                                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="payment_account_code" class="block text-xs font-semibold text-gray-700">Account Code</label>
                                        <input id="payment_account_code" name="account_code" type="text"
                                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-600 focus:ring-green-600"
                                            placeholder="Enter account code"
                                            x-model="form.account_code">
                                        @error('account_code')
                                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="payment_payer_name" class="block text-xs font-semibold text-gray-700">Name</label>
                                    <input id="payment_payer_name" name="payer_name" type="text" required
                                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-600 focus:ring-green-600"
                                        placeholder="Name of payer"
                                        x-model="form.payer_name">
                                    @error('payer_name')
                                        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="payment_receipt" class="block text-xs font-semibold text-gray-700">Payment Receipt (optional)</label>
                                    <input id="payment_receipt" name="receipt" type="file" accept=".pdf,image/png,image/jpeg"
                                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-600 focus:ring-green-600 bg-white">
                                    <p class="text-[11px] text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG. Max size 5MB.</p>
                                    @error('receipt')
                                        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pt-2">
                                    <a :href="actionUrl" class="text-xs text-gray-500 hover:text-gray-700">Open full payment page</a>
                                    <button type="submit"
                                            class="inline-flex items-center justify-center px-5 py-2.5 bg-clsu-green text-white rounded-lg hover:bg-green-700 transition font-semibold text-sm">
                                        Submit for Review
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endauth

        <main>
            @yield('content') {{-- THIS IS WHERE PAGE CONTENT GOES --}}
        </main>

        @include('partials.footer')

    </div>
    
    @yield('scripts')
    
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if(target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
