<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'image',
        'sort_order',
    ];

    /**
     * Отримати послуги цієї категорії.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    /**
     * Отримати активні послуги цієї категорії.
     */
    public function activeServices(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id')->where('is_active', true);
    }

    /**
     * Scope для сортування за порядком.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
