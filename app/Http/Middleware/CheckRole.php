<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Если пользователь клиент, а пытается зайти в бизнес-часть
        if ($role === 'business' && $request->user()->isClient()) {
            return redirect()->route('client.dashboard')
                ->with('error', 'У вас нет доступа к бизнес-панели');
        }

        // Если пользователь бизнес, а пытается зайти в клиентскую часть
        if ($role === 'client' && $request->user()->isBusiness()) {
            return redirect()->route('dashboard')
                ->with('error', 'У вас нет доступа к клиентской панели');
        }

        return $next($request);
    }
}
