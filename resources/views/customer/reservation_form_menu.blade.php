@extends('layouts.app')

@section('title', 'Menu Selection - CLSU RET Cafeteria')

@php
    $mealTimes = [
        'breakfast' => 'Breakfast',
        'am_snacks' => 'A.M. Snacks',
        'lunch' => 'Lunch',
        'pm_snacks' => 'P.M. Snacks',
        'dinner' => 'Dinner',
    ];
    $categories = [
        'standard' => 'Standard Menu',
        'special' => 'Special Menu',
    ];

    $defaultStandardPrice = 150;
    $defaultSpecialPrice = 200;

    $startDate = $reservationData['start_date'] ?? null;
    $endDate = $reservationData['end_date'] ?? null;
    $dayTimes = !empty($reservationData['day_times'])
        ? (json_decode($reservationData['day_times'], true) ?? [])
        : [];

    $numberOfDays = 0;
    if ($startDate && $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $numberOfDays = $end->diff($start)->days + 1;
    }

    $menuRateMap = [];
    foreach ($mealTimes as $mealKey => $mealLabel) {
        $menuRateMap[$mealKey] = [
            'standard' => isset($menuPrices['standard'][$mealKey][0]) && is_numeric($menuPrices['standard'][$mealKey][0]->price)
                ? (float) $menuPrices['standard'][$mealKey][0]->price
                : (float) $defaultStandardPrice,
            'special' => isset($menuPrices['special'][$mealKey][0]) && is_numeric($menuPrices['special'][$mealKey][0]->price)
                ? (float) $menuPrices['special'][$mealKey][0]->price
                : (float) $defaultSpecialPrice,
        ];
    }

    $menuLibrary = [];
    foreach ($mealTimes as $mealKey => $mealLabel) {
        foreach ($categories as $categoryKey => $categoryLabel) {
            $menuCollection = $menus[$mealKey][$categoryKey] ?? collect();

            foreach ($menuCollection as $menu) {
                $items = collect($menu->items ?? []);
                $featuredItem = $items->firstWhere('type', 'food') ?? $items->first();
                $menuLabel = trim((string) ($menu->name ?? ''));

                if (preg_match('/(Menu\s*\d+|Day\s*\d+)/i', $menuLabel, $matches)) {
                    $menuLabel = trim($matches[1]);
                }

                $menuLibrary[] = [
                    'id' => (int) $menu->id,
                    'name' => (string) ($menu->name ?? ''),
                    'menu_label' => $menuLabel,
                    'featured_title' => (string) ($featuredItem->name ?? 'Featured Menu'),
                    'meal_time' => $mealKey,
                    'meal_label' => $mealLabel,
                    'category' => $categoryKey,
                    'category_label' => $categoryLabel,
                    'price' => $menuRateMap[$mealKey][$categoryKey],
                    'description' => (string) ($menu->description ?? ''),
                    'items' => $items->map(function ($item) {
                        return [
                            'name' => (string) ($item->name ?? ''),
                            'type' => (string) ($item->type ?? ''),
                        ];
                    })->values()->all(),
                    'searchable' => strtolower(trim(implode(' ', array_filter([
                        (string) ($menu->name ?? ''),
                        $mealLabel,
                        $categoryLabel,
                        $items->pluck('name')->implode(' '),
                    ])))),
                ];
            }
        }
    }
@endphp

