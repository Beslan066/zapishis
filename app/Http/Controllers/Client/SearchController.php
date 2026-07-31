<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Service;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        $category = $request->get('category');
        $region = $request->get('region');

        $services = Service::with('business')
            ->where('is_active', true)
            ->when($query, function ($q) use ($query) {
                return $q->where('name', 'ilike', "%{$query}%")
                    ->orWhereHas('business', function ($b) use ($query) {
                        $b->where('name', 'ilike', "%{$query}%");
                    });
            })
            ->when($category, function ($q) use ($category) {
                return $q->where('category', $category);
            })
            ->when($region, function ($q) use ($region) {
                return $q->whereHas('business', function ($b) use ($region) {
                    $b->where('region', $region);
                });
            })
            ->paginate(12);

        $categories = Service::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $regions = Business::where('status', 'active')
            ->distinct()
            ->pluck('region')
            ->filter()
            ->values();

        return view('clients.search', compact('services', 'categories', 'regions'));
    }
}
