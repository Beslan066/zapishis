<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Client;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\V1\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
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

        $clients = $business->clients()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->search;
                return $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->has('phone'), function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            })
            ->when($request->has('email'), function ($query) use ($request) {
                return $query->where('email', $request->email);
            })
            ->withCount(['appointments'])
            ->orderBy('last_visit_at', 'desc')
            ->orderBy('total_visits', 'desc')
            ->paginate(15);

        return $this->paginatedResponse($clients, ClientResource::class);
    }

    public function store(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'birthday' => 'nullable|date',
            'instagram' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        // Check if client already exists
        $existingClient = $business->clients()
            ->where('phone', $request->phone)
            ->first();

        if ($existingClient) {
            return $this->errorResponse(
                'Client with this phone already exists',
                422,
                ['phone' => ['Client with this phone already exists']]
            );
        }

        $client = $business->clients()->create($request->all());

        return $this->successResponse(
            new ClientResource($client),
            'Client created successfully',
            201
        );
    }

    public function show(Request $request, Client $client)
    {
        $business = $this->getBusiness($request);

        if ($client->business_id !== $business->id) {
            return $this->errorResponse('Client not found in this business', 404);
        }

        $client->load(['appointments' => function ($query) {
            $query->orderBy('start_time', 'desc')
                ->limit(10)
                ->with(['service', 'employee']);
        }]);

        return $this->successResponse(new ClientResource($client));
    }

    public function update(Request $request, Client $client)
    {
        $business = $this->getBusiness($request);

        if ($client->business_id !== $business->id) {
            return $this->errorResponse('Client not found in this business', 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'nullable|email|max:255',
            'birthday' => 'nullable|date',
            'instagram' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $client->update($request->all());

        return $this->successResponse(
            new ClientResource($client),
            'Client updated successfully'
        );
    }

    public function destroy(Request $request, Client $client)
    {
        $business = $this->getBusiness($request);

        if ($client->business_id !== $business->id) {
            return $this->errorResponse('Client not found in this business', 404);
        }

        // Check if client has future appointments
        $hasFutureAppointments = $client->appointments()
            ->where('start_time', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasFutureAppointments) {
            return $this->errorResponse(
                'Cannot delete client with future appointments',
                422
            );
        }

        $client->delete();

        return $this->successResponse(null, 'Client deleted successfully');
    }

    public function history(Request $request, Client $client)
    {
        $business = $this->getBusiness($request);

        if ($client->business_id !== $business->id) {
            return $this->errorResponse('Client not found in this business', 404);
        }

        $appointments = $client->appointments()
            ->with(['service', 'employee'])
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        return $this->paginatedResponse($appointments, \App\Http\Resources\V1\AppointmentResource::class);
    }

    public function search(Request $request)
    {
        $business = $this->getBusiness($request);
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $clients = $business->clients()
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'ilike', "%{$query}%")
                    ->orWhere('last_name', 'ilike', "%{$query}%")
                    ->orWhere('phone', 'ilike', "%{$query}%")
                    ->orWhere('email', 'ilike', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'phone', 'email']);

        return response()->json($clients);
    }
}
