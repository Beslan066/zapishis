<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AppointmentService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getAvailableSlots(
        int $employeeId,
        string $date,
        int $serviceDuration,
        ?int $excludeAppointmentId = null
    ): Collection {
        $employee = Employee::findOrFail($employeeId);

        $dayOfWeek = Carbon::parse($date)->format('l');
        $workingHour = $employee->workingHours()->where('day_of_week', $dayOfWeek)->first();

        if (!$workingHour || !$workingHour->is_working_day) {
            return collect([]);
        }

        $start = Carbon::parse($date . ' ' . $workingHour->start_time);
        $end = Carbon::parse($date . ' ' . $workingHour->end_time);

        $appointments = Appointment::where('employee_id', $employeeId)
            ->whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->when($excludeAppointmentId, function ($query, $id) {
                return $query->where('id', '!=', $id);
            })
            ->orderBy('start_time')
            ->get();

        $slots = collect();
        $current = $start->copy();

        while ($current->addMinutes($serviceDuration)->lte($end)) {
            $slotEnd = $current->copy()->addMinutes($serviceDuration);
            $isAvailable = true;

            foreach ($appointments as $appointment) {
                $appointmentStart = Carbon::parse($appointment->start_time);
                $appointmentEnd = Carbon::parse($appointment->end_time);

                if ($current->lt($appointmentEnd) && $slotEnd->gt($appointmentStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $slots->push([
                    'start_time' => $current->format('H:i'),
                    'end_time' => $slotEnd->format('H:i'),
                    'is_available' => true,
                ]);
            }

            $current->addMinutes(15);
        }

        return $slots;
    }

    public function createAppointment(array $data): Appointment
    {
        $slots = $this->getAvailableSlots(
            $data['employee_id'] ?? null,
            Carbon::parse($data['start_time'])->format('Y-m-d'),
            $data['duration_minutes'] ?? 60,
        );

        $selectedSlot = $slots->first(function ($slot) use ($data) {
            return $slot['start_time'] === Carbon::parse($data['start_time'])->format('H:i');
        });

        if (!$selectedSlot) {
            throw new \Exception('Selected time slot is not available');
        }

        $appointment = Appointment::create($data);

        // Отправляем уведомление
        $this->notificationService->sendAppointmentCreated($appointment);

        return $appointment;
    }

    public function cancelAppointment(Appointment $appointment, ?string $reason = null): void
    {
        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        $this->notificationService->sendAppointmentCancelled($appointment);
    }

    public function getUpcomingAppointments(int $businessId, int $days = 7): Collection
    {
        return Appointment::where('business_id', $businessId)
            ->where('start_time', '>=', now())
            ->where('start_time', '<=', now()->addDays($days))
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['client', 'service', 'employee'])
            ->orderBy('start_time')
            ->get();
    }

    public function sendReminder(Appointment $appointment): void
    {
        if ($appointment->reminder_sent_at) {
            return;
        }

        $this->notificationService->sendAppointmentReminder($appointment);
        $appointment->update(['reminder_sent_at' => now()]);
    }

    public function isSlotAvailable(int $employeeId, Carbon $startTime, Carbon $endTime, ?int $excludeAppointmentId = null): bool
    {
        $overlapping = Appointment::where('employee_id', $employeeId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->when($excludeAppointmentId, function ($query, $id) {
                return $query->where('id', '!=', $id);
            })
            ->exists();

        return !$overlapping;
    }
}
