<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeBlock extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'type',
        'reason',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Отримати майстра, до якого належить це блокування.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Перевірка, чи дата попадає в період блокування.
     */
    public function coversDate($date): bool
    {
        $dateObj = \Carbon\Carbon::parse($date);
        $startDate = \Carbon\Carbon::parse($this->start_date);
        $endDate = \Carbon\Carbon::parse($this->end_date);

        return $dateObj->gte($startDate) && $dateObj->lte($endDate);
    }

    /**
     * Перевірка, чи час попадає в блокування.
     */
    public function coversTime($date, $time): bool
    {
        if (!$this->coversDate($date)) {
            return false;
        }

        // Якщо start_time та end_time null - весь день заблоковано
        if (!$this->start_time || !$this->end_time) {
            return true;
        }

        $timeObj = \Carbon\Carbon::parse($time);
        $blockStart = \Carbon\Carbon::parse($this->start_time);
        $blockEnd = \Carbon\Carbon::parse($this->end_time);

        return $timeObj->gte($blockStart) && $timeObj->lt($blockEnd);
    }

    /**
     * Scope для активних блокувань на дату.
     */
    public function scopeActiveOnDate($query, $date)
    {
        return $query->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date);
    }

    /**
     * Scope для конкретного майстра.
     */
    public function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope для конкретного типу.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
