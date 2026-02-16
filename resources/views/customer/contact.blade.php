@extends('layouts.app')

@section('title', 'Contact Us - CLSU RET Cafeteria')

@section('content')

<!-- Old Contact Hero Design -->
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

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Left Card: Customer Support Form -->
            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-200 h-full flex flex-col">
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Contact Support</h2>
                    <p class="text-gray-600">We're here to help! Send us your questions or concerns.</p>
                </div>

                <form id="contactForm" method="POST" action="{{ route('contact.send') }}" class="space-y-6 flex-grow">
                    @csrf
                    
                    <!-- Name and Email in responsive grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name field -->
                        <div class="space-y-3">
                            <label for="name" class="block text-sm font-semibold text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Full Name
                            </label>
                            <div class="relative">
                                <input type="text"
                                       id="name"
                                       name="name"
                                       placeholder="Enter your full name"
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 {{ auth()->check() ? 'bg-gray-50 text-gray-600' : 'bg-white' }}"
                                       autocomplete="name"
                                       value="{{ auth()->check() ? auth()->user()->name : '' }}"
                                       {{ auth()->check() ? 'readonly aria-readonly=true' : '' }}
                                       required>
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email field -->
                        <div class="space-y-3">
                            <label for="email" class="block text-sm font-semibold text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Email Address
                            </label>
                            <div class="relative">
                                <input type="email"
                                       id="email"
                                       name="email"
                                       placeholder="your.email@example.com"
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 {{ auth()->check() ? 'bg-gray-50 text-gray-600' : 'bg-white' }}"
                                       autocomplete="email"
                                       value="{{ auth()->check() ? auth()->user()->email : '' }}"
                                       {{ auth()->check() ? 'readonly aria-readonly=true' : '' }}
                                       required>
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Message - Increased Height -->
                    <div class="space-y-3 flex-grow">
                        <label for="message" class="block text-sm font-semibold text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            Your Message
                            <span class="ml-auto text-xs text-gray-500 font-normal">Minimum 20 characters</span>
                        </label>
                        <div class="relative h-full">
                            <textarea
                                id="message"
                                name="message"
                                placeholder="How can we help you? Please describe your issue or question in detail..."
                                class="w-full h-64 px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 resize-none"
                                minlength="20"
                                required></textarea>
                            <div class="absolute left-3 top-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </div>
                            <div class="absolute bottom-2 right-2 text-xs text-gray-500" id="charCounter">
                                0 characters
                            </div>
                        </div>
                    </div>

                    <!-- Message Box -->
                    <div id="messageBox" class="hidden p-4 rounded-lg text-sm font-medium border" role="alert"></div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button id="contactSubmitButton" type="submit" 
                                class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold py-3 px-6 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Send Message
                        </button>
                        <p class="text-xs text-gray-500 text-center mt-2">
                            We'll respond to your inquiry within 24-48 hours
                        </p>
                    </div>
                </form>
            </div>

            <!-- Right Card: Contact Information -->
            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-200 h-full flex flex-col">
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Visit Us</h2>
                    <p class="text-gray-600">Find us at our convenient location</p>
                </div>

                <div class="space-y-6 mt-2 flex-grow">
                    <!-- Address Card -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Our Location</h3>
                                <p class="text-gray-700">
                                    RET Cafeteria, Central Luzon State University<br>
                                    Science City of Muñoz, 3119 Nueva Ecija, Philippines
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200">
                            <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800">Phone Number</h4>
                                <p class="text-gray-600">(044) 456-0701</p>
                            </div>
                        </div>

                        <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200">
                            <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800">Email Address</h4>
                                <p class="text-gray-600">support@clsu-ret.com</p>
                                <p class="text-sm text-gray-500">For general inquiries and support</p>
                            </div>
                        </div>

                        <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200">
                            <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800">Business Hours</h4>
                                <p class="text-gray-600">
                                    Monday - Friday: 8:00 AM - 5:00 PM
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mt-3 pt-6 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-800 mb-3">Additional Information</h4>
                    <div class="space-y-3 text-sm text-gray-600">
                        <p class="flex items-start">
                            <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            For reservation inquiries, please include your reservation ID if available
                        </p>
                        <p class="flex items-start">
                            <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Response time is typically within 24-48 business hours
                        </p>
                        <p class="flex items-start">
                            <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            For urgent matters, please call during business hours
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<x-success-modal name="contact-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
    <p id="contactSuccessMessage" class="text-sm text-gray-600">{{ session('contact_success', 'Message sent successfully! We\'ll get back to you soon.') }}</p>
</x-success-modal>

