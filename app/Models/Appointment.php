<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'employee_id',
        'service_id',
        'appointment_date',
        'appointment_time',
        'duration',
        'price',
        'status',
        'notes',
        'cancellation_reason',
        'photo_before',
        'photo_after',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'price' => 'decimal:2',
        ];
    }

    /**
     * Accessor для appointment_time - завжди повертає тільки час у форматі H:i:s
     *
     * @param mixed $value
     * @return string|null
     */
    public function getAppointmentTimeAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        
        // Якщо це вже час у форматі H:i:s або H:i, повернути як є
        if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value)) {
            // Додати секунди, якщо їх немає
            if (strlen($value) === 5) {
                return $value . ':00';
            }
            return $value;
        }
        
        // Якщо містить пробіли, це може бути datetime або подвійний час
        // Приклад: "2025-12-17 00:00:00 13:30:00" -> "13:30:00"
        if (strpos($value, ' ') !== false) {
            // Спробувати витягнути останній час з рядка (останній елемент після пробілів)
            $parts = preg_split('/\s+/', trim($value));
            foreach (array_reverse($parts) as $part) {
                // Перевірити, чи це час у форматі H:i:s
                if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $part)) {
                    return $part;
                }
                // Перевірити, чи це час у форматі H:i
                if (preg_match('/^\d{2}:\d{2}$/', $part)) {
                    return $part . ':00';
                }
            }
        }
        
        // Якщо це datetime формат або містить подвійний час, витягнути тільки останній час
        if (preg_match('/(\d{2}:\d{2}:\d{2})\s*$/', $value, $matches)) {
            return $matches[1];
        }
        
        // Спробувати витягнути час з datetime формату
        if (preg_match('/(\d{2}:\d{2}:\d{2})/', $value, $matches)) {
            return $matches[1];
        }
        
        // Якщо не вдалося розпарсити, спробувати через Carbon (тільки якщо це не містить подвійний час)
        try {
            // Якщо містить більше одного часу, не парсити через Carbon
            $timeMatches = preg_match_all('/(\d{2}:\d{2}:\d{2}|\d{2}:\d{2})/', $value);
            if ($timeMatches > 1) {
                \Log::warning('Multiple time formats found in appointment_time: ' . $value);
                // Повернути останній знайдений час
                if (preg_match_all('/(\d{2}:\d{2}:\d{2}|\d{2}:\d{2})/', $value, $allMatches)) {
                    $lastMatch = end($allMatches[0]);
                    if (strlen($lastMatch) === 5) {
                        return $lastMatch . ':00';
                    }
                    return $lastMatch;
                }
            }
            
            // Спробувати розпарсити як datetime
            $parsed = \Carbon\Carbon::parse($value);
            return $parsed->format('H:i:s');
        } catch (\Exception $e) {
            // Якщо не вдалося розпарсити, повернути як є
            \Log::warning('Failed to parse appointment_time: ' . $value, ['exception' => $e->getMessage()]);
            return $value;
        }
    }

    /**
     * Mutator для appointment_time - завжди зберігає тільки час у форматі H:i:s
     *
     * @param mixed $value
     * @return void
     */
    public function setAppointmentTimeAttribute($value): void
    {
        if (!$value) {
            $this->attributes['appointment_time'] = null;
            return;
        }
        
        // Якщо це вже час у форматі H:i:s або H:i, зберегти як є
        if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value)) {
            // Додати секунди, якщо їх немає
            if (strlen($value) === 5) {
                $this->attributes['appointment_time'] = $value . ':00';
            } else {
                $this->attributes['appointment_time'] = $value;
            }
            return;
        }
        
        // Якщо це datetime формат або містить подвійний час, витягнути тільки останній час
        // Приклад: "2025-12-17 00:00:00 13:30:00" -> "13:30:00"
        if (preg_match('/(\d{2}:\d{2}:\d{2})\s*$/', $value, $matches)) {
            $this->attributes['appointment_time'] = $matches[1];
            return;
        }
        
        // Спробувати витягнути час з datetime формату
        if (preg_match('/(\d{2}:\d{2}:\d{2})/', $value, $matches)) {
            $this->attributes['appointment_time'] = $matches[1];
            return;
        }
        
        // Якщо містить пробіли, спробувати витягнути останній час
        if (strpos($value, ' ') !== false) {
            $parts = explode(' ', trim($value));
            foreach (array_reverse($parts) as $part) {
                if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $part)) {
                    $this->attributes['appointment_time'] = $part;
                    return;
                }
                if (preg_match('/^\d{2}:\d{2}$/', $part)) {
                    $this->attributes['appointment_time'] = $part . ':00';
                    return;
                }
            }
        }
        
        // Спробувати розпарсити через Carbon
        try {
            $parsed = \Carbon\Carbon::parse($value);
            $this->attributes['appointment_time'] = $parsed->format('H:i:s');
        } catch (\Exception $e) {
            // Якщо не вдалося розпарсити, зберегти як є (з логуванням)
            \Log::warning('Failed to parse appointment_time for saving: ' . $value, ['exception' => $e->getMessage()]);
            $this->attributes['appointment_time'] = $value;
        }
    }

    /**
     * Отримати appointment_time як Carbon об'єкт з правильною датою.
     *
     * @param string|null $date Дата для об'єднання з часом (за замовчуванням appointment_date)
     * @return \Carbon\Carbon|null
     */
    public function getAppointmentDateTime($date = null): ?\Carbon\Carbon
    {
        if (!$this->appointment_time) {
            return null;
        }

        $date = $date ?? $this->appointment_date;
        
        // Нормалізувати дату - завжди тільки дата без часу
        if ($date instanceof \Carbon\Carbon) {
            $dateStr = $date->format('Y-m-d');
        } else {
            $dateStr = \Carbon\Carbon::parse($date)->format('Y-m-d');
        }
        
        // appointment_time вже буде у форматі H:i:s завдяки accessor
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dateStr . ' ' . $this->appointment_time);
    }

    /**
     * Отримати клієнта запису.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Отримати майстра запису.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Отримати послугу запису.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Перевірка, чи запис запланований.
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Перевірка, чи запис підтверджений.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Перевірка, чи запис завершений.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Перевірка, чи запис скасований.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Scope для записів за статусом.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope для записів на певну дату.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->where('appointment_date', $date);
    }

    /**
     * Scope для записів певного майстра.
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope для записів певного клієнта.
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope для майбутніх записів.
     */
    public function scopeUpcoming($query)
    {
        $today = now()->toDateString();
        $now = now()->toTimeString();
        
        return $query->where(function ($q) use ($today, $now) {
            $q->where('appointment_date', '>', $today)
              ->orWhere(function ($q2) use ($today, $now) {
                  $q2->where('appointment_date', $today)
                     ->where('appointment_time', '>=', $now);
              });
        })->whereIn('status', ['scheduled', 'confirmed']);
    }

    /**
     * Scope для минулих записів.
     */
    public function scopePast($query)
    {
        $today = now()->toDateString();
        $now = now()->toTimeString();
        
        return $query->where(function ($q) use ($today, $now) {
            $q->where('appointment_date', '<', $today)
              ->orWhere(function ($q2) use ($today, $now) {
                  $q2->where('appointment_date', $today)
                     ->where('appointment_time', '<', $now);
              });
        });
    }

    /**
     * Отримати платежі для цього запису.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Перевірка, чи запис оплачено повністю.
     */
    public function isPaid(): bool
    {
        $totalPaid = $this->payments()
            ->where('status', 'completed')
            ->sum('amount');

        return $totalPaid >= $this->price;
    }

    /**
     * Отримати залишок до оплати.
     */
    public function getRemainingAmountAttribute(): float
    {
        $totalPaid = $this->payments()
            ->where('status', 'completed')
            ->sum('amount');

        return max(0, $this->price - $totalPaid);
    }

    /**
     * Отримати відгук для запису.
     */
    public function review(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
