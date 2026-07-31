<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Employee;
use App\Models\WorkingHour;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\V1\EmployeeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
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

        $employees = $business->employees()
            ->when($request->has('is_active'), function ($query) use ($request) {
                return $query->where('is_active', $request->boolean('is_active'));
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->with(['workingHours', 'services'])
            ->orderBy('name')
            ->paginate(15);

        return $this->paginatedResponse($employees, EmployeeResource::class);
    }

    public function store(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'position' => 'nullable|string|max:100',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'settings' => 'nullable|array',
            'booking_buffer_minutes' => 'nullable|integer|min:0',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
            'working_hours' => 'nullable|array',
            'working_hours.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'working_hours.*.start_time' => 'required|date_format:H:i',
            'working_hours.*.end_time' => 'required|date_format:H:i|after:working_hours.*.start_time',
            'working_hours.*.break_start' => 'nullable|date_format:H:i',
            'working_hours.*.break_end' => 'nullable|date_format:H:i|after:working_hours.*.break_start',
            'working_hours.*.is_working_day' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $employee = $business->employees()->create($request->except(['service_ids', 'working_hours']));

        // Attach services
        if ($request->has('service_ids')) {
            $employee->services()->attach($request->service_ids);
        }

        // Create working hours
        if ($request->has('working_hours')) {
            foreach ($request->working_hours as $wh) {
                $employee->workingHours()->create($wh);
            }
        }

        return $this->successResponse(
            new EmployeeResource($employee->load(['workingHours', 'services'])),
            'Employee created successfully',
            201
        );
    }

    public function show(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            return $this->errorResponse('Employee not found in this business', 404);
        }

        return $this->successResponse(
            new EmployeeResource($employee->load(['workingHours', 'services']))
        );
    }

    public function update(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            return $this->errorResponse('Employee not found in this business', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'position' => 'nullable|string|max:100',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'settings' => 'nullable|array',
            'booking_buffer_minutes' => 'nullable|integer|min:0',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
            'working_hours' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $employee->update($request->except(['service_ids', 'working_hours']));

        // Sync services
        if ($request->has('service_ids')) {
            $employee->services()->sync($request->service_ids);
        }

        // Update working hours
        if ($request->has('working_hours')) {
            foreach ($request->working_hours as $wh) {
                WorkingHour::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'day_of_week' => $wh['day_of_week'],
                    ],
                    $wh
                );
            }
        }

        return $this->successResponse(
            new EmployeeResource($employee->load(['workingHours', 'services'])),
            'Employee updated successfully'
        );
    }

    public function destroy(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            return $this->errorResponse('Employee not found in this business', 404);
        }

        // Check if employee has future appointments
        $hasFutureAppointments = $employee->appointments()
            ->where('start_time', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasFutureAppointments) {
            return $this->errorResponse(
                'Cannot delete employee with future appointments',
                422
            );
        }

        $employee->delete();

        return $this->successResponse(null, 'Employee deleted successfully');
    }
}