@section('styles')
    .menu_selection-hero-bg {
        background-image: url('/images/banner1.jpg');
        background-size: cover;
        background-position: top;
    }
    .card {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .bg-clsu-green {
        background-color: #1a5e3d;
    }
    .hover\:bg-green-700:hover {
        background-color: #154d32;
    }
    .focus\:ring-clsu-green:focus {
        --tw-ring-color: #1a5e3d;
    }
    .text-ret-dark {
        color: #1a5e3d;
    }
    .border-ret-dark {
        border-color: #1a5e3d;
    }

    .day-nav-container {
        background: linear-gradient(135deg, #1a5e3d 0%, #2d7a52 100%);
        padding: 16px 18px;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    .day-tabs {
        display: flex;
        justify-content: center;
        align-items: stretch;
        gap: 8px;
        flex-wrap: wrap;
    }
    .day-tab {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 108px;
        padding: 11px 16px;
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        border: 2px solid transparent;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.25s ease;
        text-align: center;
    }
    .day-tab:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-1px);
    }
    .day-tab.active {
        background: #ffffff;
        color: #1a5e3d;
        border-color: #1a5e3d;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
    }
    .day-tab.has-selection:not(.active) {
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.22);
    }
    .day-tab-title {
        display: block;
        font-size: 0.95rem;
    }
    .day-info {
        color: #ffffff;
        font-size: 0.92rem;
        margin-top: 10px;
        text-align: center;
    }

    .menu-header-guidance {
        padding: 0;
        border-radius: 0;
        background: transparent;
        border: 0;
        box-shadow: none;
    }
    .menu-guidance-accordion {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }
    .menu-guidance-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        width: 100%;
        padding: 16px 18px;
        background: transparent;
        border: 0;
        cursor: pointer;
        text-align: left;
    }
    .menu-guidance-trigger-main {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .menu-header-guidance-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }
    .menu-header-guidance-icon {
        display: inline-flex;
        width: 24px;
        height: 24px;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: rgba(22, 101, 52, 0.12);
        color: #166534;
        flex-shrink: 0;
        font-size: 0.95rem;
        font-weight: 800;
        text-transform: lowercase;
    }
    .menu-header-guidance-title {
        color: #14532d;
        font-size: 1rem;
        font-weight: 700;
        line-height: 1.25;
    }
    .menu-guidance-panel {
        padding: 0 18px 18px;
        border-top: 1px solid #e5e7eb;
    }
    .menu-header-guidance-subtext {
        margin: 0 0 10px;
        color: #4b5563;
        font-size: 0.82rem;
        line-height: 1.4;
    }
    .menu-header-guidance-list {
        margin: 8px 0 0;
        padding-left: 0;
        color: #4b5563;
        font-size: 0.84rem;
        line-height: 1.6;
        list-style: none;
    }
    .menu-header-guidance-list li {
        position: relative;
        padding-left: 1rem;
    }
    .menu-header-guidance-list li::before {
        content: "\2022";
        position: absolute;
        left: 0;
        top: 0;
        color: #166534;
        font-weight: 700;
    }
    .menu-header-guidance-list li + li {
        margin-top: 8px;
    }
    .menu-header-guidance-list strong {
        color: #14532d;
        font-weight: 700;
    }
    .menu-guidance-chevron {
        width: 18px;
        height: 18px;
        color: #6b7280;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .menu-guidance-accordion.is-open .menu-guidance-chevron {
        transform: rotate(180deg);
    }

    .green-menu-list {
        background: linear-gradient(135deg, #f0f9f0 0%, #e8f5e8 100%);
        border: 2px solid #1a5e3d;
        border-radius: 12px;
        min-height: 720px;
        width: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .green-menu-header {
        background: #ffffff;
        color: inherit;
        padding: 18px;
        border-bottom: 1px solid #e5e7eb;
        flex-shrink: 0;
    }
    .green-menu-header-layout {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .menu-search-container {
        padding: 16px 18px;
        background: #f8fff9;
        border-bottom: 1px solid #d8ead9;
        flex-shrink: 0;
    }
    .menu-tabs-container {
        display: flex;
        gap: 8px;
        padding: 6px;
        background: #e8f5e8;
        border-radius: 10px;
    }
    .menu-tab {
        padding: 9px 12px;
        cursor: pointer;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        color: #374151;
        font-size: 0.9rem;
        flex: 1 1 0;
        text-align: center;
        background: transparent;
    }
    .menu-tab.active {
        background-color: #1a5e3d;
        color: #ffffff;
        border-color: #1a5e3d;
        box-shadow: 0 2px 4px rgba(26, 94, 61, 0.35);
    }
    .menu-tab:not(.active):hover {
        background-color: #ffffff;
    }

    .meal-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .meal-type-btn {
        padding: 10px 12px;
        cursor: pointer;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
        border: 1px solid #d1d5db;
        color: #4b5563;
        font-size: 0.8rem;
        background-color: #ffffff;
        text-align: left;
        flex: 1 1 110px;
        min-width: 110px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        gap: 2px;
    }
    .meal-type-btn.active {
        background-color: #1a5e3d;
        color: #ffffff;
        border-color: #1a5e3d;
        box-shadow: 0 1px 2px rgba(26, 94, 61, 0.25);
    }
    .meal-type-btn.has-selection:not(.active) {
        background-color: #f0fdf4;
        border-color: #86efac;
    }
    .meal-type-btn:not(.active):hover {
        background-color: #f3f4f6;
        border-color: #9ca3af;
    }
    .meal-type-btn-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .meal-type-btn-status {
        display: block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 0.68rem;
        line-height: 1.2;
        color: #6b7280;
    }
    .meal-type-btn.active .meal-type-btn-status {
        color: rgba(255, 255, 255, 0.82);
    }

    .menu-preview-container {
        flex: 0 0 auto;
        min-height: auto;
        display: block;
    }
    .menu-content-area {
        padding: 18px;
        overflow: visible;
        flex: 0 0 auto;
    }

    .filter-caption {
        color: #4b5563;
        font-size: 0.86rem;
        font-weight: 600;
        margin-bottom: 12px;
    }
    .menu-list-item {
        border-left: 4px solid #1a5e3d;
        background: #ffffff;
        transition: all 0.25s ease;
    }
    .menu-browser-item {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        cursor: pointer;
    }
    .menu-browser-item.is-selected {
        border-left-color: #166534;
        box-shadow: 0 16px 28px rgba(21, 128, 61, 0.18), inset 0 0 0 1px rgba(22, 101, 52, 0.08);
        background: linear-gradient(180deg, #f3fff7 0%, #dcfce7 100%);
    }
    .menu-browser-item.is-expanded {
        box-shadow: 0 16px 28px rgba(26, 94, 61, 0.14);
    }
    .compact-menu-item {
        padding: 16px;
        border-radius: 12px;
    }
    .menu-card-tag {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 8px;
        border-radius: 999px;
        background: #ecfdf3;
        color: #166534;
        font-size: 0.74rem;
        font-weight: 700;
    }
    .menu-card-tag-premium {
        background: #fff7d6;
        color: #92400e;
    }
    .price-badge {
        background-color: #1a5e3d;
        color: #ffffff;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .menu-card-side-column {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: space-between;
        gap: 0.9rem;
        align-self: stretch;
        flex: 0 0 auto;
    }
    .selection-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(26, 94, 61, 0.1);
        color: #166534;
        font-size: 0.74rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .selection-chip-muted {
        background: #eef2ff;
        color: #475569;
    }
    .menu-card-items-list {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        margin-top: 0.9rem;
        font-size: 0.875rem;
        line-height: 1.2;
        color: #374151;
    }
    .menu-card-items-entry {
        display: flex;
        align-items: flex-start;
        gap: 0.4rem;
        line-height: 1.2;
        min-width: 0;
    }
    .menu-card-items-bullet {
        color: #16a34a;
        margin-top: 0.1rem;
        line-height: 1;
        flex: 0 0 auto;
    }
    .menu-card-items-empty {
        color: #6b7280;
        line-height: 1.2;
    }
    .menu-card-inline-check {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.8rem;
        height: 1.8rem;
        border-radius: 999px;
        background: rgba(22, 163, 74, 0.14);
        color: #15803d;
        border: 1px solid rgba(22, 163, 74, 0.22);
        flex: 0 0 auto;
    }
    .menu-card-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: flex-end;
        padding: 16px;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.04) 5%, rgba(15, 23, 42, 0.62) 100%);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }
    .menu-card-overlay-panel {
        width: 100%;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.98);
        padding: 14px 16px;
        box-shadow: 0 18px 28px rgba(15, 23, 42, 0.16);
        transform: translateY(14px);
        transition: transform 0.22s ease;
    }
    .menu-browser-item:focus-within .menu-card-overlay,
    .menu-browser-item.is-expanded .menu-card-overlay {
        opacity: 1;
        pointer-events: auto;
    }
    .menu-browser-item:focus-within .menu-card-overlay-panel,
    .menu-browser-item.is-expanded .menu-card-overlay-panel {
        transform: translateY(0);
    }
    @media (hover: hover) and (pointer: fine) {
        .menu-browser-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
        }
        .menu-browser-item:hover .menu-card-overlay {
            opacity: 1;
            pointer-events: auto;
        }
        .menu-browser-item:hover .menu-card-overlay-panel {
            transform: translateY(0);
        }
    }
    .menu-browser-footer {
        margin: 0;
        padding: 16px 18px;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        border-radius: 0;
        box-shadow: none;
        flex-shrink: 0;
    }
    .no-results {
        text-align: center;
        padding: 28px 18px;
        color: #6b7280;
        font-style: italic;
    }

    .selected-order-item {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px;
        background: #ffffff;
    }
    .selected-order-item-shell {
        display: block;
    }
    .selected-order-item-content {
        min-width: 0;
    }
    .selected-order-item-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .selected-order-item-heading {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }
    .selected-order-item-meal {
        color: #14532d;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.1;
    }
    .selected-order-item-price {
        flex-shrink: 0;
        color: #1f2937;
        font-size: 0.85rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .selected-order-item-detail {
        margin-top: 6px;
        color: #374151;
        font-size: 0.82rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .selected-order-item-list {
        margin-top: 6px;
        display: grid;
        gap: 3px;
    }
    .selected-order-item-list-entry {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        color: #4b5563;
        font-size: 0.84rem;
        line-height: 1.2;
    }
    .selected-order-item-list-bullet {
        color: #15803d;
        font-weight: 700;
        line-height: 1.2;
    }
    .selected-order-item-pax-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 8px;
        flex-wrap: wrap;
    }
    .selected-order-item-pax {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .selected-order-item-remove {
        display: inline-flex;
        width: 36px;
        height: 36px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #fef2f2;
        color: #dc2626;
        transition: background-color 0.15s ease;
    }
    .selected-order-item-remove:hover {
        background: #fee2e2;
    }
    .selected-order-item-pax-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #6b7280;
        line-height: 1.1;
    }
    .selected-order-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 22px 16px;
        text-align: center;
        color: #6b7280;
        background: #f8fafc;
    }
    .summary-qty-group {
        display: inline-flex;
        align-items: center;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        overflow: hidden;
        background: #ffffff;
    }
    .summary-qty-btn {
        width: 34px;
        height: 34px;
        border: 0;
        background: #f3f4f6;
        color: #374151;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .summary-qty-btn:hover {
        background: #e5e7eb;
    }
    .summary-qty-input {
        width: 58px;
        height: 34px;
        border: 0;
        border-left: 1px solid #d1d5db;
        border-right: 1px solid #d1d5db;
        text-align: center;
        font-size: 0.92rem;
        font-weight: 700;
        color: #111827;
    }
    .summary-qty-input:focus {
        outline: none;
        background: #f8fff9;
    }
    .summary-qty-error {
        display: none;
        margin-top: 10px;
        color: #dc2626;
        font-size: 0.76rem;
        font-weight: 600;
    }
    .summary-qty-error.visible {
        display: block;
    }
    .summary-apply-all-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 12px;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid rgba(26, 94, 61, 0.16);
        background: #f0f9f0;
        color: #166534;
        font-size: 0.78rem;
        font-weight: 700;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }
    .summary-apply-all-btn:hover {
        background: #e5f6e6;
        border-color: rgba(26, 94, 61, 0.28);
    }
    .order-summary-day-label {
        color: #374151;
        font-weight: 700;
    }
    .order-summary-day-value {
        color: #111827;
        font-weight: 700;
    }

    .navigation-button[disabled] {
        opacity: 0.55;
        cursor: not-allowed;
    }
    .menu-navigation-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }
    .menu-nav-action {
        width: 220px;
        min-height: 56px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 1.5rem;
        text-align: center;
    }

    @media (max-width: 640px) {
        .menu-navigation-actions {
            gap: 0.5rem;
        }
        .menu-nav-action {
            width: calc(50% - 0.25rem);
            max-width: 168px;
            min-height: 52px;
            padding: 0 1rem;
        }
    }

    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 20px;
        border-radius: 10px;
        color: #ffffff;
        font-weight: 600;
        z-index: 10000;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.18);
        transform: translateX(150%);
        transition: transform 0.3s ease-in-out;
        max-width: 420px;
    }
    .notification.show {
        transform: translateX(0);
    }
    .notification.success {
        background-color: #10b981;
        border-left: 4px solid #059669;
    }
    .notification.error {
        background-color: #ef4444;
        border-left: 4px solid #dc2626;
    }
    .notification-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .notification-icon {
        flex-shrink: 0;
    }

    .confirmation-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .confirmation-modal-content {
        background: #ffffff;
        padding: 28px;
        border-radius: 14px;
        max-width: 760px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }
    .summary-item {
        border-bottom: 1px solid #e5e7eb;
        padding: 12px 0;
    }
    .summary-item:last-child {
        border-bottom: none;
    }
    .total-section {
        background: #f8fafc;
        border-radius: 10px;
        padding: 18px;
        margin-top: 20px;
    }

    @media (min-width: 1024px) {
        .green-menu-list {
            min-height: 0;
            height: auto;
        }
        .green-menu-header-layout {
            display: block;
        }
    }

    @media (max-width: 1023px) {
        .green-menu-list {
            min-height: 0;
        }
    }

    @media (max-width: 640px) {
        .day-tabs {
            flex-wrap: nowrap;
            gap: 6px;
        }
        .day-tab {
            min-width: 0;
            flex: 1 1 0;
            padding: 10px 8px;
        }
        .day-tab-title {
            font-size: 0.82rem;
        }
        .menu-tabs-container {
            flex-direction: row;
            flex-wrap: nowrap;
        }
        .meal-type-btn {
            min-width: calc(50% - 8px);
        }
        .confirmation-modal-content {
            padding: 22px 18px;
        }
    }
