<?php

// app/Http/Controllers/Api/V1/AuthController.php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Register via phone (Laravel 12 native)
     *
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     summary="Register new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","phone","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string", minLength=8)
     *         )
     *     ),
     *     @OA\Response(response="201", description="User registered")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone|regex:/^\+?[0-9]{10,15}$/',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'User registered successfully', 201);
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        if (!Auth::attempt($request->only('phone', 'password'))) {
            throw ValidationException::withMessages([
                'phone' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('phone', $request->phone)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return $this->successResponse([
            'user' => $request->user()->load('currentBusiness'),
        ]);
    }

    /**
     * Send verification code to phone
     */
    public function sendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        // Generate 6-digit code
        $code = random_int(100000, 999999);

        // Store in cache
        cache()->put("verification_code_{$request->phone}", $code, 300); // 5 minutes

        // TODO: Send SMS via SMS service
        // For now, return code for testing
        return $this->successResponse([
            'code' => $code, // Remove in production
        ], 'Verification code sent');
    }

    /**
     * Verify phone
     */
    public function verifyPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $cachedCode = cache()->get("verification_code_{$request->phone}");

        if (!$cachedCode || $cachedCode != $request->code) {
            return $this->errorResponse('Invalid verification code', 400);
        }

        $user = User::where('phone', $request->phone)->first();
        $user->update(['phone_verified_at' => now()]);

        cache()->forget("verification_code_{$request->phone}");

        return $this->successResponse(null, 'Phone verified successfully');
    }
}
