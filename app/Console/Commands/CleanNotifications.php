<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanNotifications extends Command
{
    protected $signature = 'notifications:clean {--days=30 : Delete notifications older than X days}';
    protected $description = 'Clean old notifications';

    public function handle(): void
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $deleted = Notification::where('created_at', '<', $cutoffDate)
            ->where(function ($query) {
                $query->where('status', 'read')
                    ->orWhere('status', 'delivered')
                    ->orWhere('status', 'failed');
            })
            ->delete();

        $this->info("Deleted {$deleted} notifications older than {$days} days");
    }
}
