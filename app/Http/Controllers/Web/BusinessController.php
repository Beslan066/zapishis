<?php


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();

        return view('businesses.create', [
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        // Если телефон или email не переданы - берем из профиля
        $business = Business::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'phone' => $validated['phone'] ?? $user->phone,
            'email' => $validated['email'] ?? $user->email,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'region' => $validated['region'] ?? null,
            'description' => $validated['description'] ?? null,
            'trial_ends_at' => now()->addDays(30),
        ]);

        $user->update(['current_business_id' => $business->id]);

        return redirect()->route('dashboard')
            ->with('success', 'Бизнес создан!');
    }
}
