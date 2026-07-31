<?php
namespace App\Services;

use App\Models\Appointment;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AppointmentService
{
    public function getAvailableSlots(
        int $employeeId,
        string $date,
        int $serviceDuration,
        ?int $excludeAppointmentId = null
    ): Collection {
        $employee = Employee::findOrFail($employeeId);

        // Get working hours for this day
        $dayOfWeek = Carbon::parse($date)->format('l');
        $workingHour = $employee->workingHours()->where('day_of_week', $dayOfWeek)->first();

        if (!$workingHour || !$workingHour->is_working_day) {
            return collect([]);
        }

        $start = Carbon::parse($date . ' ' . $workingHour->start_time);
        $end = Carbon::parse($date . ' ' . $workingHour->end_time);

        // Get existing appointments for this day
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

            $current->addMinutes(15); // Step by 15 minutes
        }

        return $slots;
    }

    public function createAppointment(array $data): Appointment
    {
        // Validate slot is available
        $slots = $this->getAvailableSlots(
            $data['employee_id'],
            Carbon::parse($data['start_time'])->format('Y-m-d'),
            $data['duration_minutes'],
        );

        $selectedSlot = $slots->first(function ($slot) use ($data) {
            return $slot['start_time'] === Carbon::parse($data['start_time'])->format('H:i');
        });

        if (!$selectedSlot) {
            throw new \Exception('Selected time slot is not available');
        }

        $appointment = Appointment::create($data);

        $client->notify(
            "Ваша запись подтверждена!\nУслуга: {$service->name}\nДата: {$startTime->format('d.m.Y H:i')}\nМастер: {$employee->name}",
            'appointment_confirmation',
            'sms',
            'Подтверждение записи',
            [
                'appointment_id' => $appointment->id,
                'service' => $service->name,
                'date' => $startTime->format('d.m.Y H:i'),
                'employee' => $employee->name,
            ]
        );

        // Send notifications
        app(NotificationService::class)->sendAppointmentConfirmation($appointment);



        return $appointment;
    }

    public function cancelAppointment(Appointment $appointment, ?string $reason = null): void
    {
        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        $appointment->client->notify(
            "Запись отменена: {$appointment->service->name} на {$appointment->start_time->format('d.m.Y H:i')}",
            'appointment_cancellation',
            'sms',
            'Отмена записи',
            [
                'appointment_id' => $appointment->id,
                'reason' => $reason,
            ]
        );

        app(NotificationService::class)->sendAppointmentCancellation($appointment);
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

        $appointment->client->notify(
            "Напоминание о записи!\nУслуга: {$appointment->service->name}\nЗавтра в {$appointment->start_time->format('H:i')}",
            'appointment_reminder',
            'sms',
            'Напоминание о записи',
            [
                'appointment_id' => $appointment->id,
            ]
        );

        $appointment->update(['reminder_sent_at' => now()]);
    }
}
