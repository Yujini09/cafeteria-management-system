<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation #{{ $reservation->id }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin-top: 8mm;
            margin-bottom: 10mm;
            margin-left: 8mm;
            margin-right: 8mm;
        }

        :root {
            --primary: #00462E;
            --primary-light: #057C3C;
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
            --success: #059669;
            --warning: #b45309;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.45;
            color: var(--neutral-800);
            background: #ffffff;
        }

        .header-table,
        .layout-table,
        .contact-table,
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td,
        .layout-table td,
        .contact-table td {
            vertical-align: top;
        }

        .page-header {
            margin-bottom: 16px;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 12px;
        }

        .header-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.2;
            color: var(--primary);
        }

        .header-subtitle,
        .header-meta {
            margin-top: 3px;
            color: var(--neutral-600);
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .card {
            border: 1px solid var(--neutral-200);
            border-radius: 10px;
            background: var(--neutral-50);
            padding: 12px;
            margin-bottom: 12px;
        }

        .card-title {
            margin: 0 0 10px;
            font-size: 13px;
            font-weight: 700;
            color: var(--neutral-800);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .card-title .icon {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 2px solid var(--primary-light);
            border-radius: 3px;
            margin-right: 6px;
            vertical-align: 0;
            box-sizing: border-box;
        }

        .section-separator {
            border-top: 1px solid var(--neutral-200);
            margin-top: 12px;
            padding-top: 12px;
        }

        .content-block {
            padding-right: 10px;
        }

        .field {
            margin-bottom: 12px;
        }

        .field:last-child {
            margin-bottom: 0;
        }

        .field-label {
            margin-bottom: 3px;
            color: var(--neutral-600);
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .field-value {
            color: var(--neutral-900);
            font-size: 12px;
        }

        .field-value.emphasis {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary);
        }

        .field-value.strong {
            font-weight: 700;
        }

        .timeline-summary {
            margin-bottom: 8px;
            font-weight: 600;
        }

        .timeline-row {
            margin-bottom: 4px;
        }

        .timeline-date {
            display: inline-block;
            width: 128px;
            color: var(--neutral-700);
            font-weight: 600;
        }

        .timeline-time {
            display: inline-block;
            color: var(--neutral-900);
        }

        .muted {
            color: var(--neutral-500);
        }

        .break-all {
            word-break: break-word;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            margin-left: 6px;
            border: 1px solid transparent;
        }

        .badge-status {
            background: var(--neutral-100);
            color: var(--neutral-700);
            border-color: var(--neutral-200);
        }

        .badge-paid {
            background: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .badge-unpaid {
            background: #fef3c7;
            color: var(--warning);
            border-color: #fcd34d;
        }

        .badge-na {
            background: var(--neutral-100);
            color: var(--neutral-600);
            border-color: var(--neutral-200);
        }

        .table th,
        .table td {
            border: 0.5px solid var(--neutral-300);
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        .table th {
            background: var(--primary);
            color: #ffffff;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .table tbody tr:nth-child(even) {
            background: #ffffff;
        }

        .day-heading {
            margin: 10px 0 6px;
            font-size: 11px;
            font-weight: 700;
            color: var(--neutral-700);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .component-list {
            margin-top: 3px;
            color: var(--neutral-600);
            font-size: 10px;
        }

        .amount-table {
            width: 100%;
            border-collapse: collapse;
        }

        .amount-table td {
            border: 0.5px solid var(--neutral-300);
            padding: 7px 10px;
            background: #ffffff;
        }

        .amount-table .total-row td {
            background: var(--neutral-100);
            font-weight: 700;
            border-top: 1px solid var(--primary);
        }

        .amount-table tr:nth-child(even) td {
            background: var(--neutral-50);
        }

        .footer {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px solid var(--neutral-300);
            text-align: center;
            font-size: 10px;
            color: var(--neutral-500);
        }
    </style>
</head>
<body>
    @php
        $startDate = $reservation->event_date ? \Carbon\Carbon::parse($reservation->event_date) : null;
        $endDate = $reservation->end_date ? \Carbon\Carbon::parse($reservation->end_date) : $startDate;
        $dayTimes = $reservation->day_times ?? [];
        $days = ($startDate && $endDate) ? ($startDate->diffInDays($endDate) + 1) : 0;

        if ($startDate && $endDate) {
            $dateSummary = $days > 1
                ? $startDate->format('M d') . ' - ' . $endDate->format('M d, Y') . ' (' . $days . ' days)'
                : $startDate->format('M d, Y');
        } else {
            $dateSummary = 'N/A';
        }

        $dateRange = [];
        if ($startDate && $endDate) {
            for ($i = 0; $i < $days; $i++) {
                $currentDate = $startDate->copy()->addDays($i);
                $dateRange[$currentDate->format('Y-m-d')] = $currentDate;
            }
        }

        $formatTimeForDisplay = function ($timeString) {
            if (empty($timeString) || trim((string) $timeString) === '') {
                return '';
            }

            $timeString = trim((string) $timeString);

            if (preg_match('/\d{1,2}:\d{2}\s*(AM|PM|am|pm)/i', $timeString)) {
                return strtoupper($timeString);
            }

            try {
                return \Carbon\Carbon::createFromFormat('H:i', $timeString)->format('g:iA');
            } catch (\Exception $e) {
                return $timeString;
            }
        };

        $paymentBadgeClass = match ($paymentLabel) {
            'Paid' => 'badge-paid',
            'Unpaid' => 'badge-unpaid',
            default => 'badge-na',
        };
    @endphp

    <div class="page-header">
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="header-title">Reservation Details</h1>
                    <div class="header-subtitle">Reservation #{{ $reservation->id }}</div>
                    <div class="header-subtitle">Event Schedule: {{ $dateSummary }}</div>
                </td>
                <td class="text-right">
                    <div class="header-meta">Exported on {{ $exportedAt->format('F d, Y \a\t h:i A') }}</div>
                    <div class="header-meta">Exported by {{ $exportedBy }}</div>
                    <div style="margin-top: 8px;">
                        <span class="badge badge-status">{{ ucfirst($reservation->status ?? 'pending') }}</span>
                        <span class="badge {{ $paymentBadgeClass }}">Payment: {{ $paymentLabel }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="card">
        <div class="card-title">
            <span class="icon"></span>
            Event Details
        </div>

        <table class="layout-table">
            <tr>
                <td style="width: 50%;" class="content-block">
                    <div class="field">
                        <div class="field-label">Event Name:</div>
                        <div class="field-value strong">{{ $reservation->event_name ?? '—' }}</div>
                    </div>

                    <div class="field">
                        <div class="field-label">Date &amp; Time:</div>
                        <div class="field-value">
                            <div class="timeline-summary">{{ $dateSummary }}</div>
                            @if(!empty($dateRange))
                                @foreach($dateRange as $dateKey => $currentDate)
                                    @php
                                        $formattedDate = $currentDate->format('M d, Y');
                                        $startTime = '';
                                        $endTime = '';

                                        if (is_array($dayTimes) && isset($dayTimes[$dateKey])) {
                                            $timeData = $dayTimes[$dateKey];
                                            if (is_array($timeData)) {
                                                $startTime = $timeData['start_time'] ?? $timeData['start'] ?? '';
                                                $endTime = $timeData['end_time'] ?? $timeData['end'] ?? '';
                                            } elseif (is_string($timeData)) {
                                                $parts = explode(' - ', $timeData);
                                                $startTime = trim($parts[0] ?? '');
                                                $endTime = trim($parts[1] ?? '');
                                            }
                                        } elseif (is_string($dayTimes) && $days === 1) {
                                            $parts = explode(' - ', $dayTimes);
                                            $startTime = trim($parts[0] ?? '');
                                            $endTime = trim($parts[1] ?? '');
                                        }

                                        if (empty($startTime) && $days === 1 && !empty($reservation->event_time)) {
                                            $parts = explode(' - ', (string) $reservation->event_time);
                                            $startTime = trim($parts[0] ?? '');
                                            $endTime = trim($parts[1] ?? '');
                                        }

                                        $formattedStartTime = $formatTimeForDisplay($startTime);
                                        $formattedEndTime = $formatTimeForDisplay($endTime);
                                    @endphp
                                    <div class="timeline-row">
                                        <span class="timeline-date">{{ $formattedDate }}</span>
                                        <span class="timeline-time">
                                            @if($formattedStartTime !== '')
                                                {{ $formattedStartTime }}{{ $formattedEndTime !== '' ? ' - ' . $formattedEndTime : '' }}
                                            @else
                                                <span class="muted">No time specified</span>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            @elseif(!empty($reservation->event_time))
                                <div>{{ $reservation->event_time }}</div>
                            @else
                                <div class="muted">No time specified</div>
                            @endif
                        </div>
                    </div>
                </td>

                <td style="width: 50%;">
                    <div class="field">
                        <div class="field-label">Number of Persons:</div>
                        <div class="field-value emphasis">{{ $reservation->number_of_persons ?? '—' }}</div>
                    </div>

                    <div class="field">
                        <div class="field-label">Venue:</div>
                        <div class="field-value strong">{{ $reservation->venue ?? '—' }}</div>
                    </div>

                    <div class="field">
                        <div class="field-label">Special Requests:</div>
                        <div class="field-value">{{ $reservation->special_requests ?? 'None' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-separator">
            <table class="contact-table">
                <tr>
                    <td style="width: 50%;" class="content-block">
                        <div class="field">
                            <div class="field-label">Contact Person:</div>
                            <div class="field-value strong">{{ $reservation->contact_person ?? optional($reservation->user)->name ?? '—' }}</div>
                        </div>

                        <div class="field">
                            <div class="field-label">Department:</div>
                            <div class="field-value">{{ $reservation->department ?? '—' }}</div>
                        </div>

                        <div class="field">
                            <div class="field-label">Address:</div>
                            <div class="field-value break-all">{{ $reservation->address ?? '—' }}</div>
                        </div>
                    </td>

                    <td style="width: 50%;">
                        <div class="field">
                            <div class="field-label">Email:</div>
                            <div class="field-value break-all">{{ $reservation->email ?? optional($reservation->user)->email ?? '—' }}</div>
                        </div>

                        <div class="field">
                            <div class="field-label">Phone:</div>
                            <div class="field-value">{{ $reservation->contact_number ?? optional($reservation->user)->phone ?? '—' }}</div>
                        </div>

                        <div class="field">
                            <div class="field-label">OR Number:</div>
                            <div class="field-value">{{ $reservation->or_number ?? 'N/A' }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Selected Menus</div>
        @if(!empty($menuGroups))
            @foreach($menuGroups as $group)
                <div class="day-heading">
                    Day {{ $group['day_number'] }}
                    @if($group['date'])
                        - {{ $group['date']->format('M d, Y') }}
                    @endif
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Menu Item</th>
                            <th>Meal</th>
                            <th style="width: 11%;">Quantity</th>
                            <th style="width: 16%;">Price</th>
                            <th style="width: 16%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group['items'] as $item)
                            <tr>
                                <td>
                                    {{ $item['menu_name'] }}
                                    @if(!empty($item['components']))
                                        <div class="component-list">{{ implode(', ', $item['components']) }}</div>
                                    @endif
                                </td>
                                <td>{{ $item['meal_time'] ?: 'N/A' }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>PHP {{ number_format($item['price'], 2) }}</td>
                                <td>PHP {{ number_format($item['total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @else
            <div class="muted">No menus selected for this reservation.</div>
        @endif
    </div>

    <div class="card">
        <div class="card-title">Event Additionals</div>
        @if(!empty($additionals))
            <table class="table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="width: 24%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($additionals as $additional)
                        <tr>
                            <td>{{ $additional['name'] }}</td>
                            <td>PHP {{ number_format($additional['price'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="muted">No additionals recorded.</div>
        @endif
    </div>

    <div class="card">
        <div class="card-title">Charges Summary</div>
        <table class="amount-table">
            <tr>
                <td>Menu Subtotal</td>
                <td class="text-right">PHP {{ number_format($menuSubtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Additional Charges</td>
                <td class="text-right">PHP {{ number_format($additionalsTotal, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Grand Total</td>
                <td class="text-right">PHP {{ number_format($grandTotal, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This reservation detail report was generated automatically by the Smart Cafeteria Management System.</p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font('DejaVu Sans', 'normal');
            $size = 9;
            $text = 'Page {PAGE_NUM} of {PAGE_COUNT}';
            $y = $pdf->get_height() - 24;
            $x = $pdf->get_width() - $fontMetrics->get_text_width($text, $font, $size) - 18;
            $pdf->page_text($x, $y, $text, $font, $size, [0.45, 0.45, 0.45]);
        }
    </script>
</body>
</html>
