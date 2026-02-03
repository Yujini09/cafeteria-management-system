<!-- Load Font Awesome Link Here for icons -->
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

<!-- 1. HERO SECTION -->
<section class="contact-hero-bg py-20 lg:py-20 bg-gray-900 text-white relative overflow-hidden">
    <!-- Overlay for better text contrast -->
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

                <form id="contactForm" onsubmit="handleContactFormSubmit(event)" class="space-y-4">
                    
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
                            <input type="email" id="email" name="email" placeholder="Enter your email" class="contact-input" autocomplete="email" required>
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
                            autocomplete="off"
                            required></textarea>
                    </div>

                    
                    <!-- Send Button -->
                    <div>
                        <button type="submit" class="ret-green-bg text-white font-bold py-3 px-8 rounded-lg hover:bg-green-700 transition duration-300 shadow-md">
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
                            <a href="https://maps.app.goo.gl/MVBdw77FTwX9mmMV9"
                                target="_blank"
                                class="font-bold hover:underline text-gray-700">
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

<script>
    /**
     * Helper function to display a message in the message box.
     * @param {string} message - The text message to display.
     * @param {boolean} isSuccess - True for success (green), false for error (red).
     */
    function displayMessage(message, isSuccess) {
        const messageBox = document.getElementById('messageBox');
        
        // Reset classes
        messageBox.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');
        
        if (isSuccess) {
            messageBox.classList.add('bg-green-100', 'text-green-700');
        } else {
            messageBox.classList.add('bg-red-100', 'text-red-700');
        }
        
        messageBox.textContent = message;
        
        // Hide message after 7 seconds
        setTimeout(() => {
            messageBox.classList.add('hidden');
        }, 7000);
    }
    
    /**
     * Handles the form submission event, constructs the mailto link, 
     * and attempts to open the user's default email client.
     * @param {Event} event - The form submission event.
     */
    function handleContactFormSubmit(event) {
        event.preventDefault(); // Stop the default form submission

        const form = document.getElementById('contactForm');
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const message = document.getElementById('message').value.trim();
        const targetEmail = document.getElementById('targetEmail').textContent.trim();

        if (!name || !email || !message) {
            displayMessage("Please fill out all required fields.", false);
            return;
        }
        
        // Construct the subject line
        const subject = encodeURIComponent(`Inquiry from ${name} (${email})`);

        // Construct the body, including the user's name and email for context
        const body = encodeURIComponent(
            `Name: ${name}\nEmail: ${email}\n\nMessage:\n${message}`
        );
        
        // Create the full mailto link
        const mailtoLink = `mailto:${targetEmail}?subject=${subject}&body=${body}`;

        try {
            // Attempt to open the default email client
            window.location.href = mailtoLink;
            
            // Clear the form fields after successful attempt
            form.reset();
            
            // Provide feedback to the user
            displayMessage("Your message is ready! Please check your default email client to send it.", true);

        } catch (error) {
            // Fallback error message
            console.error("Mailto function failed:", error);
            displayMessage("Failed to open email client. Please manually email us at " + targetEmail, false);
        }
    }
</script>

@endsection