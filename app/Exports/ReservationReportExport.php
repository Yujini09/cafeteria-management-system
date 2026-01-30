<?php

namespace App\Exports;

use App\Models\Reservation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class ReservationReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate->startOfDay();
        $this->endDate = $endDate->endOfDay();
    }

    public function collection()
    {
        return Reservation::with(['user'])
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [$this->startDate, $this->endDate])
            ->orderBy('event_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Event Name',
            'Event Date',
            'Customer Name',
            'Department',
            'Number of Persons',
            'Status',
            'Created At',
        ];
    }

    public function map($reservation): array
    {
        return [
            $reservation->id,
            $reservation->event_name ?? 'N/A',
            $reservation->event_date ? $reservation->event_date->format('Y-m-d') : 'N/A',
            $reservation->user ? $reservation->user->name : 'N/A',
            $reservation->user ? $reservation->user->department : 'N/A',
            $reservation->number_of_persons ?? 0,
            ucfirst($reservation->status ?? 'pending'),
            $reservation->created_at ? $reservation->created_at->format('Y-m-d H:i') : 'N/A',
        ];
    }
}
