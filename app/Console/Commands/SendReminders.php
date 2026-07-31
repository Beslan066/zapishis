<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send appointment reminders for tomorrow';

    public function handle(NotificationService $notificationService): void
    {
        $tomorrow = Carbon::tomorrow();

        $appointments = Appointment::whereDate('start_time', $tomorrow)
            ->whereIn('status', ['confirmed', 'pending'])
            ->whereNull('reminder_sent_at')
            ->get();

        $this->info("Found {$appointments->count()} appointments for tomorrow");

        foreach ($appointments as $appointment) {
            $notificationService->sendAppointmentReminder($appointment);
            $this->info("Reminder sent for appointment #{$appointment->id}");
        }
    }
}
