<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'phone',
        'email',
        'date_of_birth',
        'gender',
        'address',
        'notes',
        'status',
        'photo',
        'loyalty_points',
        'loyalty_level',
        'total_loyalty_points_earned',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'loyalty_points' => 'integer',
            'total_loyalty_points_earned' => 'integer',
        ];
    }

    /**
     * Отримати користувача, пов'язаного з клієнтом.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Отримати записи клієнта.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Отримати платежі клієнта.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Отримати відгуки клієнта.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Отримати історію балів лояльності.
     */
    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    /**
     * Перевірка, чи клієнт активний.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope для активних клієнтів.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope для неактивних клієнтів.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Нарахувати бали лояльності.
     */
    public function addLoyaltyPoints(int $points, ?Appointment $appointment = null, ?string $description = null): void
    {
        DB::beginTransaction();
        try {
            $this->refresh();
            $newBalance = $this->loyalty_points + $points;
            
            // Оновити баланс
            $this->update([
                'loyalty_points' => $newBalance,
                'total_loyalty_points_earned' => $this->total_loyalty_points_earned + $points,
            ]);

            // Створити запис в історії
            LoyaltyPoint::create([
                'client_id' => $this->id,
                'appointment_id' => $appointment?->id,
                'points' => $points,
                'type' => 'earned',
                'description' => $description ?? 'Нарахування балів за послугу',
                'balance_after' => $newBalance,
            ]);

            // Оновити рівень лояльності
            $this->updateLoyaltyLevel();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Витратити бали лояльності.
     */
    public function spendLoyaltyPoints(int $points, ?string $description = null): bool
    {
        if ($this->loyalty_points < $points) {
            return false; // Недостатньо балів
        }

        DB::beginTransaction();
        try {
            $this->refresh();
            $newBalance = $this->loyalty_points - $points;
            
            // Оновити баланс
            $this->update([
                'loyalty_points' => $newBalance,
            ]);

            // Створити запис в історії
            LoyaltyPoint::create([
                'client_id' => $this->id,
                'points' => -$points,
                'type' => 'spent',
                'description' => $description ?? 'Використання балів',
                'balance_after' => $newBalance,
            ]);

            // Оновити рівень лояльності
            $this->updateLoyaltyLevel();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Оновити рівень лояльності на основі загальної кількості зароблених балів.
     */
    public function updateLoyaltyLevel(): void
    {
        $totalEarned = $this->total_loyalty_points_earned;
        $newLevel = 'bronze';

        if ($totalEarned >= 10000) {
            $newLevel = 'gold';
        } elseif ($totalEarned >= 5000) {
            $newLevel = 'silver';
        }

        if ($this->loyalty_level !== $newLevel) {
            $this->update(['loyalty_level' => $newLevel]);
        }
    }

    /**
     * Отримати назву рівня лояльності.
     */
    public function getLoyaltyLevelNameAttribute(): string
    {
        return match($this->loyalty_level) {
            'bronze' => 'Бронзовий',
            'silver' => 'Срібний',
            'gold' => 'Золотий',
            default => 'Бронзовий',
        };
    }

    /**
     * Отримати знижку на основі рівня лояльності.
     */
    public function getLoyaltyDiscountAttribute(): float
    {
        return match($this->loyalty_level) {
            'bronze' => 0.0, // Без знижки
            'silver' => 5.0, // 5% знижка
            'gold' => 10.0,  // 10% знижка
            default => 0.0,
        };
    }

    /**
     * Конвертувати бали в знижку (1 бал = 0.1 грн).
     */
    public function convertPointsToDiscount(int $points): float
    {
        return $points * 0.1; // 1 бал = 0.1 грн
    }

    /**
     * Отримати кількість балів, необхідних для знижки.
     */
    public function getPointsForDiscount(float $discountAmount): int
    {
        return (int) ceil($discountAmount / 0.1); // 0.1 грн за бал
    }
}