@endsection

@section('scripts')
<script>
    let currentDay = 1;
    let totalDays = 1;
    let activeCategory = 'standard';
    let activeMealType = 'breakfast';
    let expandedMenuId = null;
    let selectionState = {};

    const mealTimes = @json($mealTimes);
    const mealOrder = Object.keys(mealTimes);
    const menuPrices = @json($menuRateMap);
    const menuLibrary = @json($menuLibrary);
    const reservationDayTimes = @json($dayTimes);
    const initialReservations = @json(old('reservations', []));
    const menuIndex = menuLibrary.reduce((accumulator, menu) => {
        accumulator[String(menu.id)] = menu;
        return accumulator;
    }, {});
    const maxPax = 1000;

    window.closeConfirmationModal = closeConfirmationModal;
    window.submitReservation = submitReservation;
    window.redirectToReservationDetails = redirectToReservationDetails;
    window.closeSuccessModal = closeSuccessModal;

    function closeConfirmationModal() {
        const modal = document.getElementById('confirmationModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function closeSuccessModal() {
        const modal = document.getElementById('successModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function redirectToReservationDetails() {
        closeSuccessModal();
        window.location.href = '{{ route('reservation_details') }}';
    }

    function submitReservation(triggerButton = null) {
        syncHiddenInputs();

        if (triggerButton && window.cmsActionButtons) {
            const started = window.cmsActionButtons.start(triggerButton, triggerButton.dataset.loadingText || 'Submitting Reservation...');
            if (!started) {
                return;
            }
        }

        const confirmButton = document.getElementById('confirm-button');
        if (confirmButton && window.cmsActionButtons) {
            window.cmsActionButtons.start(confirmButton, confirmButton.dataset.loadingText || 'Submitting...');
        }

        closeConfirmationModal();
        document.getElementById('reservation-form').submit();
    }

    function showNotification(message, type = 'success', duration = 4500) {
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;

        const icon = type === 'success'
            ? '<svg class="notification-icon w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            : '<svg class="notification-icon w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

        notification.innerHTML = `
            <div class="notification-content">
                ${icon}
                <span>${escapeHtml(message)}</span>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 50);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, duration);
    }

    function escapeHtml(value) {
        const text = String(value ?? '');
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getPesoSign() {
        return String.fromCharCode(0x20B1);
    }

    function formatCurrency(value) {
        return `${getPesoSign()}${Number(value || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })}`;
    }

    function getDayLabel(day) {
        return `Day ${day}`;
    }

    function createLocalDate(dateString) {
        const parts = String(dateString || '').split('-').map(Number);
        if (parts.length !== 3 || parts.some(Number.isNaN)) {
            return new Date();
        }

        return new Date(parts[0], parts[1] - 1, parts[2]);
    }

    function toIsoDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function formatShortDate(date) {
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        });
    }

    function formatLongDate(date) {
        return date.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    function formatTimeForDisplay(timeValue) {
        if (!timeValue) {
            return '';
        }

        const match = String(timeValue).trim().match(/^(\d{1,2}):(\d{2})(?::\d{2})?$/);
        if (!match) {
            return String(timeValue);
        }

        const hours = parseInt(match[1], 10);
        const minutes = match[2];

        if (Number.isNaN(hours) || hours < 0 || hours > 23) {
            return String(timeValue);
        }

        const meridiem = hours >= 12 ? 'PM' : 'AM';
        const displayHours = hours % 12 || 12;
        return `${displayHours}:${minutes} ${meridiem}`;
    }

    function ensureDaySelectionState(day) {
        const key = String(day);
        if (!selectionState[key]) {
            selectionState[key] = {};
        }
        return selectionState[key];
    }

    function getSelection(day, mealTime) {
        return ensureDaySelectionState(day)[mealTime] || null;
    }

    function getSelectionsForDay(day) {
        const selections = ensureDaySelectionState(day);

        return mealOrder
            .map((mealTime) => {
                const selection = selections[mealTime];
                if (!selection) {
                    return null;
                }

                const menu = menuIndex[String(selection.menuId)];
                if (!menu) {
                    return null;
                }

                return {
                    mealTime,
                    menu,
                    category: selection.category || menu.category,
                    qty: Number(selection.qty) || 0
                };
            })
            .filter(Boolean);
    }

    function dayHasSelectedMenu(day) {
        return getSelectionsForDay(day).length > 0;
    }

    function dayHasInvalidQuantities(day) {
        return getSelectionsForDay(day).some((selection) => selection.qty < 10);
    }

    function dayIsReady(day) {
        return dayHasSelectedMenu(day) && !dayHasInvalidQuantities(day);
    }

    function everySelectedDayHasValidMenu() {
        if (totalDays < 1) {
            return false;
        }

        for (let day = 1; day <= totalDays; day++) {
            if (!dayIsReady(day)) {
                return false;
            }
        }

        return true;
    }

    function syncHiddenInputs() {
        const container = document.getElementById('reservation-hidden-inputs');
        if (!container) {
            return;
        }

        container.innerHTML = '';

        for (let day = 1; day <= totalDays; day++) {
            const selections = getSelectionsForDay(day);

            selections.forEach((selection) => {
                appendHiddenInput(container, `reservations[${day}][${selection.mealTime}][category]`, selection.category);
                appendHiddenInput(container, `reservations[${day}][${selection.mealTime}][menu]`, selection.menu.id);
                appendHiddenInput(container, `reservations[${day}][${selection.mealTime}][qty]`, selection.qty);
            });
        }
    }

    function appendHiddenInput(container, name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        container.appendChild(input);
    }

    function hydrateInitialSelections() {
        selectionState = {};

        for (let day = 1; day <= totalDays; day++) {
            ensureDaySelectionState(day);
        }

        Object.entries(initialReservations || {}).forEach(([dayKey, meals]) => {
            const day = parseInt(dayKey, 10);

            if (Number.isNaN(day) || !meals || typeof meals !== 'object') {
                return;
            }

            Object.entries(meals).forEach(([mealTime, data]) => {
                const menuId = String(data.menu || '');
                const menu = menuIndex[menuId];
                const qty = Math.min(Math.max(parseInt(data.qty, 10) || 0, 0), maxPax);

                if (!menu || qty <= 0) {
                    return;
                }

                ensureDaySelectionState(day)[mealTime] = {
                    menuId,
                    category: data.category || menu.category,
                    qty: qty < 10 ? 10 : qty
                };
            });
        });
    }

    function initializeDayTabs() {
        const tabsContainer = document.getElementById('day-tabs');
        const startDate = document.getElementById('start-date').value;
        const start = createLocalDate(startDate);
        tabsContainer.innerHTML = '';

        for (let day = 1; day <= totalDays; day++) {
            const date = new Date(start);
            date.setDate(start.getDate() + day - 1);
            const dateKey = toIsoDate(date);

            const tab = document.createElement('button');
            tab.type = 'button';
            tab.className = `day-tab ${day === currentDay ? 'active' : ''}`;
            tab.dataset.day = day;
            tab.dataset.date = dateKey;

            const dayTime = reservationDayTimes[dateKey] || {};
            tab.dataset.startTime = dayTime.start_time || '';
            tab.dataset.endTime = dayTime.end_time || '';

            tab.innerHTML = `<span class="day-tab-title">${getDayLabel(day)}</span>`;

            tabsContainer.appendChild(tab);
        }
    }

    function updateDayTabsSelectionState() {
        document.querySelectorAll('.day-tab').forEach((tab) => {
            const day = parseInt(tab.dataset.day, 10);
            const count = getSelectionsForDay(day).length;

            tab.classList.toggle('has-selection', count > 0);
        });
    }

    function updateDayInfo(day) {
        const dayInfo = document.getElementById('day-info');
        const activeTab = document.querySelector(`.day-tab[data-day="${day}"]`);
        if (!dayInfo || !activeTab) {
            return;
        }

        const date = createLocalDate(activeTab.dataset.date);
        let info = `Currently viewing: <strong>${formatLongDate(date)}</strong>`;

        if (activeTab.dataset.startTime && activeTab.dataset.endTime) {
            info += ` | Time: ${formatTimeForDisplay(activeTab.dataset.startTime)} to ${formatTimeForDisplay(activeTab.dataset.endTime)}`;
        }

        dayInfo.innerHTML = info;
    }

    function switchToDay(day) {
        if (day < 1 || day > totalDays) {
            return;
        }

        currentDay = day;
        document.querySelectorAll('.day-tab').forEach((tab) => {
            tab.classList.toggle('active', parseInt(tab.dataset.day, 10) === day);
        });

        updateDayInfo(day);
        renderAll();
    }

    function updateDayContext() {
        const label = getDayLabel(currentDay);
    }

    function updateMealFilterSelectionState() {
        document.querySelectorAll('.meal-type-btn').forEach((button) => {
            const mealTime = button.dataset.mealType;
            const selection = getSelection(currentDay, mealTime);
            const status = button.querySelector('[data-meal-filter-status]');
            const selectedMenu = selection ? menuIndex[String(selection.menuId)] : null;

            button.classList.toggle('has-selection', Boolean(selection));

            if (status) {
                status.textContent = selectedMenu
                    ? `${selectedMenu.menu_label || 'Selected'} selected`
                    : 'Not selected';
            }
        });
    }

    function updateMenuPricePreview() {
    }

    function renderMenuGrid() {
        updateMenuPricePreview();

            const grid = document.getElementById('menu-browser-grid');
            const emptyState = document.getElementById('menu-empty-state');

            const filteredMenus = menuLibrary.filter((menu) => {
                if (menu.category !== activeCategory) {
                return false;
            }

                if (menu.meal_time !== activeMealType) {
                    return false;
                }
                return true;
            });

        if (!filteredMenus.length) {
            grid.innerHTML = '';
            emptyState.classList.remove('hidden');
            emptyState.textContent = `No ${activeCategory} menus found for ${mealTimes[activeMealType]}.`;
            return;
        }

        emptyState.classList.add('hidden');

        grid.innerHTML = filteredMenus.map((menu) => {
            const selection = getSelection(currentDay, menu.meal_time);
            const isSelected = selection && String(selection.menuId) === String(menu.id);
            const isExpanded = String(expandedMenuId) === String(menu.id);
            const menuItems = Array.isArray(menu.items) ? menu.items : [];
            const renderMenuItemEntry = (item) => `
                <div class="menu-card-items-entry">
                    <span class="menu-card-items-bullet">&bull;</span>
                    <span>${escapeHtml(item.name)}</span>
                </div>
            `;
            const itemMarkup = menuItems.length
                ? menuItems.map(renderMenuItemEntry).join('')
                : '<div class="menu-card-items-empty">No items listed yet.</div>';

            return `
                <article class="menu-list-item compact-menu-item menu-browser-item ${isSelected ? 'is-selected' : ''} ${isExpanded ? 'is-expanded' : ''}" data-menu-id="${menu.id}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="menu-card-tag">${escapeHtml(menu.meal_label)}</span>
                                <span class="menu-card-tag ${menu.category === 'special' ? 'menu-card-tag-premium' : ''}">${escapeHtml(menu.category === 'special' ? 'Special' : 'Standard')}</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-green-900">${escapeHtml(menu.menu_label)}</h3>
                            </div>
                            <div class="menu-card-items-list">
                                ${itemMarkup}
                            </div>
                        </div>
                        <div class="menu-card-side-column">
                            <div class="price-badge">${formatCurrency(menu.price)} / pax</div>
                            ${isSelected
                                ? `<span class="menu-card-inline-check" title="Added to order" aria-label="Added to order">
                                       <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                       </svg>
                                   </span>`
                                : ''}
                        </div>
                    </div>

                    <div class="menu-card-overlay">
                        <div class="menu-card-overlay-panel">
                            ${isSelected
                                ? `<button type="button" class="px-4 py-2 bg-white text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition text-sm font-semibold" data-remove-selection="${menu.meal_time}">
                                       Remove
                                   </button>`
                                : `<button type="button" class="px-4 py-2 bg-clsu-green text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold" data-add-menu="${menu.id}">
                                       Add to Order
                                   </button>`}
                        </div>
                    </div>
                </article>
            `;
        }).join('');
    }

    function renderSelectedOrderPanel() {
        const list = document.getElementById('selected-order-list');
        const emptyState = document.getElementById('selected-order-empty');
        const countChip = document.getElementById('current-day-selection-count');
        const selections = getSelectionsForDay(currentDay);

        countChip.textContent = `${selections.length} selected`;

        if (!selections.length) {
            list.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');

        list.innerHTML = selections.map((selection, index) => {
            const canApplyToAll = index === 0 && selections.length > 1 && selection.qty > 0;
            const menuTypeLabel = selection.menu.category === 'special' ? 'Special' : 'Standard';
            const selectedItemsMarkup = Array.isArray(selection.menu.items) && selection.menu.items.length
                ? selection.menu.items.map((item) => `
                    <div class="selected-order-item-list-entry">
                        <span class="selected-order-item-list-bullet">&bull;</span>
                        <span>${escapeHtml(item.name)}</span>
                    </div>
                `).join('')
                : '<div class="text-sm text-gray-500">No items listed yet.</div>';
            return `
                <div class="selected-order-item" data-selected-meal="${selection.mealTime}">
                    <div class="selected-order-item-shell">
                        <div class="selected-order-item-content">
                            <div class="selected-order-item-top">
                                <div class="selected-order-item-heading">
                                    <p class="selected-order-item-meal">${escapeHtml(selection.menu.meal_label)}</p>
                                    <p class="selected-order-item-price">${formatCurrency(selection.menu.price)} / pax</p>
                                </div>
                                <button type="button" class="selected-order-item-remove" data-remove-selection="${selection.mealTime}" title="Remove">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="selected-order-item-detail">
                                ${escapeHtml(menuTypeLabel)} - ${escapeHtml(selection.menu.menu_label)}
                            </div>
                            <div class="selected-order-item-list">
                                ${selectedItemsMarkup}
                            </div>
                            <div class="selected-order-item-pax-row">
                                <div class="selected-order-item-pax">
                                    <span class="selected-order-item-pax-label"># of Guests</span>
                                    <div class="summary-qty-group">
                                        <button type="button" class="summary-qty-btn" data-qty-action="decrement" data-meal-time="${selection.mealTime}">-</button>
                                        <input
                                            type="number"
                                            min="0"
                                            max="${maxPax}"
                                            class="summary-qty-input"
                                            data-summary-qty="${selection.mealTime}"
                                            value="${selection.qty}"
                                        >
                                        <button type="button" class="summary-qty-btn" data-qty-action="increment" data-meal-time="${selection.mealTime}">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="summary-qty-error ${selection.qty > 0 && selection.qty < 10 ? 'visible' : ''}" data-qty-error="${selection.mealTime}">
                        Minimum 10 pax per selected menu.
                    </p>
                    ${canApplyToAll
                        ? `<button type="button" class="summary-apply-all-btn" data-apply-qty-all="${selection.mealTime}">
                               Apply quantity to all ordered meals
                           </button>`
                        : ''}
                </div>
            `;
        }).join('');
    }

    function updateSummaryMetrics() {
        let estimatedTotal = 0;
        const dayTotals = [];

        for (let day = 1; day <= totalDays; day++) {
            const selections = getSelectionsForDay(day);
            let dayTotal = 0;

            selections.forEach((selection) => {
                dayTotal += selection.qty * Number(selection.menu.price || 0);
            });

            estimatedTotal += dayTotal;
            dayTotals.push({ day, total: dayTotal });
        }

        const dayTotalsContainer = document.getElementById('order-summary-day-totals');
        if (dayTotalsContainer) {
            dayTotalsContainer.innerHTML = dayTotals.map(({ day, total }) => `
                <div class="flex justify-between text-sm">
                    <span class="order-summary-day-label">${escapeHtml(getDayLabel(day))} subtotal:</span>
                    <span class="order-summary-day-value">${formatCurrency(total)}</span>
                </div>
            `).join('');
        }

        document.getElementById('estimated-total').textContent = formatCurrency(estimatedTotal);
    }

    function updateNavigationButtons() {
        const backButton = document.getElementById('back-button');
        const nextDayButton = document.getElementById('next-day-button');
        const confirmButton = document.getElementById('confirm-button');
        const currentDayReady = dayIsReady(currentDay);
        const allDaysReady = everySelectedDayHasValidMenu();
        const nextDayNumber = currentDay + 1;

        backButton.classList.toggle('hidden', currentDay === 1);

        if (currentDay < totalDays) {
            nextDayButton.classList.remove('hidden');
            confirmButton.classList.add('hidden');
            nextDayButton.textContent = `${getDayLabel(nextDayNumber)} >`;
            nextDayButton.disabled = !currentDayReady;
            nextDayButton.setAttribute('aria-disabled', String(!currentDayReady));
            nextDayButton.title = currentDayReady
                ? ''
                : 'Select at least one menu with at least 10 pax before moving to the next day.';
        } else {
            nextDayButton.classList.add('hidden');
            confirmButton.classList.remove('hidden');
            confirmButton.disabled = !allDaysReady;
            confirmButton.setAttribute('aria-disabled', String(!allDaysReady));
            confirmButton.title = allDaysReady
                ? ''
                : 'Finish selecting at least one valid menu for every day before confirming.';
        }
    }

    function renderAll() {
        updateDayContext();
        updateDayTabsSelectionState();
        updateMealFilterSelectionState();
        renderMenuGrid();
        renderSelectedOrderPanel();
        updateSummaryMetrics();
        updateNavigationButtons();
        syncHiddenInputs();
    }

    function usesHoverOverlay() {
        return window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    }

    function clickStartedInsideMenuGrid(event) {
        const grid = document.getElementById('menu-browser-grid');
        if (!grid || !event) {
            return false;
        }

        if (typeof event.composedPath === 'function') {
            return event.composedPath().includes(grid);
        }

        return Boolean(event.target && event.target.closest && event.target.closest('#menu-browser-grid [data-menu-id]'));
    }

    function selectMenu(menuId) {
        const menu = menuIndex[String(menuId)];
        if (!menu) {
            return;
        }

        const nextExpandedMenuId = usesHoverOverlay() ? null : menu.id;
        const currentSelection = getSelection(currentDay, menu.meal_time);
        if (currentSelection && String(currentSelection.menuId) === String(menu.id)) {
            expandedMenuId = nextExpandedMenuId;
            renderAll();
            return;
        }

        const nextQty = currentSelection && Number(currentSelection.qty) > 0 ? Number(currentSelection.qty) : 10;
        ensureDaySelectionState(currentDay)[menu.meal_time] = {
            menuId: String(menu.id),
            category: menu.category,
            qty: nextQty
        };

        expandedMenuId = nextExpandedMenuId;
        renderAll();

        showNotification(
            currentSelection
                ? `${menu.meal_label} updated for ${getDayLabel(currentDay)}.`
                : `${menu.menu_label} added to ${getDayLabel(currentDay)}.`,
            'success'
        );
    }

    function removeSelection(day, mealTime, notify = true) {
        const daySelections = ensureDaySelectionState(day);
        if (!daySelections[mealTime]) {
            return;
        }

        delete daySelections[mealTime];

        if (activeMealType === mealTime) {
            expandedMenuId = null;
        }

        renderAll();

        if (notify) {
            showNotification(`${mealTimes[mealTime]} removed from ${getDayLabel(day)}.`, 'success');
        }
    }

    function adjustSelectionQuantity(day, mealTime, action) {
        const selection = getSelection(day, mealTime);
        if (!selection) {
            return;
        }

        let nextQty = Number(selection.qty) || 0;

        if (action === 'increment') {
            nextQty = nextQty === 0 ? 10 : Math.min(nextQty + 1, maxPax);
        } else if (action === 'decrement') {
            if (nextQty <= 10) {
                removeSelection(day, mealTime, true);
                return;
            }
            nextQty = Math.max(nextQty - 1, 0);
        }

        ensureDaySelectionState(day)[mealTime].qty = nextQty;
        renderAll();
    }

    function commitManualQuantity(day, mealTime, rawValue) {
        const selection = getSelection(day, mealTime);
        if (!selection) {
            return;
        }

        let nextQty = parseInt(rawValue, 10);

        if (Number.isNaN(nextQty) || nextQty <= 0) {
            removeSelection(day, mealTime, true);
            return;
        }

        if (nextQty > maxPax) {
            nextQty = maxPax;
        }

        if (nextQty > 0 && nextQty < 10) {
            nextQty = 10;
        }

        ensureDaySelectionState(day)[mealTime].qty = nextQty;
        renderAll();
    }

    function toggleQuantityError(mealTime, value) {
        const errorElement = document.querySelector(`[data-qty-error="${mealTime}"]`);
        if (!errorElement) {
            return;
        }

        const numericValue = parseInt(value, 10) || 0;
        errorElement.classList.toggle('visible', numericValue > 0 && numericValue < 10);
    }

    function applyQuantityToAllOrderedMeals(day, sourceMealTime) {
        const sourceSelection = getSelection(day, sourceMealTime);
        if (!sourceSelection) {
            return;
        }

        const nextQty = Math.min(Math.max(parseInt(sourceSelection.qty, 10) || 0, 0), maxPax);
        const selections = getSelectionsForDay(day);

        if (nextQty <= 0 || selections.length < 2) {
            return;
        }

        selections.forEach((selection) => {
            ensureDaySelectionState(day)[selection.mealTime].qty = nextQty > 0 && nextQty < 10 ? 10 : nextQty;
        });

        renderAll();
        showNotification(`${nextQty} pax applied to all ordered meals for ${getDayLabel(day)}.`, 'success');
    }

    function focusFirstProblemDay(problemDay, message) {
        switchToDay(problemDay);
        showNotification(message, 'error', 5000);
    }

    function validateForm() {
        for (let day = 1; day <= totalDays; day++) {
            if (!dayHasSelectedMenu(day)) {
                focusFirstProblemDay(day, `Please select at least one menu for ${getDayLabel(day)} before continuing.`);
                return false;
            }

            if (dayHasInvalidQuantities(day)) {
                focusFirstProblemDay(day, `Please set at least 10 pax for every selected menu on ${getDayLabel(day)}.`);
                return false;
            }
        }

        syncHiddenInputs();
        return true;
    }

    function showConfirmationModal() {
        const modal = document.getElementById('confirmationModal');
        const summaryContainer = document.getElementById('modal-reservation-summary');
        let totalAmount = 0;

        summaryContainer.innerHTML = '';

        for (let day = 1; day <= totalDays; day++) {
            const selections = getSelectionsForDay(day);
            if (!selections.length) {
                continue;
            }

            const dayTab = document.querySelector(`.day-tab[data-day="${day}"]`);
            const dayDate = dayTab ? formatShortDate(createLocalDate(dayTab.dataset.date)) : '';
            let dayTotal = 0;

            const itemsMarkup = selections.map((selection) => {
                const lineTotal = selection.qty * Number(selection.menu.price || 0);
                dayTotal += lineTotal;

                return `
                    <div class="summary-item">
                        <div class="flex justify-between items-start gap-4">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">${escapeHtml(selection.menu.meal_label)}</div>
                                <div class="text-sm text-gray-600">${escapeHtml(selection.menu.menu_label)} - ${escapeHtml(selection.menu.category_label)}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium text-gray-900">${selection.qty} pax x ${formatCurrency(selection.menu.price)}</div>
                                <div class="font-bold text-clsu-green">${formatCurrency(lineTotal)}</div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            const daySection = document.createElement('div');
            daySection.className = 'mb-4';
            daySection.innerHTML = `
                <div class="font-bold text-lg text-clsu-green mb-2 border-b pb-2">${escapeHtml(getDayLabel(day))}${dayDate ? ` (${escapeHtml(dayDate)})` : ''}</div>
                ${itemsMarkup}
                <div class="summary-item font-semibold border-t">
                    <div class="flex justify-between">
                        <span>${escapeHtml(getDayLabel(day))} Subtotal:</span>
                        <span class="text-clsu-green">${formatCurrency(dayTotal)}</span>
                    </div>
                </div>
            `;
            summaryContainer.appendChild(daySection);

            totalAmount += dayTotal;
        }

        const requestValue = document.getElementById('special-request-field').value.trim();
        if (requestValue) {
            const requestSection = document.createElement('div');
            requestSection.className = 'mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4';
            requestSection.innerHTML = `
                <div class="font-semibold text-gray-900 mb-2">Special Request</div>
                <p class="text-sm text-gray-600 whitespace-pre-line">${escapeHtml(requestValue)}</p>
            `;
            summaryContainer.appendChild(requestSection);
        }

        document.getElementById('modal-total-amount').textContent = formatCurrency(totalAmount);
        modal.style.display = 'flex';
    }

    document.addEventListener('DOMContentLoaded', () => {
        totalDays = parseInt(document.getElementById('number-of-days').value, 10) || 1;

        activeMealType = mealOrder.find((mealTime) => {
            return menuLibrary.some((menu) => menu.meal_time === mealTime);
        }) || 'breakfast';

        activeCategory = menuLibrary.some((menu) => menu.category === 'standard') ? 'standard' : 'special';

        hydrateInitialSelections();
        initializeDayTabs();
        updateDayInfo(currentDay);
        renderAll();

        const guidanceAccordion = document.querySelector('[data-menu-guidance-accordion]');
        const guidanceTrigger = document.querySelector('[data-menu-guidance-trigger]');
        const guidancePanel = document.querySelector('[data-menu-guidance-panel]');

        if (guidanceAccordion && guidanceTrigger && guidancePanel) {
            guidanceTrigger.addEventListener('click', () => {
                const isOpen = guidanceAccordion.classList.toggle('is-open');
                guidanceTrigger.setAttribute('aria-expanded', String(isOpen));
                guidancePanel.classList.toggle('hidden', !isOpen);
            });
        }

        document.getElementById('day-tabs').addEventListener('click', (event) => {
            const button = event.target.closest('.day-tab');
            if (!button) {
                return;
            }

            switchToDay(parseInt(button.dataset.day, 10));
        });

        document.getElementById('menu-tabs-container').addEventListener('click', (event) => {
            const button = event.target.closest('.menu-tab');
            if (!button) {
                return;
            }

            activeCategory = button.dataset.menuCategory;
            document.querySelectorAll('.menu-tab').forEach((tab) => {
                tab.classList.toggle('active', tab.dataset.menuCategory === activeCategory);
            });

            expandedMenuId = null;
            renderMenuGrid();
        });

        document.getElementById('meal-type-buttons').addEventListener('click', (event) => {
            const button = event.target.closest('.meal-type-btn');
            if (!button) {
                return;
            }

            activeMealType = button.dataset.mealType;
            document.querySelectorAll('.meal-type-btn').forEach((tab) => {
                tab.classList.toggle('active', tab.dataset.mealType === activeMealType);
            });

            expandedMenuId = null;
            renderMenuGrid();
        });

        document.getElementById('menu-browser-grid').addEventListener('click', (event) => {
            const addButton = event.target.closest('[data-add-menu]');
            if (addButton) {
                selectMenu(addButton.getAttribute('data-add-menu'));
                return;
            }

            const removeButton = event.target.closest('[data-remove-selection]');
            if (removeButton) {
                removeSelection(currentDay, removeButton.getAttribute('data-remove-selection'));
                return;
            }

            const card = event.target.closest('[data-menu-id]');
            if (card && !usesHoverOverlay()) {
                const menuId = card.getAttribute('data-menu-id');
                expandedMenuId = String(expandedMenuId) === String(menuId) ? null : menuId;
                renderMenuGrid();
            }
        });

        document.addEventListener('click', (event) => {
            if (usesHoverOverlay() || !expandedMenuId) {
                return;
            }

            if (clickStartedInsideMenuGrid(event)) {
                return;
            }

            expandedMenuId = null;
            renderMenuGrid();
        });

        document.getElementById('selected-order-list').addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-remove-selection]');
            if (removeButton) {
                removeSelection(currentDay, removeButton.getAttribute('data-remove-selection'));
                return;
            }

            const applyButton = event.target.closest('[data-apply-qty-all]');
            if (applyButton) {
                applyQuantityToAllOrderedMeals(currentDay, applyButton.getAttribute('data-apply-qty-all'));
                return;
            }

            const qtyButton = event.target.closest('[data-qty-action]');
            if (qtyButton) {
                adjustSelectionQuantity(currentDay, qtyButton.getAttribute('data-meal-time'), qtyButton.getAttribute('data-qty-action'));
            }
        });

        document.getElementById('selected-order-list').addEventListener('input', (event) => {
            const input = event.target.closest('[data-summary-qty]');
            if (!input) {
                return;
            }

            toggleQuantityError(input.getAttribute('data-summary-qty'), input.value);
        });

        document.getElementById('selected-order-list').addEventListener('blur', (event) => {
            const input = event.target.closest('[data-summary-qty]');
            if (!input) {
                return;
            }

            commitManualQuantity(currentDay, input.getAttribute('data-summary-qty'), input.value);
        }, true);

        document.getElementById('back-button').addEventListener('click', () => {
            if (currentDay > 1) {
                switchToDay(currentDay - 1);
            }
        });

        document.getElementById('next-day-button').addEventListener('click', () => {
            if (!dayHasSelectedMenu(currentDay)) {
                showNotification(`Please select at least one menu for ${getDayLabel(currentDay)} before moving on.`, 'error');
                return;
            }

            if (dayHasInvalidQuantities(currentDay)) {
                showNotification(`Please set at least 10 pax for every selected menu on ${getDayLabel(currentDay)}.`, 'error');
                return;
            }

            switchToDay(currentDay + 1);
        });

        document.getElementById('confirm-button').addEventListener('click', () => {
            if (validateForm()) {
                showConfirmationModal();
            }
        });

        document.getElementById('reservation-form').addEventListener('submit', (event) => {
            if (!validateForm()) {
                event.preventDefault();
                return;
            }

            syncHiddenInputs();
        });
    });

    document.getElementById('confirmationModal').addEventListener('click', function (event) {
        if (event.target === this) {
            closeConfirmationModal();
        }
    });

    document.getElementById('successModal').addEventListener('click', function (event) {
        if (event.target === this) {
            closeSuccessModal();
        }
    });
