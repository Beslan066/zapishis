<?php

namespace App\Jobs;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAppointmentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $appointment;
    protected $type;

    public function __construct(Appointment $appointment, string $type)
    {
        $this->appointment = $appointment;
        $this->type = $type;
    }

    public function handle(): void
    {
        $client = $this->appointment->client;

        if (!$client->email) {
            return;
        }

        Mail::send("emails.appointments.{$this->type}", [
            'appointment' => $this->appointment,
            'client' => $client,
        ], function ($message) use ($client) {
            $message->to($client->email)
                ->subject('Запись в салоне');
        });
    }
}
