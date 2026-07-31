<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\V1\AppointmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    use ApiResponseTrait;

    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

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

        $appointments = $business->appointments()
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->has('employee_id'), function ($query) use ($request) {
                return $query->where('employee_id', $request->employee_id);
            })
            ->when($request->has('client_id'), function ($query) use ($request) {
                return $query->where('client_id', $request->client_id);
            })
            ->when($request->has('from_date'), function ($query) use ($request) {
                return $query->whereDate('start_time', '>=', $request->from_date);
            })
            ->when($request->has('to_date'), function ($query) use ($request) {
                return $query->whereDate('start_time', '<=', $request->to_date);
            })
            ->when($request->has('date'), function ($query) use ($request) {
                return $query->whereDate('start_time', $request->date);
            })
            ->with(['client', 'service', 'employee'])
            ->orderBy('start_time', 'asc')
            ->paginate(20);

        return $this->paginatedResponse($appointments, AppointmentResource::class);
    }

    public function store(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'employee_id' => 'required|exists:employees,id',
            'service_id' => 'required|exists:services,id',
            'start_time' => 'required|date|after:now',
            'notes' => 'nullable|string',
            'deposit_paid' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        // Verify client and employee belong to business
        $client = $business->clients()->find($request->client_id);
        $employee = $business->employees()->find($request->employee_id);
        $service = $business->services()->find($request->service_id);

        if (!$client) {
            return $this->errorResponse('Client not found in this business', 404);
        }
        if (!$employee) {
            return $this->errorResponse('Employee not found in this business', 404);
        }
        if (!$service) {
            return $this->errorResponse('Service not found in this business', 404);
        }

        $startTime = Carbon::parse($request->start_time);
        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        // Calculate price
        $price = $service->getFinalPrice();

        $appointmentData = [
            'business_id' => $business->id,
            'client_id' => $client->id,
            'employee_id' => $employee->id,
            'service_id' => $service->id,
            'created_by_user_id' => $request->user()->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'price' => $price,
            'deposit_paid' => $request->deposit_paid ?? 0,
            'notes' => $request->notes,
            'status' => 'pending',
        ];

        try {
            $appointment = $this->appointmentService->createAppointment($appointmentData);

            return $this->successResponse(
                new AppointmentResource($appointment->load(['client', 'service', 'employee'])),
                'Appointment created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function show(Request $request, Appointment $appointment)
    {
        $business = $this->getBusiness($request);

        if ($appointment->business_id !== $business->id) {
            return $this->errorResponse('Appointment not found in this business', 404);
        }

        return $this->successResponse(
            new AppointmentResource($appointment->load(['client', 'service', 'employee']))
        );
    }

    public function update(Request $request, Appointment $appointment)
    {
        $business = $this->getBusiness($request);

        if ($appointment->business_id !== $business->id) {
            return $this->errorResponse('Appointment not found in this business', 404);
        }

        $validator = Validator::make($request->all(), [
            'client_id' => 'sometimes|exists:clients,id',
            'employee_id' => 'sometimes|exists:employees,id',
            'service_id' => 'sometimes|exists:services,id',
            'start_time' => 'sometimes|date|after:now',
            'notes' => 'nullable|string',
            'deposit_paid' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        if (!$appointment->canBeConfirmed() && !$appointment->isFuture()) {
            return $this->errorResponse('Cannot update past or completed appointment', 422);
        }

        $data = $request->all();

        // Recalculate if service or time changed
        if ($request->has('service_id') || $request->has('start_time')) {
            $service = $request->has('service_id')
                ? $business->services()->find($request->service_id)
                : $appointment->service;

            if (!$service) {
                return $this->errorResponse('Service not found', 404);
            }

            $startTime = $request->has('start_time')
                ? Carbon::parse($request->start_time)
                : $appointment->start_time;

            $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

            $data['end_time'] = $endTime;
            $data['price'] = $service->getFinalPrice();
        }

        $appointment->update($data);

        return $this->successResponse(
            new AppointmentResource($appointment->load(['client', 'service', 'employee'])),
            'Appointment updated successfully'
        );
    }

    public function destroy(Request $request, Appointment $appointment)
    {
        $business = $this->getBusiness($request);

        if ($appointment->business_id !== $business->id) {
            return $this->errorResponse('Appointment not found in this business', 404);
        }

        if (!$appointment->canBeCancelled()) {
            return $this->errorResponse('Cannot delete this appointment', 422);
        }

        $appointment->delete();

        return $this->successResponse(null, 'Appointment deleted successfully');
    }

    public function confirm(Request $request, Appointment $appointment)
    {
        $business = $this->getBusiness($request);

        if ($appointment->business_id !== $business->id) {
            return $this->errorResponse('Appointment not found in this business', 404);
        }

        if (!$appointment->canBeConfirmed()) {
            return $this->errorResponse('Cannot confirm this appointment', 422);
        }

        $appointment->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        return $this->successResponse(
            new AppointmentResource($appointment->load(['client', 'service', 'employee'])),
            'Appointment confirmed successfully'
        );
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        $business = $this->getBusiness($request);

        if ($appointment->business_id !== $business->id) {
            return $this->errorResponse('Appointment not found in this business', 404);
        }

        if (!$appointment->canBeCancelled()) {
            return $this->errorResponse('Cannot cancel this appointment', 422);
        }

        try {
            $this->appointmentService->cancelAppointment(
                $appointment,
                $request->cancellation_reason
            );

            return $this->successResponse(
                new AppointmentResource($appointment->load(['client', 'service', 'employee'])),
                'Appointment cancelled successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function complete(Request $request, Appointment $appointment)
    {
        $business = $this->getBusiness($request);

        if ($appointment->business_id !== $business->id) {
            return $this->errorResponse('Appointment not found in this business', 404);
        }

        if (!$appointment->canBeCompleted()) {
            return $this->errorResponse('Cannot complete this appointment', 422);
        }

        $appointment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update client stats
        $client = $appointment->client;
        $client->increment('total_visits');
        $client->increment('total_spent', $appointment->price);
        $client->update(['last_visit_at' => now()]);

        return $this->successResponse(
            new AppointmentResource($appointment->load(['client', 'service', 'employee'])),
            'Appointment completed successfully'
        );
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $business = $this->getBusiness($request);

        if ($appointment->business_id !== $business->id) {
            return $this->errorResponse('Appointment not found in this business', 404);
        }

        $validator = Validator::make($request->all(), [
            'start_time' => 'required|date|after:now',
            'employee_id' => 'sometimes|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        if (!$appointment->canBeCancelled()) {
            return $this->errorResponse('Cannot reschedule this appointment', 422);
        }

        $employeeId = $request->employee_id ?? $appointment->employee_id;
        $startTime = Carbon::parse($request->start_time);
        $service = $appointment->service;
        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        // Check if new slot is available
        $isAvailable = $this->appointmentService->isSlotAvailable(
            $employeeId,
            $startTime,
            $endTime,
            $appointment->id
        );

        if (!$isAvailable) {
            return $this->errorResponse('Selected time slot is not available', 422);
        }

        $appointment->update([
            'start_time' => $startTime,
            'end_time' => $endTime,
            'employee_id' => $employeeId,
            'status' => 'pending',
        ]);

        return $this->successResponse(
            new AppointmentResource($appointment->load(['client', 'service', 'employee'])),
            'Appointment rescheduled successfully'
        );
    }

    public function availableSlots(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date|after_or_equal:today',
            'service_id' => 'required|exists:services,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $employee = $business->employees()->find($request->employee_id);
        if (!$employee) {
            return $this->errorResponse('Employee not found in this business', 404);
        }

        $service = $business->services()->find($request->service_id);
        if (!$service) {
            return $this->errorResponse('Service not found in this business', 404);
        }

        $slots = $this->appointmentService->getAvailableSlots(
            $employee->id,
            $request->date,
            $service->duration_minutes
        );

        return $this->successResponse([
            'date' => $request->date,
            'employee' => $employee->name,
            'service' => $service->name,
            'duration_minutes' => $service->duration_minutes,
            'available_slots' => $slots,
        ]);
    }
}
