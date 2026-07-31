<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    protected function getBusiness(Request $request): Business
    {
        $business = $request->user()->currentBusiness;

        if (!$business) {
            abort(403, 'Please create a business first');
        }

        return $business;
    }

    public function index(Request $request)
    {
        $business = $this->getBusiness($request);

        $services = $business->services()
            ->when($request->has('is_active'), function ($query) use ($request) {
                return $query->where('is_active', $request->boolean('is_active'));
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('services.index', compact('services'));
    }

    public function create(Request $request)
    {
        $business = $this->getBusiness($request);

        return view('services.create', compact('business'));
    }

    public function store(Request $request)
    {
        $business = $this->getBusiness($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'duration_minutes' => 'required|integer|min:5|max:1440',
            'buffer_before_minutes' => 'nullable|integer|min:0',
            'buffer_after_minutes' => 'nullable|integer|min:0',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $business->services()->create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully!');
    }

    public function show(Request $request, Service $service)
    {
        $business = $this->getBusiness($request);

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        $service->load(['employees', 'appointments' => function ($query) {
            $query->orderBy('start_time', 'desc')->limit(10);
        }]);

        return view('services.show', compact('service'));
    }

    public function edit(Request $request, Service $service)
    {
        $business = $this->getBusiness($request);

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $business = $this->getBusiness($request);

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'duration_minutes' => 'sometimes|integer|min:5|max:1440',
            'buffer_before_minutes' => 'nullable|integer|min:0',
            'buffer_after_minutes' => 'nullable|integer|min:0',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully!');
    }

    public function destroy(Request $request, Service $service)
    {
        $business = $this->getBusiness($request);

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        // Check if service has future appointments
        $hasFutureAppointments = $service->appointments()
            ->where('start_time', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasFutureAppointments) {
            return redirect()->route('services.index')
                ->with('error', 'Cannot delete service with future appointments!');
        }

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully!');
    }

    public function toggleActive(Request $request, Service $service)
    {
        $business = $this->getBusiness($request);

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        $service->update(['is_active' => !$service->is_active]);

        return redirect()->route('services.index')
            ->with('success', 'Service status updated!');
    }

    public function reorder(Request $request)
    {
        $business = $this->getBusiness($request);

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:services,id',
        ]);

        foreach ($request->order as $index => $serviceId) {
            $service = $business->services()->find($serviceId);
            if ($service) {
                $service->update(['sort_order' => $index]);
            }
        }

        return response()->json(['success' => true]);
    }
}
