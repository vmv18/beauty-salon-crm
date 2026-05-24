<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'day_of_week',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'is_working',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_working' => 'boolean',
        ];
    }

    /**
     * Отримати майстра, до якого належить цей розклад.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Отримати назву дня тижня.
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            1 => 'Понеділок',
            2 => 'Вівторок',
            3 => 'Середа',
            4 => 'Четвер',
            5 => 'П\'ятниця',
            6 => 'Субота',
            7 => 'Неділя',
        ];

        return $days[$this->day_of_week] ?? 'Невідомо';
    }

    /**
     * Перевірка, чи майстер працює в цей день.
     */
    public function isWorkingDay(): bool
    {
        return $this->is_working;
    }

    /**
     * Перевірка, чи час попадає в обідню перерву.
     */
    public function isBreakTime($time): bool
    {
        if (!$this->break_start || !$this->break_end) {
            return false;
        }

        $timeObj = \Carbon\Carbon::parse($time);
        $breakStart = \Carbon\Carbon::parse($this->break_start);
        $breakEnd = \Carbon\Carbon::parse($this->break_end);

        return $timeObj->gte($breakStart) && $timeObj->lt($breakEnd);
    }

    /**
     * Перевірка, чи час попадає в робочі години.
     */
    public function isWorkingTime($time): bool
    {
        if (!$this->is_working || !$this->start_time || !$this->end_time) {
            return false;
        }

        $timeObj = \Carbon\Carbon::parse($time);
        $startTime = \Carbon\Carbon::parse($this->start_time);
        $endTime = \Carbon\Carbon::parse($this->end_time);

        return $timeObj->gte($startTime) && $timeObj->lt($endTime);
    }

    /**
     * Scope для робочих днів.
     */
    public function scopeWorking($query)
    {
        return $query->where('is_working', true);
    }

    /**
     * Scope для вихідних днів.
     */
    public function scopeNonWorking($query)
    {
        return $query->where('is_working', false);
    }

    /**
     * Scope для конкретного дня тижня.
     */
    public function scopeForDay($query, int $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Scope для конкретного майстра.
     */
    public function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}
