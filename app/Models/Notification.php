<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'user_id',
        'appointment_id',
        'client_id',
        'type',
        'channel',
        'title',
        'message',
        'data',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
        'failed_at',
        'recipient',
        'provider',
        'provider_message_id',
        'provider_response',
        'error_message',
        'retry_count',
        'next_retry_at',
        'priority',
        'is_urgent',
        'requires_action',
        'metadata',
    ];

    protected $casts = [
        'data' => 'array',
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'failed_at' => 'datetime',
        'next_retry_at' => 'datetime',
        'is_urgent' => 'boolean',
        'requires_action' => 'boolean',
        'retry_count' => 'integer',
        'priority' => 'integer',
    ];

    // ============================================
    // Relations
    // ============================================

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // ============================================
    // Scopes
    // ============================================

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    public function scopeRequiresAction($query)
    {
        return $query->where('requires_action', true);
    }

    public function scopeFailedRetry($query)
    {
        return $query->where('status', 'failed')
            ->where('retry_count', '<', 3)
            ->where(function ($q) {
                $q->whereNull('next_retry_at')
                    ->orWhere('next_retry_at', '<=', now());
            });
    }

    // ============================================
    // Methods
    // ============================================

    public function markAsRead(): void
    {
        $this->update([
            'read_at' => now(),
            'status' => 'read',
        ]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
            'next_retry_at' => $this->calculateNextRetry(),
        ]);
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function isSent(): bool
    {
        return in_array($this->status, ['sent', 'delivered', 'read']);
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canRetry(): bool
    {
        return $this->isFailed()
            && $this->retry_count < 3
            && ($this->next_retry_at === null || $this->next_retry_at <= now());
    }

    protected function calculateNextRetry(): ?\DateTime
    {
        $retryDelays = [5, 15, 60]; // minutes
        if ($this->retry_count < count($retryDelays)) {
            return now()->addMinutes($retryDelays[$this->retry_count]);
        }
        return null;
    }

    // ============================================
    // Getters
    // ============================================

    public function getTypeLabelAttribute(): string
    {
        $types = [
            'appointment_confirmation' => 'Подтверждение записи',
            'appointment_reminder' => 'Напоминание о записи',
            'appointment_cancellation' => 'Отмена записи',
            'appointment_reschedule' => 'Перенос записи',
            'birthday_greeting' => 'Поздравление с днем рождения',
            'promotion' => 'Акция/Предложение',
            'system' => 'Системное уведомление',
            'payment_confirmation' => 'Подтверждение оплаты',
            'feedback_request' => 'Запрос отзыва',
        ];
        return $types[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'pending' => 'Ожидает отправки',
            'sent' => 'Отправлено',
            'delivered' => 'Доставлено',
            'read' => 'Прочитано',
            'failed' => 'Ошибка',
            'cancelled' => 'Отменено',
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getChannelLabelAttribute(): string
    {
        $channels = [
            'system' => 'Система',
            'sms' => 'SMS',
            'email' => 'Email',
            'telegram' => 'Telegram',
            'push' => 'Push-уведомление',
        ];
        return $channels[$this->channel] ?? $this->channel;
    }

    public function getShortMessageAttribute(): string
    {
        return strlen($this->message) > 100
            ? substr($this->message, 0, 100) . '...'
            : $this->message;
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
