@php
    $reportTitle = match($reportType) {
        'reservation' => 'Reservations Report',
        'sales' => 'Sales Report',
        'inventory' => 'Inventory Report',
        'crm' => 'CRM Report',
        default => ucfirst($reportType) . ' Report',
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }} - {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</title>
    <style>
        @page {
            margin-top: 4mm;
            margin-bottom: 8mm;
            margin-left: 8mm;
            margin-right: 8mm;
        }

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
            --success: #059669;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: var(--neutral-800);
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            color: var(--primary);
            font-weight: 700;
        }

        .header p {
            margin: 3px 0;
            color: var(--neutral-600);
            font-size: 11px;
        }

        .summary {
            margin-bottom: 20px;
            background: var(--neutral-50);
            padding: 10px;
            border-radius: 8px;
            border: 1px solid var(--neutral-200);
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin: 0;
        }

        .summary-table td {
            border: none;
            background: transparent;
            width: 50%;
            padding: 0;
            text-align: center;
            vertical-align: top;
        }

        .summary-item {
            text-align: center;
            border: 1px solid var(--neutral-200);
            border-radius: 8px;
            background: white;
            padding: 10px 12px;
        }

        .summary-line {
            margin: 0;
            display: inline-flex;
            align-items: baseline;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
        }

        .summary-label {
            font-size: 10px;
            color: var(--neutral-600);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .summary-value {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
            border-radius: 6px;
            overflow: hidden;
        }

        .sales-table {
            table-layout: fixed;
            font-size: 10px;
        }

        .sales-table th {
            font-size: 9px;
        }

        .sales-table td {
            word-break: break-word;
        }

        .sales-table-section {
            page-break-before: always;
        }

        th, td {
            border: 0.5px solid var(--neutral-300);
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: var(--primary);
            color: white;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: var(--neutral-50);
        }

        .reservation-header {
            background-color: #e8f5e8 !important;
            font-weight: 600;
        }

        .total-row {
            background-color: var(--neutral-100) !important;
            font-weight: bold;
        }

        .total-row td {
            border-top: 1px solid var(--primary);
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid var(--neutral-300);
            text-align: center;
            font-size: 10px;
            color: var(--neutral-500);
        }

        .currency {
            font-weight: bold;
            color: var(--success);
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: var(--neutral-500);
            font-style: italic;
            font-size: 12px;
            background: var(--neutral-50);
            border-radius: 6px;
            border: 1px solid var(--neutral-200);
        }

        .charts-section {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .charts-title {
            margin: 0 0 6px 0;
            font-size: 11px;
            color: var(--neutral-700);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 700;
        }

        .charts-grid {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .charts-grid td {
            border: none;
            width: 50%;
            vertical-align: top;
            padding: 0 5px 8px 0;
        }

        .diagram-card {
            border: 1px solid var(--neutral-200);
            border-radius: 7px;
            padding: 6px;
            background: #fff;
            min-height: 165px;
        }

        .diagram-card h4 {
            margin: 0 0 4px 0;
            font-size: 10px;
            color: var(--neutral-700);
            font-weight: 700;
        }

        .chart-card img {
            width: 320px;
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .insight-card {
            min-height: 165px;
        }

        .insight-list {
            margin: 0;
            padding: 0 0 0 14px;
        }

        .insight-list li {
            margin: 0 0 4px 0;
            font-size: 9px;
            color: var(--neutral-700);
            line-height: 1.35;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $reportTitle }}</h1>
        <p>Report Period: {{ $startDate->format('F d, Y') }} - {{ $endDate->format('F d, Y') }}</p>
        <p>Generated on: {{ now()->format('F d, Y \a\t H:i') }}</p>
        <p>Generated by: {{ $generatedBy }}</p>
    </div>

    @if(!empty($chartSvgs) || !empty($insights))
        <div class="charts-section">
            <h3 class="charts-title">Charts & Diagrams</h3>
            <table class="charts-grid">
                <tbody>
                    @php
                        $diagramCards = [];
                        foreach (($chartSvgs ?? []) as $chart) {
                            $diagramCards[] = [
                                'type' => 'chart',
                                'label' => $chart['label'] ?? 'Chart',
                                'svg' => $chart['svg'] ?? null,
                            ];
                        }

                        if (!empty($insights)) {
                            $diagramCards[] = [
                                'type' => 'insights',
                                'label' => 'Key Insights',
                                'items' => $insights,
                            ];
                        }

                        $chartChunks = array_chunk($diagramCards, 2);
                    @endphp
                    @foreach($chartChunks as $chunk)
                        <tr>
                            @foreach($chunk as $card)
                                <td>
                                    @if(($card['type'] ?? '') === 'insights')
                                        <div class="diagram-card insight-card">
                                            <h4>{{ $card['label'] ?? 'Key Insights' }}</h4>
                                            <ul class="insight-list">
                                                @foreach(($card['items'] ?? []) as $insight)
                                                    <li>{{ $insight }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <div class="diagram-card chart-card">
                                            <h4>{{ $card['label'] ?? 'Chart' }}</h4>
                                            <img src="{{ $card['svg'] }}" alt="{{ $card['label'] ?? 'Chart' }}">
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                            @if(count($chunk) < 2)
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($reportType === 'sales')
        <div class="summary">
            <table class="summary-table">
                <tbody>
                    <tr>
                        <td>
                            <div class="summary-item">
                                <p class="summary-line">
                                    <span class="summary-label">Total Reservations:</span>
                                    <span class="summary-value">{{ $totalReservations ?? 0 }}</span>
                                </p>
                            </div>
                        </td>
                        <td>
                            <div class="summary-item">
                                <p class="summary-line">
                                    <span class="summary-label">Total Sales:</span>
                                    <span class="summary-value currency">PHP {{ number_format($totalRevenue ?? 0, 2) }}</span>
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($salesData->isEmpty())
            <div class="no-data">
                <p>No sales data found for the selected period.</p>
            </div>
        @else
            <div class="sales-table-section">
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th style="width: 6%;">Res. ID</th>
                            <th style="width: 12%;">Event Name</th>
                            <th style="width: 9%;">Event Date</th>
                            <th style="width: 12%;">Customer</th>
                            <th style="width: 6%;">Persons</th>
                            <th style="width: 15%;">Menu Item</th>
                            <th style="width: 6%;">Type</th>
                            <th style="width: 8%;">Meal Time</th>
                            <th style="width: 5%;">Qty</th>
                            <th style="width: 8%;">Unit Price</th>
                            <th style="width: 7%;">Item Total</th>
                            <th style="width: 6%;">Res. Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentReservationId = null; @endphp
                        @foreach($salesData as $reservation)
                            @foreach($reservation['items'] as $index => $item)
                            <tr @if($currentReservationId !== $reservation['reservation_id']) class="reservation-header" @endif>
                                @if($currentReservationId !== $reservation['reservation_id'])
                                    <td rowspan="{{ count($reservation['items']) }}">{{ $reservation['reservation_id'] }}</td>
                                    <td rowspan="{{ count($reservation['items']) }}">{{ $reservation['event_name'] }}</td>
                                    <td rowspan="{{ count($reservation['items']) }}">{{ $reservation['event_date'] }}</td>
                                    <td rowspan="{{ count($reservation['items']) }}">{{ $reservation['customer_name'] }}</td>
                                    <td rowspan="{{ count($reservation['items']) }}">{{ $reservation['number_of_persons'] }}</td>
                                    @php $currentReservationId = $reservation['reservation_id']; @endphp
                                @endif
                                <td>{{ $item['menu_name'] }}</td>
                                <td>{{ $item['type'] }}</td>
                                <td>{{ $item['meal_time'] }}</td>
                                <td style="text-align: center;">{{ $item['quantity'] }}</td>
                                <td style="text-align: right;">PHP {{ number_format($item['unit_price'], 2) }}</td>
                                <td style="text-align: right;">PHP {{ number_format($item['total'], 2) }}</td>
                                @if($index === 0)
                                    <td rowspan="{{ count($reservation['items']) }}" style="text-align: right; font-weight: bold;" class="currency">
                                        PHP {{ number_format($reservation['reservation_total'], 2) }}
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        @endforeach

                        <!-- Grand Total Row -->
                        <tr class="total-row">
                            <td colspan="11" style="text-align: right; font-weight: bold;">GRAND TOTAL:</td>
                            <td style="text-align: right; font-weight: bold;" class="currency">PHP {{ number_format($totalRevenue ?? 0, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        <div class="footer">
            <p>This report was generated automatically by the Smart Cafeteria Management System.</p>
            <p>Report covers approved reservations only.</p>
        </div>

    @elseif($reportType === 'reservation')
        @if($reservationData->isEmpty())
            <div class="no-data">
                <p>No reservation data found for the selected period.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">Res. ID</th>
                        <th style="width: 20%;">Event Name</th>
                        <th style="width: 12%;">Event Date</th>
                        <th style="width: 15%;">Customer</th>
                        <th style="width: 10%;">Department/Office</th>
                        <th style="width: 8%;">Persons</th>
                        <th style="width: 8%;">Status</th>
                        <th style="width: 12%;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservationData as $reservation)
                    <tr>
                        <td>{{ $reservation['id'] }}</td>
                        <td>{{ $reservation['event_name'] }}</td>
                        <td>{{ $reservation['event_date'] }}</td>
                        <td>{{ $reservation['customer_name'] }}</td>
                        <td>{{ $reservation['department'] }}</td>
                        <td style="text-align: center;">{{ $reservation['number_of_persons'] }}</td>
                        <td>{{ $reservation['status'] }}</td>
                        <td>{{ $reservation['created_at'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="footer">
            <p>This report was generated automatically by the Smart Cafeteria Management System.</p>
        </div>

    @elseif($reportType === 'inventory')
        @if($inventoryData->isEmpty())
            <div class="no-data">
                <p>No inventory usage data found for the selected period.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 40%;">Inventory Item</th>
                        <th style="width: 15%;">Unit</th>
                        <th style="width: 20%;">Total Used</th>
                        <th style="width: 25%;">Reservations Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventoryData as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['unit'] }}</td>
                        <td style="text-align: right;">{{ number_format($item['total_used'], 2) }}</td>
                        <td style="text-align: center;">{{ $item['reservations_count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="footer">
            <p>This report was generated automatically by the Smart Cafeteria Management System.</p>
            <p>Report covers approved reservations only.</p>
        </div>

    @elseif($reportType === 'crm')
        @if($crmData->isEmpty())
            <div class="no-data">
                <p>No customer data found for the selected period.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 25%;">Customer Name</th>
                        <th style="width: 25%;">Email</th>
                        <th style="width: 10%;">Total Reservations</th>
                        <th style="width: 15%;">Approved</th>
                        <th style="width: 15%;">Total Spent</th>
                        <th style="width: 10%;">Last Reservation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($crmData as $customer)
                    <tr>
                        <td>{{ $customer['name'] }}</td>
                        <td>{{ $customer['email'] }}</td>
                        <td style="text-align: center;">{{ $customer['total_reservations'] }}</td>
                        <td style="text-align: center;">{{ $customer['approved_reservations'] }}</td>
                        <td style="text-align: right;" class="currency">PHP {{ number_format($customer['total_spent'], 2) }}</td>
                        <td>{{ $customer['last_reservation'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="footer">
            <p>This report was generated automatically by the Smart Cafeteria Management System.</p>
            <p>Report covers customers with reservations in the selected period.</p>
        </div>
    @endif
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