<!-- FAQ Section -->
<section class="py-16 bg-gradient-to-br from-green-50 to-blue-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-gray-700">Find quick answers to common questions</p>
        </div>
        
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition duration-200">
                    <span class="font-semibold text-gray-800">How do I make a reservation?</span>
                    <svg class="w-5 h-5 text-green-600 transform rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="px-6 pb-4 hidden">
                    <p class="text-gray-600">You can make a reservation through our online system by navigating to the "Make Reservation" page. You'll need to provide event details, select your menu, and confirm your booking.</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition duration-200">
                    <span class="font-semibold text-gray-800">What are your payment methods?</span>
                    <svg class="w-5 h-5 text-green-600 transform rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="px-6 pb-4 hidden">
                    <p class="text-gray-600">We accept various payment methods including online bank transfer, GCash, and over-the-counter payments at the CLSU cashier. Payment details will be provided upon reservation confirmation.</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition duration-200">
                    <span class="font-semibold text-gray-800">Can I modify or cancel my reservation?</span>
                    <svg class="w-5 h-5 text-green-600 transform rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="px-6 pb-4 hidden">
                    <p class="text-gray-600">Yes, you can modify or cancel your reservation up to 48 hours before the event. Please contact our support team or access your reservation through your account dashboard.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Custom styles for consistent height */
    .h-full {
        height: 100%;
    }
    
    .flex-grow {
        flex-grow: 1;
    }
    
    /* Focus styles */
    input:focus, textarea:focus {
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }
    
    /* Smooth transitions */
    input, textarea, button {
        transition: all 0.2s ease-in-out;
    }
    
    /* Character counter styling */
    #charCounter {
        transition: color 0.2s ease;
    }
    
    /* Message box styling */
    #messageBox.error {
        background-color: #fef2f2;
        border-color: #fecaca;
        color: #991b1b;
    }
    
    /* Contact Hero Background */
.contact-hero-bg {
    /* Assuming you have a general background image like spices.webp */
    background-image: url('/images/banner.jpg');
    background-size: cover;
    background-position: bottom;
}
</style>

<script>
    // Character counter for message textarea
    document.addEventListener('DOMContentLoaded', function() {
        const flashedSuccessMessage = @json(session('contact_success'));
        const flashedErrorMessage = @json(session('contact_error'));

        const contactForm = document.getElementById('contactForm');
        if (contactForm && contactForm.dataset.ajaxBound !== 'true') {
            contactForm.dataset.ajaxBound = 'true';
            contactForm.addEventListener('submit', handleContactFormSubmit);
        }

        if (flashedSuccessMessage) {
            showSuccessModal(flashedSuccessMessage);
        }
        if (flashedErrorMessage) {
            showErrorMessage(flashedErrorMessage);
        }

        const messageField = document.getElementById('message');
        if (messageField) {
            messageField.addEventListener('input', function(e) {
                const charCounter = document.getElementById('charCounter');
                const length = e.target.value.length;
                charCounter.textContent = `${length} characters`;
                
                if (length < 20) {
                    charCounter.classList.add('text-red-500');
                    charCounter.classList.remove('text-green-500');
                } else {
                    charCounter.classList.remove('text-red-500');
                    charCounter.classList.add('text-green-500');
                }
            });
        }

        // FAQ Accordion functionality
        const faqButtons = document.querySelectorAll('button[class*="px-6 py-4"]');
        faqButtons.forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                const icon = button.querySelector('svg');
                
                content.classList.toggle('hidden');
                icon.classList.toggle('rotate-0');
                icon.classList.toggle('rotate-180');
            });
        });
    });

    // Form submission handler
    function handleContactFormSubmit(event) {
        if (event && typeof event.preventDefault === 'function') {
            event.preventDefault();
        }

        const form = event?.target || document.getElementById('contactForm');
        if (!form) {
            return;
        }
        
        // Show loading state
        const button = document.getElementById('contactSubmitButton');
        if (!button) {
            return;
        }
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg class="animate-spin w-5 h-5 mr-2 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Sending...
        `;
        button.disabled = true;
        
        // Submit the form
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessModal(data.message || 'Message sent successfully! We\'ll get back to you soon.');
                form.reset();
                
                // Reset character counter
                document.getElementById('charCounter').textContent = '0 characters';
                document.getElementById('charCounter').classList.remove('text-green-500', 'text-red-500');
            } else {
                showErrorMessage(data.message || 'Error sending message. Please try again.');
            }
        })
        .catch(error => {
            showErrorMessage('Error sending message. Please try again.');
        })
        .finally(() => {
            // Reset button
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }

    function showSuccessModal(text) {
        const successMessage = document.getElementById('contactSuccessMessage');
        if (successMessage) {
            successMessage.textContent = text;
        }

        const messageBox = document.getElementById('messageBox');
        if (messageBox) {
            messageBox.classList.add('hidden');
        }

        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'contact-success' }));
    }

    function showErrorMessage(text) {
        const messageBox = document.getElementById('messageBox');
        messageBox.textContent = text;
        messageBox.className = 'p-4 rounded-lg text-sm font-medium border error';
        messageBox.classList.remove('hidden');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            messageBox.classList.add('hidden');
        }, 5000);
    }
</script>

@endsection
