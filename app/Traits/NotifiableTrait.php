<?php

namespace App\Traits;

use App\Models\Notification;
use App\Models\Business;
use Illuminate\Support\Facades\DB;

trait NotifiableTrait
{
    /**
     * Create a notification
     */
    public function notify(
        string $message,
        string $type = 'system',
        string $channel = 'system',
        ?string $title = null,
        ?array $data = null,
        bool $isUrgent = false,
        bool $requiresAction = false
    ): Notification {
        $notification = new Notification([
            'business_id' => $this->getBusinessId(),
            'user_id' => $this->id ?? null,
            'type' => $type,
            'channel' => $channel,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_urgent' => $isUrgent,
            'requires_action' => $requiresAction,
            'recipient' => $this->getNotificationRecipient(),
        ]);

        $notification->save();

        // Dispatch job for external channels
        if (!in_array($channel, ['system'])) {
            dispatch(new \App\Jobs\SendNotificationJob($notification));
        }

        return $notification;
    }

    /**
     * Send SMS notification
     */
    public function notifySms(string $message, string $type = 'system', ?array $data = null): Notification
    {
        return $this->notify($message, $type, 'sms', null, $data);
    }

    /**
     * Send Email notification
     */
    public function notifyEmail(string $message, string $title, string $type = 'system', ?array $data = null): Notification
    {
        return $this->notify($message, $type, 'email', $title, $data);
    }

    /**
     * Send System notification (in-app)
     */
    public function notifySystem(string $message, string $type = 'system', ?array $data = null): Notification
    {
        return $this->notify($message, $type, 'system', null, $data);
    }

    /**
     * Get unread notifications count
     */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead(): void
    {
        $this->notifications()->unread()->update(['read_at' => now()]);
    }

    /**
     * Get business ID for notification
     */
    protected function getBusinessId(): ?int
    {
        if (property_exists($this, 'business_id') && $this->business_id) {
            return $this->business_id;
        }

        if (method_exists($this, 'getBusiness')) {
            $business = $this->getBusiness();
            return $business?->id;
        }

        if (property_exists($this, 'business') && $this->business) {
            return $this->business->id;
        }

        return null;
    }

    /**
     * Get recipient for notification
     */
    protected function getNotificationRecipient(): ?string
    {
        if (property_exists($this, 'phone') && $this->phone) {
            return $this->phone;
        }

        if (property_exists($this, 'email') && $this->email) {
            return $this->email;
        }

        return null;
    }
}
