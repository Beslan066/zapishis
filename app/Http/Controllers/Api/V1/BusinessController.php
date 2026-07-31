<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\V1\BusinessResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $businesses = $request->user()->businesses()
            ->withCount(['services', 'employees', 'clients', 'appointments'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginatedResponse($businesses, BusinessResource::class);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'timezone' => 'nullable|string|timezone',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $business = Business::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'region' => $request->region,
            'description' => $request->description,
            'timezone' => $request->timezone ?? 'Europe/Moscow',
            'trial_ends_at' => now()->addDays(30),
        ]);

        // Set as current business
        $request->user()->update(['current_business_id' => $business->id]);

        return $this->successResponse(
            new BusinessResource($business),
            'Business created successfully',
            201
        );
    }

    public function show(Request $request, Business $business)
    {
        if (!$request->user()->hasBusinessAccess($business->id)) {
            return $this->errorResponse('You do not have access to this business', 403);
        }

        return $this->successResponse(
            new BusinessResource($business->loadCount(['services', 'employees', 'clients', 'appointments']))
        );
    }

    public function update(Request $request, Business $business)
    {
        if (!$request->user()->hasBusinessAccess($business->id)) {
            return $this->errorResponse('You do not have access to this business', 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'address' => 'sometimes|string|max:500',
            'city' => 'sometimes|string|max:100',
            'region' => 'sometimes|string|max:100',
            'description' => 'sometimes|string',
            'timezone' => 'sometimes|string|timezone',
            'settings' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $business->update($request->all());

        return $this->successResponse(
            new BusinessResource($business),
            'Business updated successfully'
        );
    }

    public function destroy(Request $request, Business $business)
    {
        if (!$request->user()->hasBusinessAccess($business->id)) {
            return $this->errorResponse('You do not have access to this business', 403);
        }

        $business->delete();

        return $this->successResponse(null, 'Business deleted successfully');
    }

    public function switch(Request $request, Business $business)
    {
        if (!$request->user()->hasBusinessAccess($business->id)) {
            return $this->errorResponse('You do not have access to this business', 403);
        }

        $request->user()->update(['current_business_id' => $business->id]);

        return $this->successResponse(
            ['current_business_id' => $business->id],
            'Business switched successfully'
        );
    }
}
