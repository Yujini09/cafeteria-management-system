<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation #{{ $reservation->id }}</title>
    <style>
        @page {
            margin: 20px 22px;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.45;
            color: #111827;
            background: #f8fafc;
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
            margin-bottom: 14px;
        }

        .header-title {
            margin: 0;
            font-size: 21px;
            font-weight: 700;
            line-height: 1.2;
            color: #111827;
        }

        .header-subtitle,
        .header-meta {
            margin-top: 4px;
            color: #6b7280;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #ffffff;
            padding: 16px;
            margin-bottom: 14px;
        }

        .card-title {
            margin: 0 0 12px;
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }

        .card-title .icon {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid #16a34a;
            border-radius: 3px;
            margin-right: 8px;
            vertical-align: -1px;
            box-sizing: border-box;
        }

        .section-separator {
            border-top: 1px solid #e5e7eb;
            margin-top: 14px;
            padding-top: 14px;
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
            color: #6b7280;
            font-weight: 600;
            font-size: 11px;
        }

        .field-value {
            color: #111827;
            font-size: 12px;
        }

        .field-value.emphasis {
            font-size: 16px;
            font-weight: 700;
            color: #16a34a;
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
            color: #374151;
            font-weight: 500;
        }

        .timeline-time {
            display: inline-block;
            color: #111827;
        }

        .muted {
            color: #9ca3af;
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
        }

        .badge-status {
            background: #e5e7eb;
            color: #374151;
        }

        .badge-paid {
            background: #dcfce7;
            color: #166534;
        }

        .badge-unpaid {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-na {
            background: #f3f4f6;
            color: #4b5563;
        }

        .table th,
        .table td {
            border: 1px solid #e5e7eb;
            padding: 7px 8px;
            text-align: left;
            vertical-align: top;
        }

        .table th {
            background: #f9fafb;
            font-size: 11px;
        }

        .day-heading {
            margin: 12px 0 6px;
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        .component-list {
            margin-top: 3px;
            color: #6b7280;
            font-size: 10px;
        }

        .amount-table {
            width: 100%;
            border-collapse: collapse;
        }

        .amount-table td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
        }

        .amount-table .total-row td {
            background: #f0fdf4;
            font-weight: 700;
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
                </td>
                <td class="text-right">
                    <div class="header-meta">Exported {{ $exportedAt->format('M d, Y h:i A') }}</div>
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
            <tr>
                <td>Service Fee</td>
                <td class="text-right">PHP {{ number_format($serviceFee, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Grand Total</td>
                <td class="text-right">PHP {{ number_format($grandTotal, 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
