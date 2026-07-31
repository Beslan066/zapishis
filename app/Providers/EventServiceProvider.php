<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // Add custom events
        'App\Events\AppointmentCreated' => [
            'App\Listeners\SendAppointmentConfirmation',
        ],
        'App\Events\AppointmentCancelled' => [
            'App\Listeners\SendAppointmentCancellation',
        ],
        'App\Events\AppointmentCompleted' => [
            'App\Listeners\UpdateClientStats',
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
