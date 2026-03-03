<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'event_name',
        'event_date',
        'event_time',  // This should be a string time like "07:00-10:00"
        'end_date',    // Add this if you want to store date range
        'day_times', // Add this
        'number_of_persons',
        'special_requests',
        'status',
        'decline_reason',
        // Additional fields from your form
        'contact_person',
        'department',
        'address',
        'email',
        'contact_number',
        'venue',
        'project_name',
        'account_code',
        // Legacy fields for backward compatibility
        'date',
        'time',
        'guests',
        // Add these to your Reservation model's $fillable array:
        'receipt_path',
        'receipt_uploaded_at',
        'payment_status', // 'pending', 'paid', 'overdue'
        'payment_requested_at',
        'payment_last_reminder_at',
        'payment_reminder_count',

        'payment_status',
        'or_number'
    ];

    protected $casts = [
        'event_date' => 'date',
        'receipt_uploaded_at' => 'datetime',
        'end_date' => 'date',
        'day_times' => 'array', // Add this cast
        'payment_requested_at' => 'datetime',
        'payment_last_reminder_at' => 'datetime',
        'payment_reminder_count' => 'integer',
        // Don't cast event_time to datetime since it might contain JSON or time range string
        // 'event_time' => 'datetime', // Remove or comment this line
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function additionals()
    {
        return $this->hasMany(ReservationAdditional::class);
    }

    public function getGuestCountAttribute(): int
    {
        $count = $this->guests ?? $this->attendees ?? $this->number_of_persons;
        return (int) ($count ?? 1);
    }
    
    public function scopeStatus($q, $status)
    {
        if (in_array($status, ['pending','approved','declined'], true)) {
            $q->where('status', $status);
        }
        return $q;
    }

    public function emailScheduleSummary(): string
    {
        if (! $this->event_date) {
            return 'Not provided';
        }

        $startDate = Carbon::parse($this->event_date)->startOfDay();
        $endDate = $this->end_date
            ? Carbon::parse($this->end_date)->startOfDay()
            : $startDate->copy();

        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy();
        }

        $dayTimes = $this->normalizedDayTimes();
        $scheduleLines = [];
        $totalDays = $startDate->diffInDays($endDate) + 1;

        for ($dayIndex = 0; $dayIndex < $totalDays; $dayIndex++) {
            $currentDate = $startDate->copy()->addDays($dayIndex);
            [$startTime, $endTime] = $this->resolveScheduleTimes($currentDate->format('Y-m-d'), $dayTimes, $dayIndex === 0);

            $line = $currentDate->format('M j, Y');
            $timeLabel = $this->formattedTimeRange($startTime, $endTime);

            if ($timeLabel !== null) {
                $line .= ' at '.$timeLabel;
            }

            $scheduleLines[] = $line;
        }

        return implode("\n", $scheduleLines);
    }

    protected function normalizedDayTimes(): array
    {
        if (is_array($this->day_times)) {
            return $this->day_times;
        }

        if (is_string($this->day_times)) {
            $decoded = json_decode($this->day_times, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    protected function resolveScheduleTimes(string $dateKey, array $dayTimes, bool $isFirstDay): array
    {
        $startTime = null;
        $endTime = null;

        if (isset($dayTimes[$dateKey])) {
            $timeData = $dayTimes[$dateKey];

            if (is_array($timeData)) {
                $startTime = $timeData['start_time'] ?? $timeData['start'] ?? $timeData['time_start'] ?? null;
                $endTime = $timeData['end_time'] ?? $timeData['end'] ?? $timeData['time_end'] ?? null;
            } elseif (is_string($timeData)) {
                [$startTime, $endTime] = $this->splitTimeRange($timeData);
            }
        }

        if ($isFirstDay && $startTime === null && $endTime === null) {
            [$startTime, $endTime] = $this->splitTimeRange($this->event_time ?? $this->time ?? null);
        }

        return [$startTime, $endTime];
    }

    protected function splitTimeRange(?string $range): array
    {
        if ($range === null) {
            return [null, null];
        }

        $range = trim($range);

        if ($range === '') {
            return [null, null];
        }

        if (str_contains($range, '-')) {
            $parts = preg_split('/\s*-\s*/', $range);

            return [
                $parts[0] ?? null,
                $parts[1] ?? null,
            ];
        }

        return [$range, null];
    }

    protected function formattedTimeRange(?string $startTime, ?string $endTime): ?string
    {
        $formattedStart = $this->formatEmailTime($startTime);
        $formattedEnd = $this->formatEmailTime($endTime);

        if ($formattedStart === null && $formattedEnd === null) {
            return null;
        }

        if ($formattedStart !== null && $formattedEnd !== null) {
            return $formattedStart.' - '.$formattedEnd;
        }

        return $formattedStart ?? $formattedEnd;
    }

    protected function formatEmailTime(?string $timeString): ?string
    {
        if ($timeString === null) {
            return null;
        }

        $timeString = trim($timeString);

        if ($timeString === '') {
            return null;
        }

        if (preg_match('/^\d{1,2}$/', $timeString)) {
            $timeString .= ':00';
        }

        foreach (['H:i:s', 'H:i', 'g:i A', 'g:iA', 'g A', 'gA'] as $format) {
            try {
                $dateTime = Carbon::createFromFormat($format, $timeString);

                if ($dateTime !== false) {
                    return $dateTime->format('g:i A');
                }
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($timeString)->format('g:i A');
        } catch (\Throwable) {
            return $timeString;
        }
    }
}
