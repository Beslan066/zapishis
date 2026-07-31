<?php
// app/Http/Controllers/Web/ProfileController.php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Профиль обновлен!');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Текущий пароль неверный',
            ])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Пароль обновлен!');
    }

    // Отправка кода для верификации телефона
    public function sendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $code = random_int(100000, 999999);
        cache()->put("verification_code_{$request->phone}", $code, 300);

        // Отправляем SMS через сервис
        $notificationService = app(NotificationService::class);
        $notificationService->sendVerificationCode($request->phone, $code);

        return response()->json([
            'success' => true,
            'message' => 'Код отправлен!',
            // В продакшене код НЕ ВОЗВРАЩАЕМ!
            // 'code' => $code // Только для теста!
        ]);
    }

    // Подтверждение телефона
    public function verifyPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $cachedCode = cache()->get("verification_code_{$request->phone}");

        if (!$cachedCode || $cachedCode != $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный код',
            ], 400);
        }

        $user = User::where('phone', $request->phone)->first();
        $user->markPhoneAsVerified();

        cache()->forget("verification_code_{$request->phone}");

        return response()->json([
            'success' => true,
            'message' => 'Телефон подтвержден!',
        ]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user->delete();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
