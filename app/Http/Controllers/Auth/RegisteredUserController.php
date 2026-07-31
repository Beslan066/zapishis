<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    // Шаг 1: Отправка SMS кода
    public function sendPhoneCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:client,business'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Генерация кода
        $code = random_int(100000, 999999);

        cache()->put("registration_code_{$request->phone}", [
            'code' => $code,
            'data' => $request->only(['name', 'phone', 'email', 'password', 'role'])
        ], 300);

        // Отправляем SMS через сервис
        $notificationService = app(NotificationService::class);
        $notificationService->sendVerificationCode($request->phone, $code);

        return response()->json([
            'success' => true,
            'message' => 'Код отправлен',
            // Для теста - удалить в продакшене
            'code' => $code
        ]);
    }

    // Шаг 2: Проверка SMS кода и регистрация
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => ['required', 'in:client,business'],
        ]);

        $cached = cache()->get("registration_code_{$request->phone}");

        if (!$cached || $cached['code'] != $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный код подтверждения'
            ], 400);
        }

        $userData = $cached['data'];

        // Создаем пользователя
        $user = User::create([
            'name' => $userData['name'],
            'phone' => $userData['phone'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'phone_verified_at' => now(), // Сразу подтверждаем телефон
        ]);

        // Удаляем код из кэша
        cache()->forget("registration_code_{$request->phone}");

        // Отправляем событие регистрации
        event(new Registered($user));

        // Автоматический вход
        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Регистрация успешна',
            'redirect' => route('dashboard')
        ]);
    }
}
