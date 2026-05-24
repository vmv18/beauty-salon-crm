<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Розширюємо масив ролей, розділяючи рядки з комами
        $allRoles = [];
        foreach ($roles as $role) {
            $allRoles = array_merge($allRoles, array_map('trim', explode(',', $role)));
        }

        // Перевіряємо, чи користувач має одну з необхідних ролей
        foreach ($allRoles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Якщо користувач не має необхідної ролі, повертаємо 403 або редирект
        abort(403, 'Доступ заборонено. Необхідна роль: ' . implode(', ', $allRoles));
    }
}
