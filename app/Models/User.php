<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use  HasFactory, Notifiable, SoftDeletes;

    const ROLE_CLIENT = 'client';
    const ROLE_BUSINESS = 'business';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
        'current_business_id',
        'settings',
        'last_active_at',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'phone_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'settings' => 'array',
        'last_active_at' => 'datetime',
    ];

    public function isClient(): bool
    {
        return $this->role === self::ROLE_CLIENT;
    }

    public function isBusiness(): bool
    {
        return $this->role === self::ROLE_BUSINESS;
    }

    public function hasVerifiedPhone(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => now(),
        ])->save();
    }

    // Relations
    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

    public function currentBusiness()
    {
        return $this->belongsTo(Business::class, 'current_business_id');
    }

    public function createdAppointments()
    {
        return $this->hasMany(Appointment::class, 'created_by_user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'created_by_user_id');
    }

    public function clientAppointments()
    {
        return $this->hasMany(Appointment::class, 'client_id');
    }

    public function hasBusinessAccess(int $businessId): bool
    {
        return $this->businesses()->where('id', $businessId)->exists();
    }

    public function switchBusiness(int $businessId): bool
    {
        if (!$this->hasBusinessAccess($businessId)) {
            return false;
        }

        $this->update(['current_business_id' => $businessId]);
        return true;
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->whereNull('read_at');
    }


}
