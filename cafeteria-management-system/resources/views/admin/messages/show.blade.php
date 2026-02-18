@extends('layouts.sidebar')
@section('page-title', 'Read Message')

@section('content')

{{-- Logic to construct the smart email link --}}
@php
    $subject = 'Re: Inquiry from ' . $message->name . ' - ' . config('app.name');
    
    // Create a polite body with context
    $body = "Dear " . $message->name . ",\n\n";
    $body .= "Thank you for getting in touch. Regarding your message:\n";
    $body .= "\"> " . \Illuminate\Support\Str::limit($message->message, 100) . "...\"\n\n";
    $body .= "Response:\n";

    // Build the full URL
    $mailtoLink = "mailto:" . $message->email . "?subject=" . rawurlencode($subject) . "&body=" . rawurlencode($body);
@endphp

<div class="max-w-4xl mx-auto space-y-6">
    
    <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-green-600 transition-colors">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Inbox
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="bg-gray-50/50 p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center text-green-700 font-bold text-lg">
                    {{ substr($message->name, 0, 1) }}
                </div>
                
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $message->name }}</h1>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <a href="{{ $mailtoLink }}" class="hover:text-green-600 hover:underline transition-colors">
                            {{ $message->email }}
                        </a>
                        <span>&bull;</span>
                        <span>{{ $message->created_at->format('M j, Y \a\t g:i A') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ $mailtoLink }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all text-sm font-medium flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Reply via Email
                </a>
                
                <form action="{{ route('admin.messages.delete', $message->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this message?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 border border-transparent rounded-lg hover:bg-red-100 transition-all text-sm font-medium flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="p-8">
            <div class="prose max-w-none text-gray-800 leading-relaxed whitespace-pre-wrap font-medium">
{{ $message->message }}
            </div>
        </div>
    </div>

</div>
@endsection