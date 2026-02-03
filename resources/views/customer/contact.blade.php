<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

@extends('layouts.app')

@section('title', 'Contact Us - CLSU RET Cafeteria')

@section('styles')
/* Custom background similar to the menu header for consistency */
.contact-hero-bg {
    /* Assuming you have a general background image like spices.webp */
    background-image: url('/images/banner.jpg');
    background-size: cover;
    background-position: bottom;
}
.ret-dark-card {
    background-color: #1F2937; /* Dark blue/gray from your site */
}
.ret-red-text {
    color: #EF4444; /* Vibrant red/orange color */
}
.ret-green-bg {
    background-color: #057C3C; /* Custom green color for the button */
}
.ret-green-light {
    color: #057C3C; /* Custom green color for text/icons */
}

/* Base input styling - Ensure w-full is applied universally */
.contact-input {
    /* CRITICAL CHANGE: Ensure all elements using this class, including the textarea, are full width */
    @apply w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ret-green-light focus:border-ret-green-light transition duration-150;
}
@endsection

@section('content')

<section class="contact-hero-bg py-20 lg:py-20 bg-gray-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl lg:text-5xl font-extrabold mb-2 tracking-wider">
            Contact Us
        </h1>
        <p class="text-lg lg:text-xl font-poppins opacity-90">
            Have a question? We're here to help!
        </p>
    </div>
</section>

<!-- 2. MAIN CONTENT (Contact Form and Info) -->
<section class="py-10 bg-gray-50 text-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            
            <!-- Left Card: Customer Support Form -->
            <div class="bg-white p-8 rounded-xl shadow-xl border border-gray-100">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3">Customer Support</h2>

                <form id="contactForm" method="POST" action="{{ route('contact.send') }}" onsubmit="handleContactFormSubmit(event)" class="space-y-4">
                    @csrf
                    
                    <!-- Container for Name and Email - Full Width -->
                    <div class="flex flex-col sm:flex-row gap-4 w-full"> 
                        <!-- Name field (Full width) -->
                        <div class="space-y-2 w-full">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" id="name" name="name" placeholder="Enter your name" class="contact-input" autocomplete="name" required>
                        </div>
                        
                        <!-- Email field (Full width) -->
                        <div class="space-y-2 w-full">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   placeholder="Enter your email"
                                   class="contact-input {{ auth()->check() ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                   autocomplete="email"
                                   value="{{ auth()->check() ? auth()->user()->email : '' }}"
                                   {{ auth()->check() ? 'readonly aria-readonly=true' : '' }}
                                   required>
                        </div>
                    </div>
                    
                    <!-- Message/Description (Full width container) -->
                    <div class="space-y-2 w-full">
                        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="12"
                            placeholder="Description"
                            class="contact-input max-h-[30rem] -mx-1 !w-full"
                            required></textarea>
                    </div>

                    
                    <!-- Send Button -->
                    <div>
                        <button id="contactSubmitButton" type="submit" class="ret-green-bg text-white font-bold py-3 px-8 rounded-lg hover:bg-green-700 transition duration-300 shadow-md">
                            Send
                        </button>
                    </div>
                </form>
                
                <!-- Simple confirmation/error message box (replaces alert()) -->
                <div id="messageBox" class="mt-4 hidden p-3 rounded-lg text-sm" role="alert"></div>
            </div>

            <!-- Right Card: Get in touch with Us (Contact Details) -->
            <div class="bg-white p-8 rounded-xl shadow-xl border border-gray-100">
                <h2 class="text-2xl font-bold mb-10 text-gray-800 border-b pb-3">Get in touch With Us</h2>
                
                <ul class="space-y-10 text-gray-700 text-lg">
                    <!-- Location -->
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt text-2xl ret-red-text mr-4 mt-1"></i>
                        <div>
                            <a href="https://maps.app.goo.gl/MVBdw77FTwX9mmMV9" target="_blank" class="hover:underline font-bold">
                                RET Bldg. CLSU, Muñoz, Nueva Ecija, Philippines
                            </a>
                        </div>
                    </li>
                    
                    <!-- Phone -->
                    <li class="flex items-start">
                        <i class="fas fa-phone text-2xl ret-red-text mr-4 mt-1"></i>
                        <div>
                            <p class="font-bold">0927 719 7639</p>
                        </div>
                    </li>
                    
                    <!-- Facebook -->
                    <li class="flex items-start">
                        <i class="fab fa-facebook-f text-2xl ret-red-text mr-4 mt-1"></i>
                        <div>
                            <a href="https://www.facebook.com/clsuretcafe?rdid=inNkCQLwgAL4bFeF&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F15qu3aENz9%2F#" target="_blank" class="hover:underline font-bold">CLSU RET Cafeteria</a>
                        </div>
                    </li>
                    
                    <!-- Email -->
                    <li class="flex items-start">
                        <i class="fas fa-at text-2xl ret-red-text mr-4 mt-1"></i>
                        <div>
                            <!-- The target email for the mailto function -->
                            <p id="targetEmail" class="font-bold">RETCafeteria@clsu2.edu.ph</p>
                        </div>
                    </li>
                </ul>
            </div>
            
        </div>
    </div>
