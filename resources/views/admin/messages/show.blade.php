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

    <x-success-modal name="message-show-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
        <p class="text-sm text-admin-neutral-600">{{ session('message_success') }}</p>
    </x-success-modal>
    <x-admin.ui.modal name="message-show-error" title="Error" variant="error" maxWidth="sm">
        <p class="text-sm text-admin-neutral-700">{{ session('message_error') }}</p>
        <x-slot name="footer">
            <x-admin.ui.button.secondary type="button" @click="$dispatch('close-admin-modal', 'message-show-error')">
                Close
            </x-admin.ui.button.secondary>
        </x-slot>
    </x-admin.ui.modal>
    
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
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'message-reply' }))"
                        class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all text-sm font-medium flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7l-5 5 5 5M2 12h11a6 6 0 016 6v1"></path>
                    </svg>
                    Reply
                </button>
                
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

<x-admin.ui.modal name="message-reply" title="Reply to {{ $message->name }}" variant="info" maxWidth="lg">
    <form id="messageReplyForm" method="POST" action="{{ route('admin.messages.reply', $message->id) }}" class="space-y-4">
        @csrf

        <div class="rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 p-3">
            <p class="text-xs uppercase tracking-wide text-admin-neutral-500">Recipient</p>
            <p class="text-sm font-semibold text-admin-neutral-900">{{ $message->name }}</p>
            <p class="text-xs text-admin-neutral-600">{{ $message->email }}</p>
        </div>

        <div>
            <x-input-label for="reply_message" :value="__('Reply Message')" class="text-admin-neutral-700 font-medium mb-2" />
            <textarea id="reply_message" name="reply_message" rows="6"
                      class="block w-full rounded-admin border border-admin-neutral-300 bg-admin-neutral-50 px-3 py-2 text-sm text-admin-neutral-900 placeholder-admin-neutral-400 transition-all duration-200 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                      placeholder="Type your reply here...">{{ old('reply_message') }}</textarea>
            @error('reply_message')
                <p class="mt-2 text-sm text-admin-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-admin border border-admin-neutral-200 bg-white p-3">
            <p class="text-xs uppercase tracking-wide text-admin-neutral-500 mb-2">Original Message</p>
            <p class="text-sm text-admin-neutral-700 whitespace-pre-line">{{ $message->message }}</p>
        </div>
    </form>
    <x-slot name="footer">
        <x-admin.ui.button.secondary type="button" onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'message-reply' }))">
            Cancel
        </x-admin.ui.button.secondary>
        <x-admin.ui.button.primary type="submit" form="messageReplyForm">
            Send Reply
        </x-admin.ui.button.primary>
    </x-slot>
</x-admin.ui.modal>

@if(session('message_success') || session('message_error'))
<script>
    const showMessageStatusModal = () => {
        const hasSuccess = @json((bool) session('message_success'));
        const hasError = @json((bool) session('message_error'));

        if (hasSuccess) {
            window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'message-show-success' }));
        }

        if (hasError) {
            window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'message-show-error' }));
        }
    };

    document.addEventListener('DOMContentLoaded', showMessageStatusModal);
    document.addEventListener('livewire:navigated', showMessageStatusModal);
</script>
@endif

@if($errors->has('reply_message'))
<script>
    const openReplyModal = () => {
        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'message-reply' }));
    };
    document.addEventListener('DOMContentLoaded', openReplyModal);
    document.addEventListener('livewire:navigated', openReplyModal);
</script>
@endif
@endsection
