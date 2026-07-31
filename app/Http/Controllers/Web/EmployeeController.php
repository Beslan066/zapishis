<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Employee;
use App\Models\WorkingHour;
use Illuminate\Http\Request;

class EmployeeController extends Controller
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

        $employees = $business->employees()
            ->when($request->has('is_active'), function ($query) use ($request) {
                return $query->where('is_active', $request->boolean('is_active'));
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->with(['workingHours', 'services'])
            ->withCount(['appointments' => function ($query) {
                $query->where('start_time', '>=', now())
                    ->whereIn('status', ['pending', 'confirmed']);
            }])
            ->orderBy('name')
            ->paginate(20);

        return view('employees.index', compact('employees'));
    }

    public function create(Request $request)
    {
        $business = $this->getBusiness($request);
        $services = $business->services()->active()->orderBy('name')->get();

        $daysOfWeek = [
            'monday' => 'Понедельник',
            'tuesday' => 'Вторник',
            'wednesday' => 'Среда',
            'thursday' => 'Четверг',
            'friday' => 'Пятница',
            'saturday' => 'Суббота',
            'sunday' => 'Воскресенье',
        ];

        return view('employees.create', compact('services', 'daysOfWeek'));
    }

    public function store(Request $request)
    {
        $business = $this->getBusiness($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'position' => 'nullable|string|max:100',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'booking_buffer_minutes' => 'nullable|integer|min:0',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
            'working_hours' => 'nullable|array',
        ]);

        $employee = $business->employees()->create($validated);

        // Attach services
        if ($request->has('service_ids')) {
            $employee->services()->attach($request->service_ids);
        }

        // Create working hours
        if ($request->has('working_hours')) {
            foreach ($request->working_hours as $day => $wh) {
                if (isset($wh['is_working_day']) && $wh['is_working_day']) {
                    $employee->workingHours()->create([
                        'day_of_week' => $day,
                        'start_time' => $wh['start_time'] ?? '09:00',
                        'end_time' => $wh['end_time'] ?? '18:00',
                        'break_start' => $wh['break_start'] ?? null,
                        'break_end' => $wh['break_end'] ?? null,
                        'is_working_day' => true,
                    ]);
                } else {
                    $employee->workingHours()->create([
                        'day_of_week' => $day,
                        'start_time' => '09:00',
                        'end_time' => '18:00',
                        'is_working_day' => false,
                    ]);
                }
            }
        }

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully!');
    }

    public function show(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        $employee->load(['workingHours', 'services']);

        $upcomingAppointments = $employee->appointments()
            ->where('start_time', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['client', 'service'])
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        return view('employees.show', compact('employee', 'upcomingAppointments'));
    }

    public function edit(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        $services = $business->services()->active()->orderBy('name')->get();
        $employee->load(['workingHours', 'services']);

        $daysOfWeek = [
            'monday' => 'Понедельник',
            'tuesday' => 'Вторник',
            'wednesday' => 'Среда',
            'thursday' => 'Четверг',
            'friday' => 'Пятница',
            'saturday' => 'Суббота',
            'sunday' => 'Воскресенье',
        ];

        return view('employees.edit', compact('employee', 'services', 'daysOfWeek'));
    }

    public function update(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'position' => 'nullable|string|max:100',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'booking_buffer_minutes' => 'nullable|integer|min:0',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
        ]);

        $employee->update($validated);

        // Sync services
        if ($request->has('service_ids')) {
            $employee->services()->sync($request->service_ids);
        }

        // Update working hours
        if ($request->has('working_hours')) {
            foreach ($request->working_hours as $day => $wh) {
                $workingHour = $employee->workingHours()->where('day_of_week', $day)->first();

                if ($workingHour) {
                    if (isset($wh['is_working_day']) && $wh['is_working_day']) {
                        $workingHour->update([
                            'start_time' => $wh['start_time'] ?? '09:00',
                            'end_time' => $wh['end_time'] ?? '18:00',
                            'break_start' => $wh['break_start'] ?? null,
                            'break_end' => $wh['break_end'] ?? null,
                            'is_working_day' => true,
                        ]);
                    } else {
                        $workingHour->update(['is_working_day' => false]);
                    }
                } else {
                    if (isset($wh['is_working_day']) && $wh['is_working_day']) {
                        $employee->workingHours()->create([
                            'day_of_week' => $day,
                            'start_time' => $wh['start_time'] ?? '09:00',
                            'end_time' => $wh['end_time'] ?? '18:00',
                            'break_start' => $wh['break_start'] ?? null,
                            'break_end' => $wh['break_end'] ?? null,
                            'is_working_day' => true,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully!');
    }

    public function destroy(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        // Check if employee has future appointments
        $hasFutureAppointments = $employee->appointments()
            ->where('start_time', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasFutureAppointments) {
            return redirect()->route('employees.index')
                ->with('error', 'Cannot delete employee with future appointments!');
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully!');
    }

    public function toggleActive(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        $employee->update(['is_active' => !$employee->is_active]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee status updated!');
    }

    public function schedule(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        $employee->load('workingHours');
        $daysOfWeek = [
            'monday' => 'Понедельник',
            'tuesday' => 'Вторник',
            'wednesday' => 'Среда',
            'thursday' => 'Четверг',
            'friday' => 'Пятница',
            'saturday' => 'Суббота',
            'sunday' => 'Воскресенье',
        ];

        return view('employees.schedule', compact('employee', 'daysOfWeek'));
    }

    public function updateSchedule(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        $request->validate([
            'working_hours' => 'required|array',
            'working_hours.*.start_time' => 'required|date_format:H:i',
            'working_hours.*.end_time' => 'required|date_format:H:i|after:working_hours.*.start_time',
            'working_hours.*.is_working_day' => 'boolean',
        ]);

        foreach ($request->working_hours as $day => $wh) {
            WorkingHour::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'day_of_week' => $day,
                ],
                [
                    'start_time' => $wh['start_time'] ?? '09:00',
                    'end_time' => $wh['end_time'] ?? '18:00',
                    'break_start' => $wh['break_start'] ?? null,
                    'break_end' => $wh['break_end'] ?? null,
                    'is_working_day' => $wh['is_working_day'] ?? false,
                ]
            );
        }

        return redirect()->route('employees.schedule', $employee)
            ->with('success', 'Schedule updated successfully!');
    }

    public function getServices(Request $request, Employee $employee)
    {
        $business = $this->getBusiness($request);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        return response()->json([
            'services' => $employee->services()->select('id', 'name', 'price', 'duration_minutes')->get(),
        ]);
    }
}
