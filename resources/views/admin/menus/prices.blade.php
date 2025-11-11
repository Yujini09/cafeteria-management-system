@extends('layouts.sidebar')
@section('page-title','Manage Menu Prices')

@section('content')
<style>
/* Modern Design Variables */
:root {
    --primary: #00462E;
    --primary-light: #057C3C;
    --accent: #FF6B35;
    --neutral-50: #fafafa;
    --neutral-100: #f5f5f5;
    --neutral-200: #e5e5e5;
    --neutral-300: #d4d4d4;
    --neutral-400: #a3a3a3;
    --neutral-500: #737373;
    --neutral-600: #525252;
    --neutral-700: #404040;
    --neutral-800: #262626;
    --neutral-900: #171717;
}

/* Modern Card Styles */
.modern-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--neutral-100);
    overflow: hidden;
}

/* Modern Table Styles */
.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.875rem;
}

.modern-table th {
    background: var(--neutral-50);
    font-weight: 600;
    color: var(--neutral-700);
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--neutral-200);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modern-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--neutral-100);
    transition: all 0.2s ease;
}

.modern-table tr:last-child td {
    border-bottom: none;
}

.modern-table tr:hover td {
    background: var(--neutral-50);
}

/* Button Styles */
.btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 70, 46, 0.2);
}

.btn-secondary {
    background: var(--neutral-100);
    color: var(--neutral-700);
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-secondary:hover {
    background: var(--neutral-200);
}

/* Input Styles */
.price-input-container {
    position: relative;
}

.price-input {
    width: 100%;
    padding: 0.75rem 0.75rem 0.75rem 2rem;
    border: 1px solid var(--neutral-300);
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background: white;
}

.price-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.1);
}

.currency-symbol {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--neutral-600);
    font-weight: 600;
}

/* Header Styles */
.page-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-icon i {
    color: white;
    font-size: 1.25rem;
}

.header-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--neutral-900);
    letter-spacing: -0.5px;
}

/* Meal Time Badge */
.meal-time-badge {
    padding: 0.5rem 0.75rem;
    background: var(--neutral-50);
    border: 1px solid var(--neutral-200);
    border-radius: 8px;
    font-weight: 600;
    color: var(--neutral-800);
    text-transform: capitalize;
}

/* Highlight Animation
@keyframes highlightRow {
    0% {
        background-color: #c9fec7;
        transform: scale(1);
    }
    50% {
        background-color: #cbfec7;
        transform: scale(1.02);
    }
    100% {
        background-color: transparent;
        transform: scale(1);
    }
}

.highlight-row {
    animation: highlightRow 3s ease-in-out;
}

.highlight-input {
    border-color: #0bf51f !important;
    box-shadow: 0 0 0 3px rgba(11, 245, 50, 0.3) !important;
    transition: all 0.3s ease;
} */

/* Menu Card Styling for Inventory Sections */
.menu-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.menu-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #00462E 0%, #057C3C 100%);
}

.menu-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: #cbd5e0;
}
</style>

<div class="modern-card menu-card p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0" style="max-width: calc(100vw - 12rem);">
    <!-- Header -->
    <div class="page-header">
        <div class="header-icon">
            <i class="fas fa-peso-sign"></i>
        </div>
        <h1 class="header-title">Manage Menu Prices</h1>
    </div>

    <!-- Price Form -->
    <form method="POST" action="{{ route('admin.menus.prices.update') }}" class="space-y-6">
        @csrf

        <div>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Meal Time</th>
                        <th>Standard Price</th>
                        <th>Special Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($meals as $mealKey => $mealLabel)
                        <tr id="row-{{ $mealKey }}" class="price-row">
                            <td>
                                <span class="meal-time-badge">
                                    {{ $mealLabel }}
                                </span>
                            </td>
                            <td>
                                <div class="price-input-container">
                                    <span class="currency-symbol">₱</span>
                                    <input type="number"
                                           name="prices[standard][{{ $mealKey }}]"
                                           value="{{ $priceMap['standard'][$mealKey] ?? 0 }}"
                                           step="0.01"
                                           min="0"
                                           class="price-input standard-price"
                                           data-meal="{{ $mealKey }}"
                                           data-type="standard"
                                           required>
                                </div>
                            </td>
                            <td>
                                <div class="price-input-container">
                                    <span class="currency-symbol">₱</span>
                                    <input type="number"
                                           name="prices[special][{{ $mealKey }}]"
                                           value="{{ $priceMap['special'][$mealKey] ?? 0 }}"
                                           step="0.01"
                                           min="0"
                                           class="price-input special-price"
                                           data-meal="{{ $mealKey }}"
                                           data-type="special"
                                           required>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.menus.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </a>
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Prices
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectedType = '{{ $selectedType }}';
    const selectedMeal = '{{ $selectedMeal }}';

    if (selectedType && selectedMeal) {
        // Find the row for the selected meal
        const row = document.getElementById(`row-${selectedMeal}`);
        if (row) {
            // Highlight the row with animation
            row.classList.add('highlight-row');

            // Find and focus the specific input for the selected type
            const targetInput = document.querySelector(`input[data-meal="${selectedMeal}"][data-type="${selectedType}"]`);
            
            if (targetInput) {
                // Focus on the specific input after a short delay
                setTimeout(() => {
                    targetInput.focus();
                    targetInput.select();
                    targetInput.classList.add('highlight-input');
                }, 500);

                // Remove input highlight after 3 seconds
                setTimeout(() => {
                    targetInput.classList.remove('highlight-input');
                }, 3500);
            }
        }
    }

    // Add real-time validation for price inputs
    const priceInputs = document.querySelectorAll('.price-input');
    priceInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value < 0) {
                this.value = 0;
            }
            if (value > 10000) {
                this.value = 10000;
            }
        });

        input.addEventListener('blur', function() {
            if (this.value === '') {
                this.value = 0;
            }
            // Format to 2 decimal places
            this.value = parseFloat(this.value).toFixed(2);
        });
    });

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            document.querySelector('button[type="submit"]').click();
        }
    });
});
</script>
@endsection