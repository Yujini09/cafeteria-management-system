@extends('layouts.app')

@section('title', 'Make a Reservation')

@section('styles')
.reservation-hero-bg {
background-image: url('/images/banner1.jpg');
background-size: cover;
background-position: top;
}
}

/* Custom styles provided by the user, applied using Tailwind classes defined in config */
        .date-selector-btn {
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            transition-property: all;
            transition-duration: 200ms;
        }
        .date-selector-btn-active {
            background-color: white;
            color: ret-dark; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.25rem;
            text-align: center;
            font-size: 0.875rem;
        }
        .calendar-day {
            padding: 0.5rem;
            border-radius: 9999px;
            cursor: pointer;
            transition-property: color, background-color;
            transition-duration: 150ms;
        }
        .calendar-day-active {
            background-color: var(--clsu-green);
            color: white;
            font-weight: 700;
        }
        .calendar-day-inactive {
            color: rgb(55 65 81); /* gray-700 */
        }
        .calendar-day-inactive:hover {
            background-color: rgb(243 244 246); /* gray-100 */
        }
        .calendar-day-other-month {
            color: rgb(156 163 175); /* gray-400 */
            cursor: default;
        }

@endsection

@section('content')

<!-- Reservation Banner Header -->
    <section class="reservation-hero-bg py-20 lg:py-20 bg-gray-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl lg:text-5xl font-extrabold mb-3 tracking-wide">
                Make a Reservation
            </h1>
            <p class="text-lg lg:text-xl font-poppins opacity-90">
                Reserve your spot with us today!
            </p>
        </div>
    </section>

    <!-- Reservation Form Section -->
    <section class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form id="reservation-form" action="/reservation_form_menu" method="GET" class="space-y-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- Left Column: User and Event Details -->
                    <div class="bg-white p-8 rounded-xl shadow-2xl space-y-6 border border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-800 border-b pb-4">Personal & Event Information</h2>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="name" name="name" placeholder="Enter your name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm">
                        </div>

                        <!-- Department/Office -->
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department/Office</label>
                            <input type="text" id="department" name="department" placeholder="Enter your department/office" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm">
                        </div>
                        
                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" id="address" name="address" placeholder="Enter your address" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm">
                        </div>

                        <!-- Activity -->
                        <div>
                            <label for="activity" class="block text-sm font-medium text-gray-700 mb-1">Activity</label>
                            <input type="text" id="activity" name="activity" placeholder="Enter your activity" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm">
                        </div>

                        <!-- Venue -->
                        <div>
                            <label for="venue" class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                            <input type="text" id="venue" name="venue" placeholder="Enter your venue" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm">
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Name of Project -->
                            <div>
                                <label for="project_name" class="block text-sm font-medium text-gray-700 mb-1">Name of Project</label>
                                <input type="text" id="project_name" name="project_name" placeholder="Enter project name (optional)"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm">
                            </div>
                            
                            <!-- Account Code -->
                            <div>
                                <label for="account_code" class="block text-sm font-medium text-gray-700 mb-1">Account Code</label>
                                <input type="text" id="account_code" name="account_code" placeholder="Enter account code (optional)"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Date & Time Selection (Now fully functional with JS) -->
                    <div class="bg-white p-8 rounded-xl shadow-2xl space-y-6 border border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-800 border-b pb-4">Date & Time Selection</h2>

                        <!-- Start/End Time -->
                        <div class="space-y-4">
                            <label class="block text-base font-medium text-gray-700 mb-2">Time Slot</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="start_time" class="block text-xs font-semibold text-gray-500 mb-1">Start Time</label>
                                    <input type="time" id="start_time" name="start_time" value="07:00" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg appearance-none text-center focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm">
                                </div>
                                <div>
                                    <label for="end_time" class="block text-xs font-semibold text-gray-500 mb-1">End Time</label>
                                    <input type="time" id="end_time" name="end_time" value="10:00" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg appearance-none text-center focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm">
                                </div>
                            </div>
                            <div id="time-error" class="text-sm text-red-500 hidden mt-2 font-semibold">
                                Error: End time must be after start time.
                            </div>
                        </div>

                        <!-- Date Selection Chips -->
                        <div class="space-y-4">
                            <label class="block text-base font-medium text-gray-700 mb-2">Selected Dates</label>
                            <div id="selected-dates-container" class="flex flex-wrap gap-2 mb-6 min-h-[40px] items-center p-2 border border-dashed border-gray-300 rounded-lg bg-gray-50">
                                <!-- Selected dates chips appear here -->
                                <span id="no-dates-selected" class="text-sm text-gray-500 italic">Click dates on the calendar below to select.</span>
                            </div>
                            
                            <!-- Calendar Display -->
                            <div class="border border-gray-200 p-4 rounded-xl shadow-inner bg-white">
                                <div class="flex justify-between items-center mb-4 text-gray-700 font-semibold">
                                    <button type="button" id="prev-month-btn" class="text-gray-500 hover:text-clsu-green transition p-2 rounded-full hover:bg-gray-100">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                    </button>
                                    <span id="current-month-year" class="text-lg font-bold text-gray-800">Month Year</span>
                                    <button type="button" id="next-month-btn" class="text-gray-500 hover:text-clsu-green transition p-2 rounded-full hover:bg-gray-100">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </button>
                                </div>
                                
                                <div class="calendar-grid mb-2 font-bold text-gray-500 border-b pb-2">
                                    <span>S</span>
                                    <span>M</span>
                                    <span>T</span>
                                    <span>W</span>
                                    <span>T</span>
                                    <span>F</span>
                                    <span>S</span>
                                </div>
                                <div id="calendar-days" class="calendar-grid">
                                    <!-- Days will be generated here by JavaScript -->
                                </div>
                            </div>
                            
                            <!-- Hidden input to store selected dates for form submission -->
                            <input type="hidden" id="selected_dates_input" name="selected_dates" required>

                        </div>

                    </div>
                </div>

                <!-- Action Button -->
                <div class="text-center pt-8">
                    <a href="{{ route('reservation_form_menu') }}" 
                        id="menu-selection-btn"
                        class="inline-block bg-clsu-green px-10 py-4 rounded-lg font-bold text-white text-lg transition duration-300 shadow-xl cursor-pointer">
                        Proceed to Menu Selection
                    </a>
                    <div id="validation-message" class="mt-4 text-sm font-semibold text-red-600 hidden">
                        Please select at least one date and ensure the time slot is valid.
                    </div>
                </div>

            </form>
        </div>
    </section>

    <script>
        // Global state for the calendar
        let currentDisplayDate = new Date();
        let selectedDates = []; // Stores date strings (YYYY-MM-DD)
        const calendarDaysEl = document.getElementById('calendar-days');
        const monthYearEl = document.getElementById('current-month-year');
        const selectedDatesContainer = document.getElementById('selected-dates-container');
        const selectedDatesInput = document.getElementById('selected_dates_input');
        const timeErrorEl = document.getElementById('time-error');
        const startTimeEl = document.getElementById('start_time');
        const endTimeEl = document.getElementById('end_time');
        const menuSelectionBtn = document.getElementById('menu-selection-btn');
        const validationMessageEl = document.getElementById('validation-message');

        // Utility function to format date as YYYY-MM-DD
        const formatDate = (date) => {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        };

        // Utility function to check if a date is selected
        const isDateSelected = (dateStr) => selectedDates.includes(dateStr);

        // --- Core Calendar Functions ---

        /**
         * Renders the calendar grid for the currentDisplayDate month.
         */
        const renderCalendar = () => {
            const year = currentDisplayDate.getFullYear();
            const month = currentDisplayDate.getMonth(); // 0-11
            
            // Update Month/Year Header
            monthYearEl.textContent = currentDisplayDate.toLocaleString('en-US', { month: 'long' }) + ' ' + year;

            // Get the first day of the month (0 = Sun, 6 = Sat)
            const firstDayOfMonth = new Date(year, month, 1).getDay();
            
            // Get the number of days in the month
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            
            // Get the number of days in the previous month
            const daysInPrevMonth = new Date(year, month, 0).getDate();
            
            // Clear existing days
            calendarDaysEl.innerHTML = '';
            
            // Today's date for comparison (midnight of today)
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // 1. Add days from the previous month (other-month)
            for (let i = firstDayOfMonth; i > 0; i--) {
                const day = daysInPrevMonth - i + 1;
                const cell = createCalendarDay(day, 'other-month');
                calendarDaysEl.appendChild(cell);
            }

            // 2. Add current month days
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dateStr = formatDate(date);
                const isSelected = isDateSelected(dateStr);
                
                // Only allow selection of current or future dates
                const isPast = date < today;

                let status = 'inactive';
                if (isPast) {
                    status = 'other-month'; // Treat past dates as disabled/other-month
                } else if (isSelected) {
                    status = 'active';
                }
                
                const cell = createCalendarDay(day, status, dateStr);
                
                // Add click listener only for non-past dates
                if (!isPast) {
                    cell.addEventListener('click', () => toggleDateSelection(dateStr, cell));
                }

                calendarDaysEl.appendChild(cell);
            }

            // 3. Add days from the next month
            let totalCells = firstDayOfMonth + daysInMonth;
            let nextDay = 1;
            while (totalCells % 7 !== 0) {
                const cell = createCalendarDay(nextDay, 'other-month');
                calendarDaysEl.appendChild(cell);
                nextDay++;
                totalCells++;
                // Break after a maximum of 6 rows (42 cells) to prevent infinite loop
                if (totalCells > 42) break; 
            }
        };

        /**
         * Creates a single calendar day element.
         */
        const createCalendarDay = (day, status, dateStr = null) => {
            const cell = document.createElement('span');
            cell.classList.add('calendar-day');
            
            // Apply appropriate custom class from CSS
            if (status === 'active') {
                cell.classList.add('calendar-day-active');
            } else if (status === 'inactive') {
                cell.classList.add('calendar-day-inactive');
            } else if (status === 'other-month') {
                cell.classList.add('calendar-day-other-month');
            }

            cell.textContent = day;
            if (dateStr) {
                cell.dataset.date = dateStr;
            }
            return cell;
        };

        /**
         * Toggles a date's selection status.
         */
        const toggleDateSelection = (dateStr, cell) => {
            const index = selectedDates.indexOf(dateStr);
            
            if (index > -1) {
                // Deselect
                selectedDates.splice(index, 1);
                cell.classList.remove('calendar-day-active');
                cell.classList.add('calendar-day-inactive');
            } else {
                // Select
                selectedDates.push(dateStr);
                cell.classList.add('calendar-day-active');
                cell.classList.remove('calendar-day-inactive');
            }

            // Keep dates sorted chronologically
            selectedDates.sort((a, b) => new Date(a) - new Date(b));
            
            updateSelectedDatesDisplay();
            validateForm();
        };
        
        /**
         * Updates the list of selected date chips above the calendar.
         */
        const updateSelectedDatesDisplay = () => {
            selectedDatesContainer.innerHTML = '';
            
            if (selectedDates.length === 0) {
                const noDatesSpan = document.createElement('span');
                noDatesSpan.id = 'no-dates-selected';
                noDatesSpan.className = 'text-sm text-gray-500 italic';
                noDatesSpan.textContent = 'Click dates on the calendar below to select.';
                selectedDatesContainer.appendChild(noDatesSpan);
            } else {
                
                selectedDates.forEach(dateStr => {
                    const date = new Date(dateStr);
                    const formattedDate = date.toLocaleString('en-US', { month: 'short', day: 'numeric' });

                    const chip = document.createElement('div');
                    chip.classList.add('date-selector-btn', 'date-selector-btn-active', 'flex', 'items-center', 'gap-1');
                    chip.innerHTML = `
                        <span>${formattedDate}</span>
                        <button type="button" class="text-white/80 hover:text-white transition" data-date="${dateStr}" aria-label="Remove date ${formattedDate}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="black" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    
                    chip.querySelector('button').addEventListener('click', (e) => {
                        const dateToRemove = e.currentTarget.dataset.date;
                        // Find the corresponding cell and simulate a click to deselect
                        const cell = calendarDaysEl.querySelector(`[data-date="${dateToRemove}"]`);
                        if (cell) {
                            toggleDateSelection(dateToRemove, cell);
                        } else {
                            // If the cell isn't visible (in a different month), just remove from the list
                            selectedDates = selectedDates.filter(d => d !== dateToRemove);
                            updateSelectedDatesDisplay();
                        }
                    });

                    selectedDatesContainer.appendChild(chip);
                });
            }
            
            // Update hidden input field for form submission
            selectedDatesInput.value = selectedDates.join(',');
        };

        /**
         * Handles navigation to the previous month.
         */
        document.getElementById('prev-month-btn').addEventListener('click', () => {
            // Check if navigating to a past month is allowed (only current or future months)
            const today = new Date();
            today.setDate(1); // Set to 1st of the month
            today.setHours(0, 0, 0, 0);

            const potentialDate = new Date(currentDisplayDate);
            potentialDate.setMonth(potentialDate.getMonth() - 1);
            potentialDate.setDate(1); // Set to 1st of the month
            
            if (potentialDate < today) {
                // Optionally show a message or do nothing
                console.log("Cannot navigate to previous years/months.");
                return;
            }
            
            currentDisplayDate.setMonth(currentDisplayDate.getMonth() - 1);
            renderCalendar();
        });

        /**
         * Handles navigation to the next month.
         */
        document.getElementById('next-month-btn').addEventListener('click', () => {
            currentDisplayDate.setMonth(currentDisplayDate.getMonth() + 1);
            renderCalendar();
        });

        // --- Time Validation Functions ---
        const validateTime = () => {
            const start = startTimeEl.value;
            const end = endTimeEl.value;
            let isValid = true;
            
            if (start && end && start >= end) {
                timeErrorEl.classList.remove('hidden');
                isValid = false;
            } else {
                timeErrorEl.classList.add('hidden');
            }
            validateForm(isValid);
            return isValid;
        };
        
        // --- Form Validation & Button State ---
        const validateForm = (isTimeValid = validateTime()) => {
            const isDateSelected = selectedDates.length > 0;
            const isNativeValid = document.getElementById('reservation-form').checkValidity(); // Check HTML5 required fields
            const isValid = isTimeValid && isDateSelected && isNativeValid; // Combine all checks
            
            if (isValid) {
                menuSelectionBtn.disabled = false;
                menuSelectionBtn.classList.remove('bg-clsu-green/50', 'cursor-not-allowed', 'shadow-red-300');
                menuSelectionBtn.classList.add('bg-clsu-green', 'hover:bg-green-700', 'shadow-clsu-green/50');
                validationMessageEl.classList.add('hidden');
            } else {
                menuSelectionBtn.disabled = true;
                menuSelectionBtn.classList.add('bg-clsu-green/50', 'cursor-not-allowed');
                menuSelectionBtn.classList.remove('bg-clsu-green', 'hover:bg-green-700');
                validationMessageEl.classList.remove('hidden');
                
                // Update validation message based on failure reason
                if (!isDateSelected) {
                    validationMessageEl.textContent = 'Please select at least one reservation date.';
                } else if (!isTimeValid) {
                    validationMessageEl.textContent = 'Please ensure the end time is after the start time.';
                } else if (!isNativeValid) {
                     // Check for the first missing required field
                    const form = document.getElementById('reservation-form');
                    const firstInvalid = Array.from(form.elements).find(el => el.required && !el.value);
                    if (firstInvalid) {
                        validationMessageEl.textContent = `Please fill out the required field: ${firstInvalid.labels ? firstInvalid.labels[0].textContent.replace('*', '').trim() : firstInvalid.name}.`;
                    } else {
                        validationMessageEl.textContent = 'Please fill out all required fields.';
                    }
                } else {
                    validationMessageEl.textContent = 'Please fill out the form completely and resolve any errors.';
                }
            }
            return isValid; // Return the final validation state
        }


        // Event listeners for time change
        startTimeEl.addEventListener('change', validateTime);
        endTimeEl.addEventListener('change', validateTime);

                // Add listeners to required fields for real-time validation check
        document.getElementById('reservation-form').querySelectorAll('[required]').forEach(el => {
            el.addEventListener('input', () => validateForm());
        });

        // --- Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            // Initial render
            renderCalendar();
            
            // Initial validation (sets up button state)
            validateForm();
        });

        // Prevent actual form submission for demonstration
                document.getElementById('reservation-form').addEventListener('submit', (e) => {
            // Re-run validation one last time
            if (!validateForm()) {
                 e.preventDefault(); // Stop submission if validation fails
                 // HTML5 required validation will also stop the submission and show a browser message
                 // Our custom validation message is already updated inside validateForm()
                return;
            }
            

        });
        
    </script>

@endsection
