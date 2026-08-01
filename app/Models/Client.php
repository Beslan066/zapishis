<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'birthday',
        'instagram',
        'notes',
        'metadata',
        'total_visits',
        'total_spent',
        'last_visit_at',
        'user_id'
    ];

    protected $casts = [
        'birthday' => 'date',
        'metadata' => 'array',
        'total_spent' => 'decimal:2',
        'total_visits' => 'integer',
        'last_visit_at' => 'datetime',
    ];

    // Relations
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function scopeWithUpcomingAppointments($query)
    {
        return $query->with(['appointments' => function ($q) {
            $q->where('start_time', '>=', now())
                ->whereIn('status', ['pending', 'confirmed'])
                ->orderBy('start_time');
        }]);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . ($this->last_name ? substr($this->last_name, 0, 1) : ''));
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
