@extends('layouts.sidebar')
@section('page-title', 'Feedback Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customer Feedback</h1>
            <p class="mt-2 text-sm text-gray-700">
                Review and manage feedback submitted by customers. Toggle "Visibility" to show selected feedback on the homepage.
            </p>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Feedback Table --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Homepage Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($feedbacks as $feedback)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $feedback->created_at->format('M d, Y') }}<br>
                            <span class="text-xs text-gray-400">{{ $feedback->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $feedback->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $feedback->message }}">
                                {{ Str::limit($feedback->message, 80) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($feedback->is_visible)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 mr-1.5 bg-green-400 rounded-full"></span>
                                    Visible
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <span class="w-2 h-2 mr-1.5 bg-gray-400 rounded-full"></span>
                                    Hidden
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-4" x-data="{ showViewModal: false }">
                                
                                {{-- View Full Feedback Button --}}
                                <button @click="showViewModal = true" class="text-gray-400 hover:text-blue-600 transition-colors p-1" title="View Full Feedback">
                                    <i class="fas fa-envelope-open-text text-lg"></i>
                                </button>

                                {{-- Toggle Visibility Form --}}
                                <form action="{{ route('admin.feedbacks.toggle', $feedback->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="text-gray-400 hover:text-clsu-green transition-colors p-1"
                                            title="{{ $feedback->is_visible ? 'Hide from Homepage' : 'Show on Homepage' }}">
                                        @if($feedback->is_visible)
                                            <i class="fas fa-eye text-lg"></i>
                                        @else
                                            <i class="fas fa-eye-slash text-lg"></i>
                                        @endif
                                    </button>
                                </form>

                                {{-- View Full Feedback Modal --}}
                                <div x-show="showViewModal" 
                                     x-cloak 
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                                     @click.self="showViewModal = false"
                                     x-transition.opacity>
                                    
                                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 text-left flex flex-col max-h-[90vh] overflow-hidden"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 translate-y-4"
                                         x-transition:enter-end="opacity-100 translate-y-0">
                                        
                                        {{-- Modal Header --}}
                                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                            <h3 class="text-lg font-bold text-gray-900">Feedback Details</h3>
                                            <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600 transition">
                                                <i class="fas fa-times text-xl"></i>
                                            </button>
                                        </div>
                                        
                                        {{-- Modal Body --}}
                                        <div class="px-6 py-6 overflow-y-auto">
                                            <div class="grid grid-cols-2 gap-4 mb-6">
                                                <div>
                                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Customer Name</p>
                                                    <p class="text-base font-medium text-gray-900">{{ $feedback->name }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Date Submitted</p>
                                                    <p class="text-sm text-gray-900">{{ $feedback->created_at->format('F j, Y - g:i A') }}</p>
                                                </div>
                                            </div>

                                            <div class="mb-6">
                                                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Rating</p>
                                                <div class="flex items-center text-xl">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= ($feedback->rating ?? 5))
                                                            <span class="text-yellow-400">★</span>
                                                        @else
                                                            <span class="text-gray-300">★</span>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-2">Customer Review</p>
                                                <div class="p-4 bg-gray-50 border border-gray-100 rounded-lg text-gray-700 whitespace-pre-wrap leading-relaxed">"{{ $feedback->message }}"</div>
                                            </div>
                                        </div>

                                        {{-- Modal Footer --}}
                                        <div class="px-6 py-4 border-t border-gray-100 flex justify-end bg-gray-50 gap-3">
                                            <button @click="showViewModal = false" class="px-5 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition shadow-sm font-medium">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">No feedback received yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($feedbacks->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $feedbacks->links() }}
        </div>
        @endif
    </div>
</div>
@endsection