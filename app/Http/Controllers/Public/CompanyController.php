<?php
// app/Http/Controllers/Public/CompanyController.php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $businesses = Business::withCount(['clients', 'appointments'])
            ->where('status', 'active')
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->search;
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                        ->orWhere('city', 'ilike', "%{$search}%")
                        ->orWhere('region', 'ilike', "%{$search}%");
                });
            })
            ->when($request->has('region'), function ($query) use ($request) {
                return $query->where('region', $request->region);
            })
            ->latest()
            ->paginate(12);

        $regions = Business::where('status', 'active')
            ->distinct()
            ->pluck('region')
            ->filter()
            ->values();

        return view('public.companies', compact('businesses', 'regions'));
    }

    public function show(Request $request, string $slug)
    {
        $business = Business::where('slug', $slug)
            ->where('status', 'active')
            ->with(['services' => function ($query) {
                $query->where('is_active', true);
            }, 'employees' => function ($query) {
                $query->where('is_active', true);
            }])
            ->firstOrFail();

        return view('public.company', compact('business'));
    }
}
