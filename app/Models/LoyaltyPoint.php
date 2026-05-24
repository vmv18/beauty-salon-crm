<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPoint extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'appointment_id',
        'points',
        'type',
        'description',
        'balance_after',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'balance_after' => 'integer',
        ];
    }

    /**
     * Отримати клієнта.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Отримати запис, пов'язаний з операцією.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Scope для зароблених балів.
     */
    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    /**
     * Scope для витрачених балів.
     */
    public function scopeSpent($query)
    {
        return $query->where('type', 'spent');
    }

    /**
     * Scope для застарілих балів.
     */
    public function scopeExpired($query)
    {
        return $query->where('type', 'expired');
    }
}
