<?php
// app/Http/Middleware/EnsureUserIsBusinessOwner.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsBusinessOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Если у пользователя нет бизнеса - редирект на создание
        if ($user->businesses()->count() === 0) {
            return redirect()->route('businesses.create')
                ->with('info', 'Сначала создайте бизнес');
        }

        // Если нет текущего бизнеса - устанавливаем первый
        if (!$user->current_business_id) {
            $firstBusiness = $user->businesses()->first();
            $user->update(['current_business_id' => $firstBusiness->id]);
        }

        $businessId = $request->route('business') ?? $request->input('business_id');

        // Если в запросе есть ID бизнеса - проверяем доступ
        if ($businessId && !$user->hasBusinessAccess($businessId)) {
            abort(403, 'У вас нет доступа к этому бизнесу');
        }

        return $next($request);
    }
}
