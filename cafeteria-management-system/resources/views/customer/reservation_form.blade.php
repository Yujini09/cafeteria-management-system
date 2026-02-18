@extends('layouts.app')

@section('title', 'Make a Reservation')

@section('styles')
.reservation-hero-bg {
background-image: url('/images/banner1.jpg');
background-size: cover;
background-position: top;
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
            color: #374151 !important; /* Force dark gray text */
            font-weight: 500;
        }
        .calendar-day-inactive {
            background-color: transparent;
        }
        .calendar-day-inactive:hover {
            background-color: rgb(243 244 246); /* gray-100 */
        }
        .calendar-day-other-month {
            color: rgb(156 163 175) !important; /* gray-400 */
            cursor: default;
            background-color: transparent;
        }
        .calendar-day-in-range {
            background-color: rgba(34, 197, 94, 0.2); /* Light green for dates in range */
        }
        .calendar-day-range-start, .calendar-day-range-end {
            background-color: #16a34a !important; /* clsu-green */
            color: white !important; /* White text on green background */
            font-weight: 700;
        }
        .day-tab {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            background-color: #f3f4f6;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
        }
        .day-tab-active {
            background-color: #16a34a;
            color: white;
            border-color: #15803d;
        }
        .time-section {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            background-color: #f9fafb;
        }
        .time-section-active {
            border-color: #16a34a;
            background-color: #f0fdf4;
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
    <section class="bg-gray-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-center">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-clsu-green rounded-full">
                            <span class="text-white font-bold">1</span>
                        </div>
                        <div class="ml-2 text-sm font-medium text-clsu-green">Reservation Details</div>
                    </div>
                    <div class="w-16 h-1 bg-gray-300 mx-2"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full">
                            <span class="text-white font-bold">2</span>
                        </div>
                        <div class="ml-2 text-sm font-medium text-gray-500">Menu Selection</div>
                    </div>
                    <div class="w-16 h-1 bg-gray-300 mx-2"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full">
                            <span class="text-gray-500 font-bold">3</span>
                        </div>
                        <div class="ml-2 text-sm font-medium text-gray-500">Confirmation</div>
                    </div>
                </div>
            </div>
        </div>
            <form id="reservation-form" action="{{ route('reservation.create') }}" method="GET" class="space-y-10">
                <!-- Hidden fields to pass personal information -->
                <input type="hidden" name="name" id="hidden-name">
                <input type="hidden" name="department" id="hidden-department">
                <input type="hidden" name="address" id="hidden-address">
                <input type="hidden" name="email" id="hidden-email">
                <input type="hidden" name="phone" id="hidden-phone">
                <input type="hidden" name="activity" id="hidden-activity">
                <input type="hidden" name="venue" id="hidden-venue">
                <input type="hidden" name="project_name" id="hidden-project_name">
                <input type="hidden" name="account_code" id="hidden-account_code">

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
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span> (CLSU Email only)</label>
                            <input type="email" id="email" name="email" placeholder="Enter your CLSU email (@clsu2.edu.ph)" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm">
                            <div id="email-error" class="text-sm text-red-500 mt-1 hidden">
                                Please use a valid CLSU email address (must end with @clsu2.edu.ph)
                            </div>
                        </div>


                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required
                                pattern="[0-9]{10,15}"
                                inputmode="numeric"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
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

                    <!-- Right Column: Date & Time Selection (Now with date range) -->
                    <div class="bg-white p-8 rounded-xl shadow-2xl space-y-6 border border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-800 border-b pb-4">Date & Time Selection</h2>

                        <!-- Date Range Selection -->
                        <div class="space-y-4">
                            <label class="block text-base font-medium text-gray-700 mb-2">Date Range</label>
                            
                            <!-- Selected Date Range Display -->
                            <div id="selected-date-range-container" class="flex flex-col gap-2 mb-4 min-h-[40px] items-start p-3 border border-dashed border-gray-300 rounded-lg bg-gray-50">
                                <div id="no-dates-selected" class="text-sm text-gray-500 italic">
                                    Select a date range by clicking the start and end dates on the calendar.
                                </div>
                                <div id="date-range-display" class="hidden">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-700">From:</span>
                                        <div class="date-selector-btn date-selector-btn-active flex items-center gap-1">
                                            <span id="start-date-display"></span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">To:</span>
                                        <div class="date-selector-btn date-selector-btn-active flex items-center gap-1">
                                            <span id="end-date-display"></span>
                                        </div>
                                        <span id="total-days-indicator" class="ml-2 text-sm font-medium text-clsu-green"></span>
                                    </div>
                                </div>
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
                            
                            <!-- Day Tabs for Time Selection -->
                            <div id="day-tabs-container" class="hidden mt-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-3">Time Selection for Each Day</h3>
                                <div id="day-tabs" class="flex flex-wrap gap-2 mb-4">
                                    <!-- Day tabs will be generated here -->
                                </div>
                                
                                <!-- Time Selection Sections -->
                                <div id="time-sections-container">
                                    <!-- Time sections for each day will be generated here -->
                                </div>
                            </div>
                            
                            <!-- Hidden inputs to store selected date range and times for form submission -->
                            <input type="hidden" id="start_date_input" name="start_date" required>
                            <input type="hidden" id="end_date_input" name="end_date" required>
                            <input type="hidden" id="day_times_input" name="day_times" required>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="text-center pt-4">
                    <button type="submit" 
                        id="menu-selection-btn"
                        class="px-8 py-3 bg-clsu-green text-white rounded-lg hover:bg-green-700 transition duration-150 shadow-lg font-semibold">
                        Proceed to Menu Selection
                    </button>
                    <div id="validation-message" class="mt-4 text-sm font-semibold text-red-600 hidden">
                        Please fill up the form, select a valid date range, and ensure the time slot is valid.
                    </div>
                </div>

            </form>
        </div>
    </section>

    <script>
        // Global state for the calendar
        let currentDisplayDate = new Date();
        let selectedStartDate = null;
        let selectedEndDate = null;
        let isSelectingStartDate = true; // Toggle between selecting start and end date
        let selectedDays = []; // Array to store all selected days
        let dayTimes = {}; // Object to store times for each day
        let activeDayTab = 0; // Index of currently active day tab
        
        const calendarDaysEl = document.getElementById('calendar-days');
        const monthYearEl = document.getElementById('current-month-year');
        const selectedDateRangeContainer = document.getElementById('selected-date-range-container');
        const dateRangeDisplay = document.getElementById('date-range-display');
        const startDateDisplay = document.getElementById('start-date-display');
        const endDateDisplay = document.getElementById('end-date-display');
        const totalDaysIndicator = document.getElementById('total-days-indicator');
        const startDateInput = document.getElementById('start_date_input');
        const endDateInput = document.getElementById('end_date_input');
        const dayTimesInput = document.getElementById('day_times_input');
        const dayTabsContainer = document.getElementById('day-tabs-container');
        const dayTabsEl = document.getElementById('day-tabs');
        const timeSectionsContainer = document.getElementById('time-sections-container');
        const menuSelectionBtn = document.getElementById('menu-selection-btn');
        const validationMessageEl = document.getElementById('validation-message');

        // Utility function to format date as YYYY-MM-DD
        const formatDate = (date) => {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        };

        // Utility function to format date for display
        const formatDateDisplay = (dateStr) => {
            const date = new Date(dateStr);
            return date.toLocaleString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric' 
            });
        };

        // Utility function to get all dates between start and end
        const getDatesInRange = (startDate, endDate) => {
            const dates = [];
            const currentDate = new Date(startDate);
            const end = new Date(endDate);
            
            while (currentDate <= end) {
                dates.push(formatDate(new Date(currentDate)));
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            return dates;
        };

        // Utility function to check if a date is within the selected range
        const isDateInRange = (dateStr) => {
            if (!selectedStartDate || !selectedEndDate) return false;
            
            const date = new Date(dateStr);
            const start = new Date(selectedStartDate);
            const end = new Date(selectedEndDate);
            
            return date >= start && date <= end;
        };

        // Utility function to check if a date is the start or end of the range
        const isRangeBoundary = (dateStr) => {
            return dateStr === selectedStartDate || dateStr === selectedEndDate;
        };

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
                
                // Determine the status of this date
                let status = 'inactive';
                if (isDateInRange(dateStr)) {
                    status = 'in-range';
                }
                if (isRangeBoundary(dateStr)) {
                    if (dateStr === selectedStartDate) {
                        status = 'range-start';
                    } else if (dateStr === selectedEndDate) {
                        status = 'range-end';
                    }
                }
                
                // Only allow selection of current or future dates
                const isPast = date < today;
                if (isPast) {
                    status = 'other-month'; // Treat past dates as disabled/other-month
                }
                
                const cell = createCalendarDay(day, status, dateStr);
                
                // Add click listener only for non-past dates
                if (!isPast) {
                    cell.addEventListener('click', () => handleDateSelection(dateStr, cell));
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
            
            // Apply appropriate custom class from CSS and set inline styles for visibility
            if (status === 'range-start' || status === 'range-end') {
                cell.classList.add('calendar-day-range-start');
                // Force white text on green background for range boundaries
                cell.style.backgroundColor = '#16a34a';
                cell.style.color = 'white';
                cell.style.fontWeight = '700';
            } else if (status === 'in-range') {
                cell.classList.add('calendar-day-in-range');
                // Force dark text on light green background for in-range dates
                cell.style.backgroundColor = 'rgba(34, 197, 94, 0.2)';
                cell.style.color = '#374151';
                cell.style.fontWeight = '500';
            } else if (status === 'inactive') {
                cell.classList.add('calendar-day-inactive');
                // Force dark text on transparent background
                cell.style.backgroundColor = 'transparent';
                cell.style.color = '#374151';
                cell.style.fontWeight = '500';
            } else if (status === 'other-month') {
                cell.classList.add('calendar-day-other-month');
                // Force light gray text for other months
                cell.style.backgroundColor = 'transparent';
                cell.style.color = '#9ca3af';
                cell.style.fontWeight = '500';
            }

            cell.textContent = day;
            if (dateStr) {
                cell.dataset.date = dateStr;
            }
            return cell;
        };

        /**
         * Handles date selection for the range picker.
         */
        const handleDateSelection = (dateStr, cell) => {
            if (isSelectingStartDate) {
                // Selecting the start date
                selectedStartDate = dateStr;
                selectedEndDate = null; // Reset end date when selecting a new start
                isSelectingStartDate = false;
                selectedDays = [dateStr];
                dayTimes = { [dateStr]: { start_time: '07:00', end_time: '10:00' } };
            } else {
                // Selecting the end date
                const startDate = new Date(selectedStartDate);
                const endDate = new Date(dateStr);
                
                // Ensure end date is not before start date
                if (endDate < startDate) {
                    // Swap dates if end is before start
                    selectedEndDate = selectedStartDate;
                    selectedStartDate = dateStr;
                } else {
                    selectedEndDate = dateStr;
                }
                
                isSelectingStartDate = true; // Reset for next selection
                
                // Generate all dates in range
                selectedDays = getDatesInRange(selectedStartDate, selectedEndDate);
                
                // Initialize times for each day
                dayTimes = {};
                selectedDays.forEach(day => {
                    dayTimes[day] = { start_time: '07:00', end_time: '10:00' };
                });
            }
            
            updateDateRangeDisplay();
            renderDayTabs();
            renderCalendar(); // Re-render to show the updated range
            validateForm();
        };

        /**
         * Updates the date range display above the calendar.
         */
        const updateDateRangeDisplay = () => {
            if (selectedStartDate && selectedEndDate) {
                // Both dates selected - show the range
                startDateDisplay.textContent = formatDateDisplay(selectedStartDate);
                endDateDisplay.textContent = formatDateDisplay(selectedEndDate);
                
                // Calculate and display total days
                const start = new Date(selectedStartDate);
                const end = new Date(selectedEndDate);
                const totalDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
                totalDaysIndicator.textContent = `(${totalDays} day${totalDays > 1 ? 's' : ''})`;
                
                dateRangeDisplay.classList.remove('hidden');
                document.getElementById('no-dates-selected').classList.add('hidden');
                
                // Update hidden inputs for form submission
                startDateInput.value = selectedStartDate;
                endDateInput.value = selectedEndDate;
                
                // Show day tabs container
                dayTabsContainer.classList.remove('hidden');
            } else if (selectedStartDate) {
                // Only start date selected
                startDateDisplay.textContent = formatDateDisplay(selectedStartDate);
                endDateDisplay.textContent = 'Select end date';
                totalDaysIndicator.textContent = '';
                
                dateRangeDisplay.classList.remove('hidden');
                document.getElementById('no-dates-selected').classList.add('hidden');
                
                // Update hidden inputs for form submission
                startDateInput.value = selectedStartDate;
                endDateInput.value = '';
                
                // Show day tabs container for single day
                dayTabsContainer.classList.remove('hidden');
            } else {
                // No dates selected
                dateRangeDisplay.classList.add('hidden');
                document.getElementById('no-dates-selected').classList.remove('hidden');
                totalDaysIndicator.textContent = '';
                
                // Clear hidden inputs
                startDateInput.value = '';
                endDateInput.value = '';
                
                // Hide day tabs container
                dayTabsContainer.classList.add('hidden');
            }
        };

        /**
         * Renders day tabs for time selection
         */
        const renderDayTabs = () => {
            dayTabsEl.innerHTML = '';
            timeSectionsContainer.innerHTML = '';
            
            selectedDays.forEach((day, index) => {
                // Create day tab
                const tab = document.createElement('div');
                tab.className = `day-tab ${index === activeDayTab ? 'day-tab-active' : ''}`;
                tab.textContent = `Day ${index + 1} (${formatDateDisplay(day)})`;
                tab.dataset.dayIndex = index;
                
                tab.addEventListener('click', () => {
                    activeDayTab = index;
                    renderDayTabs(); // Re-render to update active state
                });
                
                dayTabsEl.appendChild(tab);
                
                // Create time section for this day
                const timeSection = document.createElement('div');
                timeSection.className = `time-section ${index === activeDayTab ? 'time-section-active' : 'hidden'}`;
                timeSection.id = `time-section-${index}`;
                
                timeSection.innerHTML = `
                    <h4 class="font-semibold text-gray-700 mb-3">Time Selection for ${formatDateDisplay(day)}</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_time_${index}" class="block text-xs font-semibold text-gray-500 mb-1">Start Time</label>
                            <input type="time" id="start_time_${index}" name="start_time_${index}" 
                                value="${dayTimes[day]?.start_time || '07:00'}" 
                                class="day-time-input w-full px-4 py-3 border border-gray-300 rounded-lg appearance-none text-center focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm"
                                data-day="${day}" data-type="start_time">
                        </div>
                        <div>
                            <label for="end_time_${index}" class="block text-xs font-semibold text-gray-500 mb-1">End Time</label>
                            <input type="time" id="end_time_${index}" name="end_time_${index}" 
                                value="${dayTimes[day]?.end_time || '10:00'}" 
                                class="day-time-input w-full px-4 py-3 border border-gray-300 rounded-lg appearance-none text-center focus:ring-clsu-green focus:border-clsu-green transition duration-150 shadow-sm"
                                data-day="${day}" data-type="end_time">
                        </div>
                    </div>
                    <div id="time-error-${index}" class="text-sm text-red-500 hidden mt-2 font-semibold">
                        Error: End time must be after start time.
                    </div>
                `;
                
                timeSectionsContainer.appendChild(timeSection);
            });
            
            // Add event listeners to time inputs
            document.querySelectorAll('.day-time-input').forEach(input => {
                input.addEventListener('change', handleTimeChange);
            });
            
            // Update the hidden input with current day times
            updateDayTimesInput();
        };

        /**
         * Handles time changes for individual days
         */
        const handleTimeChange = (e) => {
            const day = e.target.dataset.day;
            const type = e.target.dataset.type;
            const value = e.target.value;
            
            // Update the dayTimes object
            if (!dayTimes[day]) {
                dayTimes[day] = {};
            }
            dayTimes[day][type] = value;
            
            // Validate time for this day
            const dayIndex = selectedDays.indexOf(day);
            const startTime = dayTimes[day].start_time;
            const endTime = dayTimes[day].end_time;
            const errorEl = document.getElementById(`time-error-${dayIndex}`);
            
            if (startTime && endTime && startTime >= endTime) {
                errorEl.classList.remove('hidden');
            } else {
                errorEl.classList.add('hidden');
            }
            
            // Update the hidden input
            updateDayTimesInput();
            validateForm();
        };

        /**
         * Updates the hidden input with JSON string of day times
         */
        const updateDayTimesInput = () => {
            dayTimesInput.value = JSON.stringify(dayTimes);
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

        // --- Form Validation & Button State ---
        const validateForm = () => {
            const isDateRangeSelected = selectedStartDate && selectedEndDate;
            const isNativeValid = document.getElementById('reservation-form').checkValidity(); // Check HTML5 required fields
            
            // Validate all day times
            let allTimesValid = true;
            if (isDateRangeSelected) {
                for (const day of selectedDays) {
                    const startTime = dayTimes[day]?.start_time;
                    const endTime = dayTimes[day]?.end_time;
                    
                    if (!startTime || !endTime || startTime >= endTime) {
                        allTimesValid = false;
                        break;
                    }
                }
            }
            
            const isValid = isDateRangeSelected && isNativeValid && allTimesValid;
            
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
                if (!isDateRangeSelected) {
                    validationMessageEl.textContent = 'Please select a complete date range (start and end dates).';
                } else if (!allTimesValid) {
                    validationMessageEl.textContent = 'Please ensure the end time is after the start time for all days.';
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
        
        // Initialize form submission handler
        initializeFormSubmission();
        
        // Initialize CLSU email validation
        initializeEmailValidation();
    });

    function initializeFormSubmission() {
        document.getElementById('reservation-form').addEventListener('submit', (e) => {
            // Re-run validation one last time
            if (!validateForm()) {
                e.preventDefault(); // Stop submission if validation fails
                return;
            }
            
            // Copy all personal info to hidden fields
            const fields = ['name', 'department', 'address', 'email', 'phone', 'activity', 'venue', 'project_name', 'account_code'];
            fields.forEach(field => {
                const visibleInput = document.getElementById(field);
                const hiddenInput = document.getElementById(`hidden-${field}`);
                if (visibleInput && hiddenInput) {
                    hiddenInput.value = visibleInput.value;
                }
            });
            
            // The form will now submit with all data as GET parameters
        });
    }

    function initializeEmailValidation() {
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('email-error');
        const form = document.getElementById('reservation-form');
        
        function validateCLSUEmail(email) {
            return email.endsWith('@clsu2.edu.ph');
        }
        
        emailInput.addEventListener('blur', function() {
            if (emailInput.value && !validateCLSUEmail(emailInput.value)) {
                emailError.classList.remove('hidden');
                emailInput.classList.add('border-red-500');
            } else {
                emailError.classList.add('hidden');
                emailInput.classList.remove('border-red-500');
            }
        });
        
        emailInput.addEventListener('input', function() {
            if (validateCLSUEmail(emailInput.value)) {
                emailError.classList.add('hidden');
                emailInput.classList.remove('border-red-500');
            }
        });
        
        // Override form submission for email validation
        form.addEventListener('submit', function(e) {
            if (emailInput.value && !validateCLSUEmail(emailInput.value)) {
                e.preventDefault();
                emailError.classList.remove('hidden');
                emailInput.classList.add('border-red-500');
                emailInput.focus();
                alert('Please use a valid CLSU email address (@clsu2.edu.ph)');
            }
        });
    }

    // Function to copy form data to hidden fields before submission
    function copyFormDataToHiddenFields() {
        const fields = ['name', 'department', 'address', 'email', 'phone', 'activity', 'venue', 'project_name', 'account_code'];
        
        fields.forEach(field => {
            const visibleInput = document.getElementById(field);
            const hiddenInput = document.getElementById(`hidden-${field}`);
            if (visibleInput && hiddenInput) {
                hiddenInput.value = visibleInput.value;
            }
        });
    }

    // Prevent actual form submission for demonstration
    document.getElementById('reservation-form').addEventListener('submit', (e) => {
        // Re-run validation one last time
        if (!validateForm()) {
             e.preventDefault(); // Stop submission if validation fails
             // HTML5 required validation will also stop the submission and show a browser message
             // Our custom validation message is already updated inside validateForm()
            return;
        }
        
        // Copy form data to hidden fields before submission
        copyFormDataToHiddenFields();
    });

    // CLSU email Validation
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('email-error');
        const form = document.getElementById('reservation-form');
        
        function validateCLSUEmail(email) {
            return email.endsWith('@clsu2.edu.ph');
        }
        
        emailInput.addEventListener('blur', function() {
            if (emailInput.value && !validateCLSUEmail(emailInput.value)) {
                emailError.classList.remove('hidden');
                emailInput.classList.add('border-red-500');
            } else {
                emailError.classList.add('hidden');
                emailInput.classList.remove('border-red-500');
            }
        });
        
        emailInput.addEventListener('input', function() {
            if (validateCLSUEmail(emailInput.value)) {
                emailError.classList.add('hidden');
                emailInput.classList.remove('border-red-500');
            }
        });
        
        // Override form submission
        form.addEventListener('submit', function(e) {
            if (emailInput.value && !validateCLSUEmail(emailInput.value)) {
                e.preventDefault();
                emailError.classList.remove('hidden');
                emailInput.classList.add('border-red-500');
                emailInput.focus();
                alert('Please use a valid CLSU email address (@clsu2.edu.ph)');
            }
        });
    });

    </script>

@endsection