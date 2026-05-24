<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Додаємо кастомні Blade директиви для ролей
        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        Blade::if('anyRole', function (...$roles) {
            if (!auth()->check()) {
                return false;
            }
            
            foreach ($roles as $role) {
                if (auth()->user()->hasRole($role)) {
                    return true;
                }
            }
            
            return false;
        });

        Blade::if('allRoles', function (...$roles) {
            if (!auth()->check()) {
                return false;
            }
            
            foreach ($roles as $role) {
                if (!auth()->user()->hasRole($role)) {
                    return false;
                }
            }
            
            return true;
        });
    }
}
