<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Employee;
use App\Models\WorkingHour;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkingHourController extends Controller
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

    public function batchUpdate(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'working_hours' => 'required|array',
            'working_hours.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'working_hours.*.start_time' => 'required_if:working_hours.*.is_working_day,true|date_format:H:i',
            'working_hours.*.end_time' => 'required_if:working_hours.*.is_working_day,true|date_format:H:i|after:working_hours.*.start_time',
            'working_hours.*.is_working_day' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $employee = $business->employees()->find($request->employee_id);

        if (!$employee) {
            return $this->errorResponse('Employee not found in this business', 404);
        }

        foreach ($request->working_hours as $wh) {
            WorkingHour::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'day_of_week' => $wh['day_of_week'],
                ],
                [
                    'start_time' => $wh['start_time'] ?? '09:00',
                    'end_time' => $wh['end_time'] ?? '18:00',
                    'break_start' => $wh['break_start'] ?? null,
                    'break_end' => $wh['break_end'] ?? null,
                    'is_working_day' => $wh['is_working_day'] ?? true,
                ]
            );
        }

        return $this->successResponse(null, 'Working hours updated successfully');
    }

    public function getByEmployee(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            return $this->errorResponse('Employee not found in this business', 404);
        }

        $workingHours = $employee->workingHours()
            ->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->get();

        return $this->successResponse($workingHours);
    }

    public function getBusinessWorkingHours(Request $request)
    {
        $business = $this->getBusiness($request);

        $employees = $business->employees()
            ->with(['workingHours' => function ($query) {
                $query->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')");
            }])
            ->active()
            ->get();

        return $this->successResponse($employees);
    }
}
