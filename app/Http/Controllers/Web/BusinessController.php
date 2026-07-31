<?php


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public function create()
    {
        return view('businesses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $business = Business::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'region' => $validated['region'],
            'description' => $validated['description'],
            'trial_ends_at' => now()->addDays(30),
        ]);

        $request->user()->update(['current_business_id' => $business->id]);

        return redirect()->route('dashboard')
            ->with('success', 'Business created successfully!');
    }
}