</script>
@endsection

@section('content')
<section class="menu_selection-hero-bg py-20 lg:py-20 bg-gray-900 text-white relative overflow-hidden">
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl lg:text-5xl font-extrabold mb-2 tracking-wider">
            Menu Selection
        </h1>
        <p class="text-lg lg:text-xl font-poppins opacity-90">
            Guaranteed delicious meals.
        </p>
    </div>
</section>

<section class="py-10 bg-gray-50 text-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex w-full max-w-3xl mx-auto items-start sm:items-center justify-between sm:justify-center">
                <div class="flex flex-1 sm:flex-none flex-col sm:flex-row items-center text-center sm:text-left min-w-0">
                    <div class="flex items-center justify-center w-10 h-10 bg-clsu-green rounded-full">
                        <span class="text-white font-bold">1</span>
                    </div>
                    <div class="mt-2 sm:mt-0 sm:ml-2 text-[11px] sm:text-sm leading-tight font-medium text-clsu-green">Reservation Details</div>
                </div>
                <div class="hidden sm:block w-16 h-1 bg-clsu-green mx-2"></div>
                <div class="flex flex-1 sm:flex-none flex-col sm:flex-row items-center text-center sm:text-left min-w-0">
                    <div class="flex items-center justify-center w-10 h-10 bg-clsu-green rounded-full">
                        <span class="text-white font-bold">2</span>
                    </div>
                    <div class="mt-2 sm:mt-0 sm:ml-2 text-[11px] sm:text-sm leading-tight font-medium text-clsu-green">Menu Selection</div>
                </div>
                <div class="hidden sm:block w-16 h-1 bg-gray-300 mx-2"></div>
                <div class="flex flex-1 sm:flex-none flex-col sm:flex-row items-center text-center sm:text-left min-w-0">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full">
                        <span class="text-gray-500 font-bold">3</span>
                    </div>
                    <div class="mt-2 sm:mt-0 sm:ml-2 text-[11px] sm:text-sm leading-tight font-medium text-gray-500">Confirmation</div>
                </div>
            </div>
        </div>

        <form action="{{ route('reservation.store') }}" method="POST" class="space-y-6" id="reservation-form">
            @csrf

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    @foreach ($errors->all() as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('reservation_data'))
                <input type="hidden" name="name" value="{{ session('reservation_data.name') }}">
                <input type="hidden" name="department" value="{{ session('reservation_data.department') }}">
                <input type="hidden" name="address" value="{{ session('reservation_data.address') }}">
                <input type="hidden" name="email" value="{{ session('reservation_data.email') }}">
                <input type="hidden" name="phone" value="{{ session('reservation_data.phone') }}">
                <input type="hidden" name="activity" value="{{ session('reservation_data.activity') }}">
                <input type="hidden" name="venue" value="{{ session('reservation_data.venue') }}">
                <input type="hidden" name="project_name" value="{{ session('reservation_data.project_name') }}">
                <input type="hidden" name="account_code" value="{{ session('reservation_data.account_code') }}">
            @endif

            <input type="hidden" id="start-date" value="{{ $startDate }}">
            <input type="hidden" id="end-date" value="{{ $endDate }}">
            <input type="hidden" id="number-of-days" value="{{ $numberOfDays }}">
            <div id="reservation-hidden-inputs" class="hidden" aria-hidden="true"></div>

            <div class="day-nav-container">
                <div class="day-tabs" id="day-tabs"></div>
                <div class="day-info" id="day-info"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-8 space-y-4">
                    <div class="menu-guidance-accordion is-open" data-menu-guidance-accordion>
                        <button
                            type="button"
                            class="menu-guidance-trigger"
                            data-menu-guidance-trigger
                            aria-expanded="true"
                            aria-controls="menu-guidance-panel"
                        >
                            <span class="menu-guidance-trigger-main">
                                <span class="menu-header-guidance-icon" aria-hidden="true">i</span>
                                <span class="menu-header-guidance-title">How to select menus?</span>
                            </span>
                            <svg class="menu-guidance-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="menu-guidance-panel" id="menu-guidance-panel" data-menu-guidance-panel>
                            <div class="menu-header-guidance">
                                <ul class="menu-header-guidance-list">
                                    <li><strong>Pick a Meal Time:</strong> Select Breakfast, Lunch, or Snacks.</li>
                                    <li><strong>Choose a Menu:</strong> Toggle between Standard or Special.</li>
                                    <li><strong>Add to Order:</strong> Click your preferred menu card.</li>
                                    <li><strong>Adjust Pax:</strong> Set the number of guests in the Order Preview.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="green-menu-list w-full lg:sticky lg:top-6">
                        <div class="menu-search-container">
                            <div class="space-y-4">
                                <div class="meal-filters" id="meal-type-buttons">
                                    <button type="button" class="meal-type-btn active" data-meal-type="breakfast">
                                        <span class="meal-type-btn-label">Breakfast</span>
                                        <span class="meal-type-btn-status" data-meal-filter-status>Not selected</span>
                                    </button>
                                    <button type="button" class="meal-type-btn" data-meal-type="am_snacks">
                                        <span class="meal-type-btn-label">A.M. Snacks</span>
                                        <span class="meal-type-btn-status" data-meal-filter-status>Not selected</span>
                                    </button>
                                    <button type="button" class="meal-type-btn" data-meal-type="lunch">
                                        <span class="meal-type-btn-label">Lunch</span>
                                        <span class="meal-type-btn-status" data-meal-filter-status>Not selected</span>
                                    </button>
                                    <button type="button" class="meal-type-btn" data-meal-type="pm_snacks">
                                        <span class="meal-type-btn-label">P.M. Snacks</span>
                                        <span class="meal-type-btn-status" data-meal-filter-status>Not selected</span>
                                    </button>
                                    <button type="button" class="meal-type-btn" data-meal-type="dinner">
                                        <span class="meal-type-btn-label">Dinner</span>
                                        <span class="meal-type-btn-status" data-meal-filter-status>Not selected</span>
                                    </button>
                                </div>

                                <div class="menu-tabs-container" id="menu-tabs-container">
                                    <button type="button" class="menu-tab active" data-menu-category="standard">Standard</button>
                                    <button type="button" class="menu-tab" data-menu-category="special">Special</button>
                                </div>
                            </div>
                        </div>

                        <div class="menu-preview-container">
                            <div class="menu-content-area">
                                <div id="menu-browser-grid" class="grid grid-cols-1 xl:grid-cols-2 gap-4"></div>
                                <div id="menu-empty-state" class="no-results hidden"></div>
                            </div>
                        </div>

                        <div class="menu-browser-footer">
                            <p class="text-sm text-gray-600">
                                Use the Order Preview Panel to adjust pax after adding a menu. Selecting another menu for the same meal category automatically replaces the earlier choice.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4">
                    <div class="space-y-6 lg:sticky lg:top-6">
                        <div class="card p-6 space-y-6">
                            <div>
                                <div class="flex items-start justify-between gap-4 mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-clsu-green">Order Preview</h3>
                                    </div>
                                    <span class="selection-chip" id="current-day-selection-count">0 selected</span>
                                </div>

                                <div id="selected-order-list" class="space-y-3"></div>
                                <div id="selected-order-empty" class="selected-order-empty">
                                    Choose a menu from the left panel to start building the order for this day.
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-6">
                                <div id="order-summary-day-totals" class="space-y-3"></div>

                                <div class="border-t border-gray-300 pt-3 mt-4 space-y-2">
                                    <div class="flex justify-between font-bold">
                                        <span>Estimated Reservation Total:</span>
                                        <span id="estimated-total">&#8369;0.00</span>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-6">
                                <div class="flex items-start justify-between gap-4 mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-clsu-green">Special Request <span class="text-sm font-semibold text-gray-500">(Optional)</span></h3>
                                    </div>
                                </div>

                                <textarea
                                    name="notes"
                                    id="special-request-field"
                                    rows="6"
                                    class="w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-clsu-green focus:border-clsu-green"
                                    placeholder="Add special instructions, allergy notes, or dietary restrictions..."
                                >{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="menu-navigation-actions pt-2">
                <button
                    type="button"
                    id="back-button"
                    class="navigation-button menu-nav-action bg-gray-500 text-white text-base rounded-xl hover:bg-gray-600 transition duration-150 shadow-lg font-semibold hidden"
                >
                    Back
                </button>
                <button
                    type="button"
                    id="next-day-button"
                    class="navigation-button menu-nav-action bg-clsu-green text-white text-base rounded-xl hover:bg-green-700 transition duration-150 shadow-2xl font-bold tracking-wide"
                >
                    Next Day
                </button>
                <button
                    type="button"
                    id="confirm-button"
                    data-loading-text="Submitting..."
                    class="navigation-button menu-nav-action bg-clsu-green text-white text-base rounded-xl hover:bg-green-700 transition duration-150 shadow-2xl font-bold tracking-wide hidden"
                    disabled
                    aria-disabled="true"
                >
                    Confirm
                </button>
            </div>
        </form>
    </div>
</section>

<div id="confirmationModal" class="confirmation-modal">
    <div class="confirmation-modal-content">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Confirm Your Reservation</h3>
            <p class="text-sm text-gray-600">Please review your day-by-day order summary before confirming.</p>
        </div>

        <div class="border border-gray-200 rounded-lg p-6 mb-6">
            <h4 class="text-lg font-bold text-clsu-green mb-4">Reservation Summary</h4>
            <div id="modal-reservation-summary"></div>
            <div class="total-section">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-900">Total Amount:</span>
                    <span id="modal-total-amount" class="text-2xl font-bold text-clsu-green">&#8369;0.00</span>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="text-sm text-yellow-800">
                    <p class="font-semibold">Important:</p>
                    <p class="mt-1">Your reservation will be reviewed by the cafeteria admin. Please make sure to complete your payment through fund transfer after the event.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeConfirmationModal()" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 font-medium">
                Cancel
            </button>
            <button type="button" id="submit-reservation-button" data-loading-text="Submitting Reservation..." onclick="submitReservation(this)" class="px-6 py-3 bg-clsu-green text-white rounded-lg hover:bg-green-700 transition duration-150 font-medium">
                Confirm Reservation
            </button>
        </div>
    </div>
</div>

<div id="successModal" class="confirmation-modal">
    <div class="confirmation-modal-content">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Reservation Successful!</h3>
            <p class="text-sm text-gray-600">Your reservation has been created successfully.</p>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-green-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-green-800">
                    <p class="font-semibold">What happens next?</p>
                    <ul class="mt-2 space-y-1">
                        <li>- You will be redirected to your reservation details</li>
                        <li>- You will receive a confirmation email</li>
                        <li>- Please proceed with payment within 3 days</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex justify-center">
            <button type="button" onclick="redirectToReservationDetails()" class="px-6 py-3 bg-clsu-green text-white rounded-lg hover:bg-green-700 transition duration-150 font-medium">
                View Reservation Details
            </button>
        </div>
    </div>
</div>
@endsection
