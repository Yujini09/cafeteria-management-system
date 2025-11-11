@extends('layouts.app')

@section('title', 'Reservation Details - CLSU RET Cafeteria')

@section('styles')
<style>
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
</style>
@endsection

@section('content')

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold text-ret-dark mb-2">Reservation Details</h1>
        <p class="text-xl text-gray-600">Track the status of your catering requests.</p>
    </div>
</section>

<div class="py-12 bg-gray-50">
    <div class="details-container">
        <div class="reservation-table-card">
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
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">May 30<br>10:00-12:00pm</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">A.M. Snacks</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Standard Menu</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Menu 1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10 pax</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱6,500</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="status-label status-approved">Approved</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="action-link">Proceed to Payment</a>
                        </td>
                    </tr>
                    
                    {{-- DECLINED EXAMPLE --}}
                    <tr id="declined-row">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">June 10<br>12:00-1:00pm</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Lunch</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Special Menu</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Menu 3</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10 pax</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱6,600</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="status-label status-declined">Declined</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="javascript:void(0)" onclick="showDeclineDetails('{{ addslashes('Menu 3 is currently unavailable for lunch on June 10 due to a shortage of key ingredients. Please select another menu.') }}')" class="action-link">See Details</a>
                        </td>
                    </tr>
                    
                    {{-- PENDING EXAMPLE --}}
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">June 23, June 24<br>7:00-10:00am</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Breakfast</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Standard Menu</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Menu 1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10 pax</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱6,600</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="status-label status-pending">Pending</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="action-link">See Details</a>
                        </td>
                    </tr>

                    {{-- CANCELLED EXAMPLE (Optional) --}}
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">July 1<br>6:00-7:00pm</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Dinner</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Special Menu</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Menu 4</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">5 pax</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱3,250</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="status-label bg-gray-300 text-gray-700 border-gray-400">Cancelled</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="action-link">See Details</a>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL STRUCTURE for Decline Reason (Purely Frontend/JS for demonstration) --}}
<div id="declineModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
        <div class="bg-red-500 px-4 py-3 text-white">
            <h3 class="text-lg leading-6 font-medium">Reservation Declined</h3>
        </div>
        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <p class="text-sm text-gray-700">Reason for decline:</p>
                    <p id="decline-reason" class="mt-2 text-md font-semibold text-gray-900"></p>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" onclick="closeDeclineDetails()" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-clsu-green text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-clsu-green sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
    }

    function closeDeclineDetails() {
        document.getElementById('declineModal').classList.add('hidden');
        document.getElementById('declineModal').classList.remove('flex');
    }
</script>

@endsection