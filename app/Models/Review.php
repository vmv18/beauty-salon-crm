<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Після створення, оновлення або видалення відгуку - оновити рейтинг майстра
        static::created(function (Review $review) {
            if ($review->is_approved) {
                static::updateEmployeeRating($review->employee_id);
            }
        });

        static::updated(function (Review $review) {
            if ($review->isDirty('is_approved') || $review->isDirty('rating')) {
                static::updateEmployeeRating($review->employee_id);
            }
        });

        static::deleted(function (Review $review) {
            static::updateEmployeeRating($review->employee_id);
        });
    }

    /**
     * Оновити рейтинг майстра на основі схвалених відгуків.
     */
    protected static function updateEmployeeRating(int $employeeId): void
    {
        $employee = Employee::find($employeeId);
        
        if (!$employee) {
            return;
        }

        // Розрахувати середній рейтинг зі схвалених відгуків
        $averageRating = static::where('employee_id', $employeeId)
            ->where('is_approved', true)
            ->avg('rating');

        // Оновити рейтинг майстра (округлити до 2 знаків після коми)
        $employee->update([
            'rating' => round($averageRating ?? 0, 2)
        ]);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'appointment_id',
        'client_id',
        'employee_id',
        'service_id',
        'rating',
        'comment',
        'is_approved',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Отримати запис, пов'язаний з відгуком.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Отримати клієнта, який залишив відгук.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Отримати майстра, на якого залишено відгук.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Отримати послугу, на яку залишено відгук.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Перевірити, чи відгук схвалено.
     */
    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    /**
     * Scope для отримання тільки схвалених відгуків.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope для отримання тільки не схвалених відгуків.
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope для фільтрації по рейтингу.
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope для фільтрації по майстру.
     */
    public function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope для фільтрації по послузі.
     */
    public function scopeForService($query, int $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Scope для фільтрації по клієнту.
     */
    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }
}
