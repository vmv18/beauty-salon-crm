<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'specialization',
        'bio',
        'photo',
        'rating',
        'hire_date',
        'status',
        'work_start_time',
        'work_end_time',
        'min_break_between_appointments',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'decimal:2',
            'hire_date' => 'date',
        ];
    }

    /**
     * Отримати користувача, пов'язаного з майстром.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Отримати послуги, які надає майстер (many-to-many).
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'employee_service')
                    ->withTimestamps();
    }

    /**
     * Перевірка, чи майстер активний.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope для активних майстрів.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope для неактивних майстрів.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope для майстрів у відпустці.
     */
    public function scopeOnLeave($query)
    {
        return $query->where('status', 'on_leave');
    }

    /**
     * Scope для сортування за рейтингом.
     */
    public function scopeOrderByRating($query, $direction = 'desc')
    {
        return $query->orderBy('rating', $direction);
    }

    /**
     * Отримати записи майстра.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Отримати розклад роботи майстра.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Отримати розклад для конкретного дня тижня.
     */
    public function scheduleForDay(int $dayOfWeek): ?Schedule
    {
        return $this->schedules()->where('day_of_week', $dayOfWeek)->first();
    }

    /**
     * Отримати блокування часу майстра.
     */
    public function timeBlocks(): HasMany
    {
        return $this->hasMany(TimeBlock::class);
    }

    /**
     * Отримати відгуки про майстра.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
