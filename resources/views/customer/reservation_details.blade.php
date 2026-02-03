@extends('layouts.app')

@section('title', 'Reservation Details - CLSU RET Cafeteria')

@section('styles')
    .reservation-hero-bg {
    background-image: url('/images/banner1.jpg');
    background-size: cover;
    background-position: top;
    }
    /* Custom styles for the reservation details page */
    .details-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px;
        background-color: #f7f7f7;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .reservation-table-card {
        background-color: white;
        border-radius: 6px;
        overflow-x: auto;
        border: 1px solid #e0e0e0;
    }
    .status-label {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 9999px; /* Fully rounded */
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .status-approved {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .status-declined {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
    .action-link {
        color: #007bff;
        text-decoration: none;
        transition: color 0.2s;
    }
    .action-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }
@endsection

@section('content')

<!-- Reservation Details Banner Header -->
<section class="reservation-hero-bg py-20 lg:py-20 bg-gray-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl lg:text-5xl font-extrabold mb-3 tracking-wide">
            Reservation Details
        </h1>
        <p class="text-lg lg:text-xl font-poppins opacity-90">
            Track the status of your catering requests.
        </p>
    </div>
</section>

<!-- Reservation Details Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Your Reservations</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date and Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        
                        {{-- APPROVED EXAMPLE --}}
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-medium">May 30</div>
                                <div class="text-gray-500">10:00-12:00pm</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">A.M. Snacks</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Standard Menu</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Menu 1</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10 pax</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₱6,500</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-label status-approved">Approved</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('billing_info') }}" class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-150 text-xs font-semibold">
                                    See Details
                                </a>
                            </td>
                        </tr>
                        
                        {{-- DECLINED EXAMPLE --}}
                        <tr id="declined-row" class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-medium">June 10</div>
                                <div class="text-gray-500">12:00-1:00pm</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Lunch</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Special Menu</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Menu 3</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10 pax</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₱6,600</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-label status-declined">Declined</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="showDeclineDetails('{{ addslashes('Menu 3 is currently unavailable for lunch on June 10 due to a shortage of key ingredients. Please select another menu.') }}')" 
                                        class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-150 text-xs font-semibold">
                                    See Details
                                </button>
                            </td>
                        </tr>
                        
                        {{-- PENDING EXAMPLE --}}
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-medium">June 23, June 24</div>
                                <div class="text-gray-500">7:00-10:00am</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Breakfast</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Standard Menu</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Menu 1</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10 pax</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₱6,600</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-label status-pending">Pending</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-150 text-xs font-semibold">
                                    See Details
                                </button>
                            </td>
                        </tr>

                        {{-- CANCELLED EXAMPLE (Optional) --}}
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-medium">July 1</div>
                                <div class="text-gray-500">6:00-7:00pm</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Dinner</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Special Menu</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Menu 4</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">5 pax</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₱3,250</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-label bg-gray-300 text-gray-700 border-gray-400">Cancelled</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-150 text-xs font-semibold">
                                    See Details
                                </button>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
        
        </div>
    </div>
</section>

{{-- MODAL STRUCTURE for Decline Reason (Purely Frontend/JS for demonstration) --}}
<div id="declineModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg w-full">
        <div class="bg-red-500 px-6 py-4 text-white">
            <h3 class="text-xl font-bold">Reservation Declined</h3>
        </div>
        <div class="px-6 py-5">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-700 font-medium">Reason for decline:</p>
                    <p id="decline-reason" class="mt-2 text-gray-900"></p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 flex justify-end">
            <button type="button" onclick="closeDeclineDetails()" 
                    class="px-4 py-2 bg-clsu-green text-white rounded-lg font-semibold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-clsu-green transition duration-150">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function showDeclineDetails(reason) {
        document.getElementById('decline-reason').textContent = reason;
        document.getElementById('declineModal').classList.remove('hidden');
        document.getElementById('declineModal').classList.add('flex');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeDeclineDetails() {
        document.getElementById('declineModal').classList.add('hidden');
        document.getElementById('declineModal').classList.remove('flex');
        document.body.style.overflow = 'auto'; // Restore scrolling
    }
    
    // Close modal when clicking outside the modal content
    document.getElementById('declineModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeclineDetails();
        }
    });
</script>

@endsection