<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Service;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\V1\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    use ApiResponseTrait;

    protected function getBusiness(Request $request): Business
    {
        $businessId = $request->user()->current_business_id;
        $business = Business::findOrFail($businessId);

        if (!$request->user()->hasBusinessAccess($businessId)) {
            abort(403, 'You do not have access to this business');
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
            ->orderBy('name')
            ->paginate(15);

        return $this->paginatedResponse($services, ServiceResource::class);
    }

    public function store(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
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
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $service = $business->services()->create($request->all());

        return $this->successResponse(
            new ServiceResource($service),
            'Service created successfully',
            201
        );
    }

    public function show(Request $request, Service $service)
    {
        $business = $this->getBusiness($request);

        if ($service->business_id !== $business->id) {
            return $this->errorResponse('Service not found in this business', 404);
        }

        return $this->successResponse(new ServiceResource($service));
    }

    public function update(Request $request, Service $service)
    {
        $business = $this->getBusiness($request);

        if ($service->business_id !== $business->id) {
            return $this->errorResponse('Service not found in this business', 404);
        }

        $validator = Validator::make($request->all(), [
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
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $service->update($request->all());

        return $this->successResponse(
            new ServiceResource($service),
            'Service updated successfully'
        );
    }

    public function destroy(Request $request, Service $service)
    {
        $business = $this->getBusiness($request);

        if ($service->business_id !== $business->id) {
            return $this->errorResponse('Service not found in this business', 404);
        }

        // Check if service has future appointments
        $hasFutureAppointments = $service->appointments()
            ->where('start_time', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasFutureAppointments) {
            return $this->errorResponse(
                'Cannot delete service with future appointments',
                422
            );
        }

        $service->delete();

        return $this->successResponse(null, 'Service deleted successfully');
    }
}
