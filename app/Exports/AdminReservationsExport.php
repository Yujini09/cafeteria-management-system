<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AdminReservationsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        protected Collection $reservations
    ) {
    }

    public function collection(): Collection
    {
        return $this->reservations;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer',
            'Department/Office',
            'Status',
            'Payment',
            'Created',
            'Email',
        ];
    }

    public function map($reservation): array
    {
        $status = strtolower((string) ($reservation->status ?? 'pending'));
        $statusLabel = match ($status) {
            'approved' => 'Approved',
            'declined' => 'Declined',
            'cancelled' => 'Cancelled',
            default => 'Pending',
        };

        $paymentLabel = in_array($status, ['declined', 'cancelled'], true)
            ? 'N/A'
            : (($reservation->payment_status ?? 'unpaid') === 'paid' ? 'Paid' : 'Unpaid');

        return [
            $reservation->id,
            $reservation->contact_person ?? optional($reservation->user)->name ?? '-',
            $reservation->department ?? optional($reservation->user)->department ?? 'N/A',
            $statusLabel,
            $paymentLabel,
            optional($reservation->created_at)->format('Y-m-d H:i') ?? 'N/A',
            $reservation->email ?? optional($reservation->user)->email ?? '-',
        ];
    }
}
