<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Jobs\SendNotificationJob;
use Illuminate\Console\Command;

class RetryFailedNotifications extends Command
{
    protected $signature = 'notifications:retry-failed';
    protected $description = 'Retry failed notifications';

    public function handle(): void
    {
        $failedNotifications = Notification::failedRetry()->get();

        $this->info("Found {$failedNotifications->count()} failed notifications to retry");

        foreach ($failedNotifications as $notification) {
            $this->line("Retrying notification #{$notification->id}...");

            dispatch(new SendNotificationJob($notification));

            $this->info("Notification #{$notification->id} dispatched for retry");
        }

        $this->info('All retry jobs dispatched successfully');
    }
}
