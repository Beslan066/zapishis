<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем, что пользователь авторизован
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Проверяем, что телефон подтвержден
        if (!$request->user()->hasVerifiedPhone()) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Пожалуйста, подтвердите ваш номер телефона');
        }

        return $next($request);
    }
}
