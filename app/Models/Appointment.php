<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'client_id',
        'employee_id',
        'service_id',
        'created_by_user_id',
        'start_time',
        'end_time',
        'price',
        'discount_applied',
        'deposit_paid',
        'status',
        'cancellation_reason',
        'notes',
        'metadata',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'reminder_sent_at',
        'client_phone',
        'client_name',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price' => 'decimal:2',
        'discount_applied' => 'decimal:2',
        'deposit_paid' => 'decimal:2',
        'metadata' => 'array',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    protected $with = ['client', 'service', 'employee'];

    // Relations
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    public function scopeForTomorrow($query)
    {
        return $query->whereDate('start_time', today()->addDay());
    }

    public function scopeInDateRange($query, $from, $to)
    {
        return $query->whereBetween('start_time', [$from, $to]);
    }

    // Methods
    public function isPast(): bool
    {
        return $this->end_time->isPast();
    }

    public function isFuture(): bool
    {
        return $this->start_time->isFuture();
    }

    public function isInProgress(): bool
    {
        return now()->between($this->start_time, $this->end_time);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'])
            && $this->start_time->isFuture();
    }

    public function canBeConfirmed(): bool
    {
        return $this->status === 'pending'
            && $this->start_time->isFuture();
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'confirmed'
            && $this->start_time->isPast();
    }

    public function getDurationInMinutes(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending' => 'Ожидает',
            'confirmed' => 'Подтверждена',
            'in_progress' => 'В процессе',
            'completed' => 'Завершена',
            'cancelled' => 'Отменена',
            'no_show' => 'Не явился',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'pending' => 'yellow',
            'confirmed' => 'green',
            'in_progress' => 'blue',
            'completed' => 'gray',
            'cancelled' => 'red',
            'no_show' => 'red',
        ][$this->status] ?? 'gray';
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Генерация номера заявки
    public static function generateBookingNumber(): string
    {
        return 'BK-' . date('Ymd') . '-' . strtoupper(uniqid());
    }


}
