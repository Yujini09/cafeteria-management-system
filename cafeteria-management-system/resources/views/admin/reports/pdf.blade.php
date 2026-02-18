<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($reportType) }} Report - {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</title>
    <style>
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
            padding: 20px;
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
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background: var(--neutral-50);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--neutral-200);
        }

        .summary-item {
            text-align: center;
            flex: 1;
        }

        .summary-item h3 {
            margin: 0 0 8px 0;
            font-size: 11px;
            color: var(--neutral-600);
            text-transform: uppercase;
            font-weight: 600;
        }

        .summary-item p {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
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

        th, td {
            border: 1px solid var(--neutral-300);
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
            border-top: 2px solid var(--primary);
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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($reportType) }} Report</h1>
        <p>Report Period: {{ $startDate->format('F d, Y') }} - {{ $endDate->format('F d, Y') }}</p>
        <p>Generated on: {{ now()->format('F d, Y \a\t H:i') }}</p>
    </div>

    @if($reportType === 'sales')
        <div class="summary">
            <div class="summary-item">
                <h3>Total Reservations</h3>
                <p>{{ $totalReservations ?? 0 }}</p>
            </div>
            <div class="summary-item">
                <h3>Total Sales</h3>
                <p class="currency">₱{{ number_format($totalRevenue ?? 0, 2) }}</p>
            </div>
        </div>

        @if($salesData->isEmpty())
            <div class="no-data">
                <p>No sales data found for the selected period.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">Res. ID</th>
                        <th style="width: 15%;">Event Name</th>
                        <th style="width: 12%;">Event Date</th>
                        <th style="width: 15%;">Customer</th>
                        <th style="width: 8%;">Persons</th>
                        <th style="width: 20%;">Menu Item</th>
                        <th style="width: 7%;">Type</th>
                        <th style="width: 10%;">Meal Time</th>
                        <th style="width: 5%;">Qty</th>
                        <th style="width: 10%;">Unit Price</th>
                        <th style="width: 10%;">Item Total</th>
                        <th style="width: 12%;">Res. Total</th>
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
                            <td style="text-align: right;">₱{{ number_format($item['unit_price'], 2) }}</td>
                            <td style="text-align: right;">₱{{ number_format($item['total'], 2) }}</td>
                            @if($index === 0)
                                <td rowspan="{{ count($reservation['items']) }}" style="text-align: right; font-weight: bold;" class="currency">
                                    ₱{{ number_format($reservation['reservation_total'], 2) }}
                                </td>
                            @endif
                        </tr>
                        @endforeach
                    @endforeach

                    <!-- Grand Total Row -->
                    <tr class="total-row">
                        <td colspan="11" style="text-align: right; font-weight: bold;">GRAND TOTAL:</td>
                        <td style="text-align: right; font-weight: bold;" class="currency">₱{{ number_format($totalRevenue ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>
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
                        <th style="width: 10%;">Department</th>
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
                        <td style="text-align: right;" class="currency">₱{{ number_format($customer['total_spent'], 2) }}</td>
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
</body>
</html>