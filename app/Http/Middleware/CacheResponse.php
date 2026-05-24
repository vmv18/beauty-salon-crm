<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $minutes = 60): Response
    {
        // Кешуємо тільки GET запити
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Не кешуємо автентифікованих користувачів
        if (auth()->check()) {
            return $next($request);
        }

        // Генеруємо ключ кешу на основі URL та параметрів
        $key = 'response:' . md5($request->fullUrl());

        return Cache::remember($key, now()->addMinutes($minutes), function () use ($next, $request) {
            return $next($request);
        });
    }
}

