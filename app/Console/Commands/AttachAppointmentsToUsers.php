<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\User;
use Illuminate\Console\Command;

class AttachAppointmentsToUsers extends Command
{
    protected $signature = 'appointments:attach-users';
    protected $description = 'Attach appointments to users by phone';

    public function handle()
    {
        $this->info('Attaching appointments to users...');

        // Находим все записи с client_id
        $appointments = Appointment::whereNotNull('client_id')->get();

        foreach ($appointments as $appointment) {
            $client = Client::find($appointment->client_id);

            if (!$client) continue;

            $user = User::where('phone', $client->phone)->first();

            if ($user) {
                $this->line("User found: {$user->phone}, appointment: {$appointment->id}");

                // Создаем клиента в бизнесе если его нет
                $businessClient = Client::firstOrCreate(
                    [
                        'phone' => $client->phone,
                        'business_id' => $appointment->business_id,
                    ],
                    [
                        'first_name' => $user->name,
                        'email' => $user->email,
                    ]
                );

                if ($businessClient->id != $appointment->client_id) {
                    $this->line("Updating appointment {$appointment->id} client_id from {$appointment->client_id} to {$businessClient->id}");

                    $appointment->update([
                        'client_id' => $businessClient->id,
                    ]);
                }
            }
        }

        $this->info('Done!');
    }
}
