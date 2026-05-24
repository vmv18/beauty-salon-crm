<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'appointment_id',
        'client_id',
        'amount',
        'payment_method',
        'payment_date',
        'status',
        'notes',
        'document',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    /**
     * Отримати запис, пов'язаний з платежем.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Отримати клієнта, який здійснив платіж.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Перевірка, чи платіж очікує обробки.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Перевірка, чи платіж завершений.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Перевірка, чи платіж не вдався.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Перевірка, чи платіж повернуто.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Отримати назву способу оплати.
     */
    public function getPaymentMethodNameAttribute(): string
    {
        $methods = [
            'cash' => 'Готівка',
            'card' => 'Картка',
            'online' => 'Онлайн',
        ];

        return $methods[$this->payment_method] ?? 'Невідомо';
    }

    /**
     * Отримати назву статусу.
     */
    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'pending' => 'Очікує',
            'completed' => 'Завершено',
            'failed' => 'Не вдався',
            'refunded' => 'Повернено',
        ];

        return $statuses[$this->status] ?? 'Невідомо';
    }

    /**
     * Scope для платежів за статусом.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope для завершених платежів.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope для очікуючих платежів.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope для платежів за способом оплати.
     */
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope для платежів за датою.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->where('payment_date', $date);
    }

    /**
     * Scope для платежів за діапазоном дат.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope для платежів конкретного клієнта.
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope для платежів конкретного запису.
     */
    public function scopeForAppointment($query, $appointmentId)
    {
        return $query->where('appointment_id', $appointmentId);
    }
}
