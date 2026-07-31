<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'phone',
        'email',
        'position',
        'avatar_url',
        'commission_percent',
        'is_active',
        'settings',
        'booking_buffer_minutes',
    ];

    protected $casts = [
        'settings' => 'array',
        'commission_percent' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relations
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function workingHours()
    {
        return $this->hasMany(WorkingHour::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'employee_service')
            ->withPivot('price', 'duration_minutes')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getWorkingHoursForDay(string $dayOfWeek): ?WorkingHour
    {
        return $this->workingHours()->where('day_of_week', $dayOfWeek)->first();
    }

    public function isAvailableAt(Carbon $startTime, Carbon $endTime): bool
    {
        // Check if employee has overlapping appointments
        $overlapping = $this->appointments()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();

        return !$overlapping;
    }
}
