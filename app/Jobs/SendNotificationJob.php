<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 600]; // 1, 5, 10 minutes

    protected Notification $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function handle(NotificationService $notificationService): void
    {
        try {
            // Mark as sent
            $this->notification->markAsSent();

            // Send based on channel
            $result = match ($this->notification->channel) {
                'sms' => $notificationService->sendSms(
                    $this->notification->recipient,
                    $this->notification->message
                ),
                'email' => $notificationService->sendEmail(
                    $this->notification->recipient,
                    $this->notification->title,
                    $this->notification->message
                ),
                'telegram' => $notificationService->sendTelegram(
                    $this->notification->recipient,
                    $this->notification->message
                ),
                'push' => $notificationService->sendPush(
                    $this->notification->user_id,
                    $this->notification->title,
                    $this->notification->message
                ),
                'system' => true, // System notifications are saved instantly
                default => false,
            };

            if ($result) {
                $this->notification->markAsDelivered();

                // Log success
                Log::info('Notification sent successfully', [
                    'notification_id' => $this->notification->id,
                    'channel' => $this->notification->channel,
                    'recipient' => $this->notification->recipient,
                ]);
            } else {
                throw new \Exception('Failed to send notification');
            }

        } catch (\Exception $e) {
            $this->notification->markAsFailed($e->getMessage());

            Log::error('Failed to send notification', [
                'notification_id' => $this->notification->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Retry if possible
            if ($this->notification->canRetry()) {
                $this->release($this->notification->next_retry_at->diffInSeconds());
            } else {
                $this->fail($e);
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Notification job failed permanently', [
            'notification_id' => $this->notification->id,
            'error' => $exception->getMessage(),
        ]);

        $this->notification->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => 'Job permanently failed: ' . $exception->getMessage(),
        ]);
    }
}
