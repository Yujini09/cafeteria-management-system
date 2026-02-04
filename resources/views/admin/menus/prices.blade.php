@extends('layouts.sidebar')
@section('page-title','Manage Menu Prices')

@section('content')
<style>
/* Modern Card Styles */
.modern-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--neutral-100);
    overflow: hidden;
}

/* Input Styles */
.price-input-container {
    position: relative;
}

.price-input-container .price-input {
    width: 100%;
    padding: 0.75rem 0.75rem 0.75rem 3rem !important;
    border: 1px solid var(--neutral-300);
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background: white;
    box-sizing: border-box;
}

.price-input-container .price-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.1);
}

.currency-symbol {
    position: absolute;
    left: 0.9rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--neutral-600);
    font-weight: 600;
    font-size: 0.875rem;
    pointer-events: none;
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

/* Highlight Animation */
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
}

</style>

<div class="modern-card menu-card admin-page-shell p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0">
    <x-success-modal name="menu-prices-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
        <p class="text-sm text-admin-neutral-600">Menu prices updated successfully.</p>
    </x-success-modal>

    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-peso-sign"></i>
            </div>
            <div class="header-text">
                <h1 class="header-title">Manage Menu Prices</h1>
            </div>
        </div>
    </div>

    <!-- Price Form -->
    <form method="POST" action="{{ route('admin.menus.prices.update') }}" class="space-y-6">
        @csrf

        <div class="overflow-x-auto">
            <table class="modern-table min-w-[520px]">
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

        <div class="flex flex-col gap-3 pt-6 border-t border-gray-100 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.menus.index') }}" wire:navigate class="btn-secondary">
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

@if(session('menu_success') && \Illuminate\Support\Str::contains(session('menu_success'), 'Menu prices updated'))
<script>
document.addEventListener('livewire:navigated', function () {
    requestAnimationFrame(() => {
        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'menu-prices-success' }));
    });
});
</script>
@endif

<script>
document.addEventListener('livewire:navigated', function() {
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
