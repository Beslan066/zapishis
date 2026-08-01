<?php
// app/Http/Controllers/Auth/RegisteredUserController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Client;
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
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:client,business'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $code = random_int(100000, 999999);

        cache()->put("registration_code_{$request->phone}", [
            'code' => $code,
            'data' => $request->only(['name', 'phone', 'email', 'password', 'role'])
        ], 300);

        $notificationService = app(NotificationService::class);
        $notificationService->sendVerificationCode($request->phone, $code);

        return response()->json([
            'success' => true,
            'message' => 'Код отправлен',
            'code' => $code
        ]);
    }

    // Шаг 2: Проверка SMS кода и регистрация
    public function verifyPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => ['required', 'in:client,business'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cached = cache()->get("registration_code_{$request->phone}");

        if (!$cached || $cached['code'] != $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный код подтверждения'
            ], 400);
        }

        $userData = $cached['data'];

        $user = User::create([
            'name' => $userData['name'],
            'phone' => $userData['phone'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'role' => $userData['role'],
            'phone_verified_at' => now(),
        ]);

        cache()->forget("registration_code_{$request->phone}");

        // ============================================
        // ПРИВЯЗКА ЗАПИСЕЙ К ПОЛЬЗОВАТЕЛЮ
        // ============================================
        $this->attachAppointmentsToUser($user);

        event(new Registered($user));

        Auth::login($user);

        $redirect = $user->role === 'client'
            ? route('client.dashboard')
            : route('dashboard');

        return response()->json([
            'success' => true,
            'message' => 'Регистрация успешна',
            'redirect' => $redirect
        ]);
    }

    /**
     * Привязка гостевых записей к пользователю
     */
    private function attachAppointmentsToUser(User $user): void
    {
        // Находим все записи с этим номером телефона, где client_id = null
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
        }

        // Также проверяем записи, где client_id = user_id
        $userAppointments = Appointment::where('client_id', $user->id)->get();

        foreach ($userAppointments as $appointment) {
            $client = Client::where('phone', $user->phone)
                ->where('business_id', $appointment->business_id)
                ->first();

            if (!$client) {
                $client = Client::create([
                    'business_id' => $appointment->business_id,
                    'first_name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                ]);

                $appointment->update([
                    'client_id' => $client->id,
                ]);
            }
        }
    }
}