</section>

{{-- Contact Success/Error Modals (reused components) --}}
<x-success-modal name="contact-message-success" title="Message Sent" maxWidth="sm" overlayClass="bg-black/50">
    <p id="contactSuccessMessage" class="text-sm text-green-700">
        Your message has been sent to our admin team.
    </p>
</x-success-modal>

<x-admin.ui.modal name="contact-message-error" title="Message Not Sent" variant="error" maxWidth="sm">
    <p id="contactErrorMessage" class="text-sm text-admin-neutral-700">
        We couldn't send your message. Please try again.
    </p>
    <x-slot name="footer">
        <button type="button"
                class="px-4 py-2 rounded-admin bg-admin-neutral-200 text-admin-neutral-700 hover:bg-admin-neutral-300 transition"
                @click="$dispatch('close-admin-modal', 'contact-message-error')">
            Close
        </button>
    </x-slot>
</x-admin.ui.modal>

<script>
    /**
     * Helper function to display a message in the message box.
     * @param {string} message - The text message to display.
     * @param {boolean} isSuccess - True for success (green), false for error (red).
     */
    function showSuccessModal(message) {
        const messageEl = document.getElementById('contactSuccessMessage');
        if (messageEl && message) {
            messageEl.textContent = message;
        }
        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'contact-message-success' }));
    }

    function showErrorModal(message) {
        const messageEl = document.getElementById('contactErrorMessage');
        if (messageEl && message) {
            messageEl.textContent = message;
        }
        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'contact-message-error' }));
    }
    
    /**
     * Handles the form submission event, constructs the mailto link, 
     * and attempts to open the user's default email client.
     * @param {Event} event - The form submission event.
     */
    function handleContactFormSubmit(event) {
        event.preventDefault(); // Stop the default form submission

        const form = document.getElementById('contactForm');
        const submitButton = document.getElementById('contactSubmitButton');
        const name = document.getElementById('name').value.trim();
        const emailInput = document.getElementById('email');
        if (!emailInput) {
            showErrorModal("Input valid email address");
            return;
        }
        const email = emailInput.value.trim();
        const message = document.getElementById('message').value.trim();
        const targetEmail = document.getElementById('targetEmail').textContent.trim();

        if (!name || !email || !message) {
            showErrorModal("Please fill out all required fields.");
            return;
        }

        if (!emailInput.checkValidity()) {
            showErrorModal("Please enter a valid email address.");
            emailInput.focus();
            return;
        }

        if (submitButton) {
            if (!submitButton.dataset.defaultText) {
                submitButton.dataset.defaultText = submitButton.textContent.trim();
            }
            submitButton.disabled = true;
            submitButton.textContent = "Sending...";
            submitButton.classList.add('opacity-70', 'cursor-not-allowed');
            submitButton.setAttribute('aria-busy', 'true');
        }

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: formData
        })
            .then(async (response) => {
                const payload = await response.json().catch(() => null);

                if (response.ok) {
                    return payload;
                }

                if (response.status === 422) {
                    const firstError = payload?.errors ? Object.values(payload.errors).flat()[0] : null;
                    throw new Error(firstError || "Please check your inputs and try again.");
                }

                throw new Error(payload?.message || "Something went wrong. Please try again later.");
            })
            .then((data) => {
                if (data && data.success === false) {
                    throw new Error(data.message || "Failed to send message. Please try again.");
                }
                form.reset();
                showSuccessModal(data.message || "Message sent successfully!");
            })
            .catch((error) => {
                console.error("Contact form submission failed:", error);
                showErrorModal(error.message || ("Failed to send message. Please email us at " + targetEmail));
            })
            .finally(() => {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = submitButton.dataset.defaultText || "Send";
                    submitButton.classList.remove('opacity-70', 'cursor-not-allowed');
                    submitButton.removeAttribute('aria-busy');
                }
            });
    }
</script>

@endsection
