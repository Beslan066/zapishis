<?php


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Employee;
use App\Models\WorkingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkingHourController extends Controller
{
    protected function getBusiness(Request $request): Business
    {
        $business = $request->user()->currentBusiness;

        if (!$business) {
            abort(403, 'Please create a business first');
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
            'working_hours.*.break_start' => 'nullable|date_format:H:i',
            'working_hours.*.break_end' => 'nullable|date_format:H:i|after:working_hours.*.break_start',
            'working_hours.*.is_working_day' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee = $business->employees()->find($request->employee_id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found in this business',
            ], 404);
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

        return response()->json([
            'success' => true,
            'message' => 'Working hours updated successfully!',
        ]);
    }

    public function copyWeek(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'from_day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'to_day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee = $business->employees()->find($request->employee_id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found in this business',
            ], 404);
        }

        $source = $employee->workingHours()
            ->where('day_of_week', $request->from_day)
            ->first();

        if (!$source) {
            return response()->json([
                'success' => false,
                'message' => 'Source day working hours not found',
            ], 404);
        }

        $target = $employee->workingHours()
            ->where('day_of_week', $request->to_day)
            ->first();

        if ($target) {
            $target->update([
                'start_time' => $source->start_time,
                'end_time' => $source->end_time,
                'break_start' => $source->break_start,
                'break_end' => $source->break_end,
                'is_working_day' => $source->is_working_day,
            ]);
        } else {
            $employee->workingHours()->create([
                'day_of_week' => $request->to_day,
                'start_time' => $source->start_time,
                'end_time' => $source->end_time,
                'break_start' => $source->break_start,
                'break_end' => $source->break_end,
                'is_working_day' => $source->is_working_day,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Working hours copied successfully!',
        ]);
    }

    public function getByEmployee(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found in this business',
            ], 404);
        }

        $workingHours = $employee->workingHours()
            ->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->get();

        return response()->json([
            'success' => true,
            'data' => $workingHours,
        ]);
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

        return response()->json([
            'success' => true,
            'data' => $employees,
        ]);
    }
}
