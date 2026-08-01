<?php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    // Шаг 1: Отправка SMS кода
    public function sendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Введите номер телефона'
            ], 422);
        }

        $phone = $this->formatPhone($request->phone);
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Клиент',
                'phone' => $phone,
                'email' => null,
                'password' => Hash::make(str()->random(16)),
                'role' => 'client',
                'phone_verified_at' => now(),
            ]);

            Log::info('New user created via login: ' . $phone);
        }

        $code = random_int(100000, 999999);

        cache()->put("login_code_{$phone}", [
            'code' => $code,
            'user_id' => $user->id
        ], 300);

        Log::info("Login code for {$phone}: {$code}");

        return response()->json([
            'success' => true,
            'message' => $user->wasRecentlyCreated ? 'Аккаунт создан! Код отправлен' : 'Код отправлен',
            'code' => $code
        ]);
    }

    // Шаг 2: Проверка кода и вход
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Введите код'
            ], 422);
        }

        $phone = $this->formatPhone($request->phone);
        $cached = cache()->get("login_code_{$phone}");

        if (!$cached || $cached['code'] != $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный код подтверждения'
            ], 400);
        }

        $user = User::find($cached['user_id']);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден'
            ], 404);
        }

        // ============================================
        // ПРИВЯЗКА ЗАПИСЕЙ К ПОЛЬЗОВАТЕЛЮ
        // ============================================
        $this->attachAppointmentsToUser($user);

        Auth::login($user);

        cache()->forget("login_code_{$phone}");

        $redirect = $user->role === 'client'
            ? route('client.dashboard')
            : route('dashboard');

        return response()->json([
            'success' => true,
            'message' => 'Вход выполнен',
            'redirect' => $redirect
        ]);
    }

    /**
     * Привязка записей к пользователю при входе
     */
    private function attachAppointmentsToUser(User $user): void
    {
        // 1. Находим все записи с этим номером телефона, где client_id = null
        $guestAppointments = Appointment::where('client_phone', $user->phone)
            ->whereNull('client_id')
            ->get();

        foreach ($guestAppointments as $appointment) {
            $client = Client::firstOrCreate(
                [
                    'phone' => $user->phone,
                    'business_id' => $appointment->business_id,
                ],
                [
                    'first_name' => $appointment->client_name ?? $user->name,
                    'email' => $user->email,
                ]
            );

            $appointment->update([
                'client_id' => $client->id,
                'client_phone' => null,
                'client_name' => null,
            ]);

            Log::info("Appointment {$appointment->id} attached to client {$client->id}");
        }

        // 2. Находим все записи, где client_id = user_id (если пользователь уже создан как клиент)
        $userAppointments = Appointment::where('client_id', $user->id)->get();

        foreach ($userAppointments as $appointment) {
            // Проверяем, есть ли клиент в этом бизнесе
            $client = Client::where('phone', $user->phone)
                ->where('business_id', $appointment->business_id)
                ->first();

            if (!$client) {
                // Создаем клиента в этом бизнесе
                $client = Client::create([
                    'business_id' => $appointment->business_id,
                    'first_name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                ]);

                // Обновляем запись, привязывая к клиенту
                $appointment->update([
                    'client_id' => $client->id,
                ]);

                Log::info("Client {$client->id} created for user {$user->id}, appointment {$appointment->id} attached");
            }
        }
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 11 && $phone[0] === '8') {
            $phone = '7' . substr($phone, 1);
        }

        if (strlen($phone) === 11 && $phone[0] === '7') {
            return $phone;
        }

        if (strlen($phone) === 10) {
            return '7' . $phone;
        }

        return $phone;
    }
}
